<?php

namespace App\Services;

use App\Models\Article;
use App\Models\DictationHistory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getSummary(): array
    {
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'total_users' => User::clients()->count(),
            'pro_users' => User::clients()->where('subscription_tier', 'pro')->count(),
            'monthly_revenue' => Transaction::query()
                ->where('status', 'success')
                ->where('created_at', '>=', $monthStart)
                ->sum('amount'),
            'total_articles' => Article::query()->count(),
            'new_users_week' => User::clients()->where('created_at', '>=', now()->subDays(7))->count(),
            'new_pro_week' => User::clients()
                ->where('subscription_tier', 'pro')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
    }

    public function getRecentUsers(int $limit = 5): Collection
    {
        return User::clients()->latest()->limit($limit)->get();
    }

    public function getRecentTransactions(int $limit = 5): Collection
    {
        return Transaction::query()
            ->with(['user', 'plan'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getCharts(): array
    {
        return [
            'users_growth' => $this->buildUsersGrowthChart(),
            'revenue_trend' => $this->buildRevenueTrendChart(),
            'learning_activity' => $this->buildLearningActivityChart(),
            'transaction_status' => $this->buildTransactionStatusChart(),
        ];
    }

    private function buildUsersGrowthChart(int $days = 14): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        $users = User::clients()
            ->where('created_at', '>=', $startDate)
            ->get(['subscription_tier', 'created_at']);

        $usersByDate = $users->groupBy(fn (User $user) => $user->created_at?->format('Y-m-d'));
        $proUsersByDate = $users
            ->where('subscription_tier', 'pro')
            ->groupBy(fn (User $user) => $user->created_at?->format('Y-m-d'));

        $labels = [];
        $totalUsers = [];
        $proUsers = [];

        for ($index = $days - 1; $index >= 0; $index--) {
            $date = now()->subDays($index);
            $dateKey = $date->format('Y-m-d');

            $labels[] = $date->format('d/m');
            $totalUsers[] = $usersByDate->get($dateKey)?->count() ?? 0;
            $proUsers[] = $proUsersByDate->get($dateKey)?->count() ?? 0;
        }

        return [
            'labels' => $labels,
            'total' => $totalUsers,
            'pro' => $proUsers,
            'window_days' => $days,
        ];
    }

    private function buildRevenueTrendChart(int $months = 6): array
    {
        $startMonth = now()->startOfMonth()->subMonths($months - 1);
        $transactions = Transaction::query()
            ->where('status', 'success')
            ->where('created_at', '>=', $startMonth)
            ->get(['amount', 'created_at']);

        $revenueByMonth = $transactions->groupBy(
            fn (Transaction $transaction) => $transaction->created_at?->format('Y-m'),
        );

        $labels = [];
        $revenue = [];

        for ($index = $months - 1; $index >= 0; $index--) {
            $month = now()->subMonths($index)->startOfMonth();
            $monthKey = $month->format('Y-m');

            $labels[] = $month->format('m/Y');
            $revenue[] = round((float) ($revenueByMonth->get($monthKey)?->sum('amount') ?? 0), 2);
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'window_months' => $months,
        ];
    }

    private function buildLearningActivityChart(int $days = 14): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();

        $dictationHistories = DictationHistory::query()
            ->where('completed_at', '>=', $startDate)
            ->get(['completed_at']);

        $dictationsByDate = $dictationHistories->groupBy(
            fn (DictationHistory $history) => $history->completed_at?->format('Y-m-d'),
        );

        $labels = [];
        $dictationCounts = [];

        for ($index = $days - 1; $index >= 0; $index--) {
            $date = now()->subDays($index);
            $dateKey = $date->format('Y-m-d');

            $labels[] = $date->format('d/m');
            $dictationCounts[] = $dictationsByDate->get($dateKey)?->count() ?? 0;
        }

        return [
            'labels' => $labels,
            'dictation' => $dictationCounts,
            'window_days' => $days,
        ];
    }

    private function buildTransactionStatusChart(int $days = 30): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        $statusCounts = Transaction::query()
            ->where('created_at', '>=', $startDate)
            ->get(['status'])
            ->countBy('status');

        return [
            'labels' => ['Thành công', 'Chờ xác nhận', 'Thất bại'],
            'values' => [
                (int) ($statusCounts->get('success') ?? 0),
                (int) ($statusCounts->get('pending') ?? 0),
                (int) ($statusCounts->get('failed') ?? 0),
            ],
            'window_days' => $days,
        ];
    }
}
