<?php

namespace App\Services;

final class ArticleContentService
{
    /**
     * @param  array<int, array<string, mixed>>  $raw
     * @return list<array{type: string, level?: int, text?: string, en?: string, vi?: string}>
     */
    public function normalizeBlocks(array $raw): array
    {
        $normalized = [];

        foreach ($this->upgradeLegacy($raw) as $block) {
            $type = $block['type'] ?? null;

            if ($type === 'heading') {
                $level = (int) ($block['level'] ?? 1);
                $text = trim((string) ($block['text'] ?? ''));

                if (! in_array($level, [1, 2, 3], true) || $text === '') {
                    continue;
                }

                $normalized[] = ['type' => 'heading', 'level' => $level, 'text' => $text];

                continue;
            }

            if ($type === 'sentence') {
                $en = trim((string) ($block['en'] ?? ''));
                $vi = trim((string) ($block['vi'] ?? ''));

                if ($en === '' || $vi === '') {
                    continue;
                }

                $normalized[] = ['type' => 'sentence', 'en' => $en, 'vi' => $vi];
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int, array<string, mixed>>  $raw
     * @return list<array{type: string, level?: int, text?: string, en?: string, vi?: string}>
     */
    public function upgradeLegacy(array $raw): array
    {
        $result = [];

        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (! isset($item['type']) && array_key_exists('en', $item)) {
                $result[] = [
                    'type' => 'sentence',
                    'en' => (string) ($item['en'] ?? ''),
                    'vi' => (string) ($item['vi'] ?? ''),
                ];

                continue;
            }

            if (isset($item['type'])) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @return list<array{en: string, vi: string}>
     */
    public function extractSentences(array $blocks): array
    {
        $sentences = [];

        foreach ($this->upgradeLegacy($blocks) as $block) {
            if (($block['type'] ?? '') !== 'sentence') {
                continue;
            }

            $en = trim((string) ($block['en'] ?? ''));
            $vi = trim((string) ($block['vi'] ?? ''));

            if ($en === '') {
                continue;
            }

            $sentences[] = ['en' => $en, 'vi' => $vi];
        }

        return $sentences;
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     */
    public function validateHasSentence(array $blocks): bool
    {
        return $this->extractSentences($blocks) !== [];
    }
}
