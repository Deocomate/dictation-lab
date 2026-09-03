<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class FormatHelper
{
    public static function money(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return number_format($value, 0, ',', '.') . 'đ';
    }

    public static function dateTime($dateTime, string $format = 'd/m/Y H:i'): string
    {
        if (empty($dateTime)) {
            return '-';
        }

        return Carbon::parse($dateTime)->format($format);
    }
}
