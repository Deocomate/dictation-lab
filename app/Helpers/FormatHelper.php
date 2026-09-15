<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class FormatHelper
{
    public static function money(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return number_format($value, 0, ',', '.').'đ';
    }

    public static function dateTime($dateTime, string $format = 'd/m/Y H:i'): string
    {
        if (empty($dateTime)) {
            return '-';
        }

        return Carbon::parse($dateTime)->format($format);
    }

    public static function fileSize(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = (int) floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
