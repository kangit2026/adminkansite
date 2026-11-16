<?php

namespace App\Support;

class Plural
{
    /** «1 вакансія», «2 вакансії», «5 вакансій». */
    public static function uk(int $n, string $one, string $few, string $many): string
    {
        $d = $n % 10;
        $h = $n % 100;
        $word = $d === 1 && $h !== 11 ? $one : ($d >= 2 && $d <= 4 && ($h < 12 || $h > 14) ? $few : $many);

        return $n.' '.$word;
    }
}
