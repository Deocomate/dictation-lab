<?php

namespace App\Services;

use App\Models\DictationHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClientUserService
{
    public function getClients(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->clients();

        if (! empty($filters['search'])) {
            $query->where(function ($builder) use ($filters): void {
                $builder->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['subscription_tier'])) {
            $query->where('subscription_tier', $filters['subscription_tier']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function getClientDetail(User $client, array $filters = []): array
    {
        abort_unless($client->role === 'user', 404);

        $filters = $this->normalizeFilters($filters);
        $periodStart = now()->subDays(max(0, $filters['period_days'] - 1))->startOfDay();

        $client = $client->load([
            'transactions' => fn ($q) => $q->with('plan')->latest()->limit(10),
        ]);

        $dictationHistories = $client->dictationHistories()
            ->with(['article.categories'])
            ->where('completed_at', '>=', $periodStart)
            ->latest('completed_at')
            ->get();

        $attempts = $this->buildAttempts($dictationHistories);

        return [
            'client' => $client,
            'filters' => $filters,
            'summary' => $this->buildSummary($dictationHistories),
            'charts' => $this->buildCharts($dictationHistories, $filters['period_days']),
            'attempts' => $this->paginateAttempts($attempts, $filters['per_page'], $filters['page']),
        ];
    }

    public function updateStatus(User $client, string $status): User
    {
        abort_unless($client->role === 'user', 404);

        $client->update(['status' => $status]);

        return $client->fresh();
    }

    public function updateSubscription(User $client, string $subscriptionTier, ?string $subscriptionExpiresAt): User
    {
        abort_unless($client->role === 'user', 404);

        $client->update([
            'subscription_tier' => $subscriptionTier,
            'subscription_expires_at' => $subscriptionTier === 'pro' && $subscriptionExpiresAt
                ? Carbon::parse($subscriptionExpiresAt)->endOfDay()
                : null,
        ]);

        return $client->fresh();
    }

    private function normalizeFilters(array $filters): array
    {
        $periodDays = (int) ($filters['period_days'] ?? 30);
        $perPage = (int) ($filters['per_page'] ?? 15);
        $page = (int) ($filters['page'] ?? request()->integer('page', 1));

        return [
            'period_days' => in_array($periodDays, [7, 30, 90, 180, 365], true) ? $periodDays : 30,
            'per_page' => in_array($perPage, [10, 15, 20, 50], true) ? $perPage : 15,
            'page' => max(1, $page),
        ];
    }

    private function buildSummary(Collection $dictationHistories): array
    {
        $latestActivityAt = $dictationHistories->max('completed_at');
        $latestActivityAt = $latestActivityAt ? Carbon::parse($latestActivityAt) : null;

        return [
            'total_dictations' => $dictationHistories->count(),
            'avg_wpm' => $this->averageOrNull($dictationHistories, 'wpm', 0),
            'avg_accuracy' => $this->averageOrNull($dictationHistories, 'accuracy', 2),
            'latest_activity_at' => $latestActivityAt,
        ];
    }

    private function buildCharts(Collection $dictationHistories, int $periodDays): array
    {
        $dictationTrend = $dictationHistories
            ->sortByDesc('completed_at')
            ->take(12)
            ->sortBy('completed_at')
            ->values();

        $volumeWindowDays = min(30, max(7, $periodDays));
        $volumeLabels = [];
        $dictationVolumes = [];

        $dictationByDate = $dictationHistories->groupBy(
            fn (DictationHistory $history) => optional($history->completed_at)->format('Y-m-d')
        );

        for ($index = $volumeWindowDays - 1; $index >= 0; $index--) {
            $date = now()->subDays($index);
            $dateKey = $date->format('Y-m-d');

            $volumeLabels[] = $date->format('d/m');
            $dictationVolumes[] = $dictationByDate->get($dateKey, collect())->count();
        }

        return [
            'dictation_trend' => [
                'labels' => $dictationTrend->map(fn (DictationHistory $h) => optional($h->completed_at)->format('d/m'))->all(),
                'datasets' => [
                    'wpm' => $dictationTrend->map(fn (DictationHistory $h) => (int) $h->wpm)->all(),
                    'accuracy' => $dictationTrend->map(fn (DictationHistory $h) => $this->roundMaybe($h->accuracy, 2))->all(),
                ],
            ],
            'attempt_volume' => [
                'labels' => $volumeLabels,
                'dictation' => $dictationVolumes,
                'window_days' => $volumeWindowDays,
            ],
        ];
    }

    private function buildAttempts(Collection $dictationHistories): Collection
    {
        return $dictationHistories->map(function (DictationHistory $history): array {
            $doneAt = $history->completed_at;
            $durationSeconds = $this->estimateDictationDurationSeconds($history);

            return [
                'attempt_id' => $history->id,
                'article_title' => $history->article?->title ?? 'N/A',
                'category_name' => $history->article?->categories->pluck('name')->join(', '),
                'done_at' => $doneAt,
                'done_at_label' => $doneAt ? $doneAt->format('d/m/Y H:i') : '-',
                'done_timestamp' => $doneAt?->timestamp ?? 0,
                'duration_seconds' => $durationSeconds,
                'duration_label' => $this->formatDuration($durationSeconds),
                'wpm' => (int) $history->wpm,
                'accuracy' => $this->roundMaybe($history->accuracy, 2),
            ];
        })->sortByDesc('done_timestamp')->values();
    }

    private function paginateAttempts(Collection $attempts, int $perPage, int $page): LengthAwarePaginator
    {
        $total = $attempts->count();
        $items = $attempts->forPage($page, $perPage)->values();

        return new PaginationLengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }

    private function estimateDictationDurationSeconds(DictationHistory $history): ?int
    {
        $content = $history->article?->content_json;
        $text = is_array($content) ? implode(' ', array_column($content, 'en')) : '';
        $wordCount = $text ? str_word_count(strip_tags($text)) : 0;
        $wpm = (int) ($history->wpm ?? 0);

        if ($wordCount <= 0 || $wpm <= 0) {
            return null;
        }

        return (int) round(($wordCount / $wpm) * 60);
    }

    private function formatDuration(?int $seconds): ?string
    {
        if ($seconds === null || $seconds <= 0) {
            return null;
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm %02ds', $hours, $minutes, $remainingSeconds);
        }

        if ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $remainingSeconds);
        }

        return sprintf('%ds', $remainingSeconds);
    }

    private function averageOrNull(Collection $items, string $key, int $precision = 1): ?float
    {
        if ($items->isEmpty()) {
            return null;
        }

        return $this->roundMaybe($items->avg($key), $precision);
    }

    private function roundMaybe(float|int|string|null $value, int $precision = 1): ?float
    {
        if ($value === null || ! is_numeric($value)) {
            return null;
        }

        return round((float) $value, $precision);
    }
}
