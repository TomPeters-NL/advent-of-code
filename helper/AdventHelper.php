<?php

namespace AdventOfCode\Helper;

class AdventHelper
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function printSolutions(string|int $alpha, string|int $beta): void
    {
        $endTime = microtime(true);
        $duration = $endTime - $this->startTime;
        $milliseconds = number_format($duration * 1000, 2);
        $time = "| Time: $milliseconds ms";

        $solutionAlpha = "| Solution #1: $alpha";
        $solutionBeta = "| Solution #2: $beta";

        $lengthAlpha = strlen($solutionAlpha);
        $lengthBeta  = strlen($solutionBeta);
        $lengthTime = strlen($time);
        $length = max($lengthAlpha, $lengthBeta, $lengthTime);

        $separator = '*' . str_repeat('-', $length) . '*' . PHP_EOL;
        $fillerTime = str_repeat(' ', $length - $lengthTime + 1);
        $fillerAlpha = str_repeat(' ', $length - $lengthAlpha + 1);
        $fillerBeta = str_repeat(' ', $length - $lengthBeta + 1);

        echo PHP_EOL;
        echo $separator;
        echo $solutionAlpha . $fillerAlpha . '|' . PHP_EOL;
        echo $solutionBeta . $fillerBeta . '|' . PHP_EOL;
        echo '|' . str_repeat(' ', $length) . '|' . PHP_EOL;
        echo $time . $fillerTime . '|' . PHP_EOL;
        echo $separator;
        echo PHP_EOL;
    }
}
