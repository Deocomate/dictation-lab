<?php

namespace App\Services\Client;

use App\Models\User;
use App\Models\UserVocabulary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RuntimeException;

class VocabularyService
{
    private const FREE_VOCAB_LIMIT = 50;

    public function getVocabularies(User $user, array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $user->userVocabularies()->with('article:id,title')->latest();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('word', 'like', "%{$search}%")
                    ->orWhere('meaning', 'like', "%{$search}%")
                    ->orWhere('sentence_en', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function saveVocabulary(User $user, array $data): UserVocabulary
    {
        $word = trim($data['word']);

        if ($word === '') {
            throw new RuntimeException('Từ vựng không hợp lệ.');
        }

        $existing = UserVocabulary::where('user_id', $user->id)->where('word', $word)->first();

        if (! $existing && ! $user->isPro() && $this->getCount($user) >= self::FREE_VOCAB_LIMIT) {
            throw new RuntimeException('Bạn đã đạt giới hạn 50 từ của gói Free. Nâng cấp Pro để lưu không giới hạn.');
        }

        $meaning = trim((string) ($data['meaning'] ?? ''));

        $vocabulary = UserVocabulary::firstOrCreate(
            [
                'user_id' => $user->id,
                'word' => $word,
            ],
            [
                'article_id' => $data['article_id'] ?? null,
                'meaning' => $meaning !== '' ? $meaning : null,
                'sentence_en' => $data['sentence_en'] ?? null,
                'sentence_vi' => $data['sentence_vi'] ?? null,
            ],
        );

        if (! $vocabulary->wasRecentlyCreated) {
            $updates = [];

            if ($meaning !== '' && ($vocabulary->meaning === null || $vocabulary->meaning === '')) {
                $updates['meaning'] = $meaning;
            }

            if (! $vocabulary->article_id && ! empty($data['article_id'])) {
                $updates['article_id'] = $data['article_id'];
            }

            if (! $vocabulary->sentence_en && ! empty($data['sentence_en'])) {
                $updates['sentence_en'] = $data['sentence_en'];
            }

            if (! $vocabulary->sentence_vi && ! empty($data['sentence_vi'])) {
                $updates['sentence_vi'] = $data['sentence_vi'];
            }

            if (! empty($updates)) {
                $vocabulary->fill($updates);
                $vocabulary->save();
            }
        }

        return $vocabulary;
    }

    public function updateMeaning(User $user, int $vocabularyId, string $meaning): UserVocabulary
    {
        $vocab = UserVocabulary::where('user_id', $user->id)->findOrFail($vocabularyId);
        $vocab->update(['meaning' => trim($meaning)]);

        return $vocab->fresh();
    }

    public function deleteVocabulary(User $user, int $vocabularyId): void
    {
        $vocab = UserVocabulary::where('user_id', $user->id)->findOrFail($vocabularyId);
        $vocab->delete();
    }

    public function getCount(User $user): int
    {
        return $user->userVocabularies()->count();
    }
}
