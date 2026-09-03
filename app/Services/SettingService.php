<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Validation\ValidationException;

class SettingService
{
    public const DEMO_VIDEO_URL_KEY = 'demo_video_url';

    public function get(string $key, ?string $default = null): ?string
    {
        return Setting::query()->where('key', $key)->value('value') ?? $default;
    }

    public function set(string $key, ?string $value): Setting
    {
        return Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function getDemoVideoUrl(): ?string
    {
        return $this->get(self::DEMO_VIDEO_URL_KEY);
    }

    public function updateDemoVideoUrl(?string $url): Setting
    {
        $normalizedUrl = $url ? $this->normalizeYoutubeEmbedUrl($url) : null;

        return $this->set(self::DEMO_VIDEO_URL_KEY, $normalizedUrl);
    }

    public function normalizeYoutubeEmbedUrl(string $url): string
    {
        $videoId = $this->extractYoutubeVideoId($url);

        if ($videoId === null) {
            throw ValidationException::withMessages([
                'demo_video_url' => 'URL YouTube không hợp lệ. Vui lòng dùng link youtube.com hoặc youtu.be.',
            ]);
        }

        return 'https://www.youtube.com/embed/'.$videoId;
    }

    public function extractYoutubeVideoId(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url) === 1) {
            return $url;
        }

        $patterns = [
            '~(?:youtube\.com|m\.youtube\.com)/(?:watch\?[^#]*v=|embed/|shorts/|live/)([A-Za-z0-9_-]{11})~i',
            '~youtu\.be/([A-Za-z0-9_-]{11})~i',
            '~youtube\.com/watch\?.*?[&?]v=([A-Za-z0-9_-]{11})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        $query = parse_url($url, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $parameters);
            $candidate = $parameters['v'] ?? null;
            if (is_string($candidate) && preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) === 1) {
                return $candidate;
            }
        }

        return null;
    }
}
