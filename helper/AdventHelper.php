<?php

namespace AdventOfCode\Helper;

class AdventHelper
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Converts a one-dimensional string array to a two-dimensional array.
     *
     * @param string[] $list The string array to be converted.
     * @param bool     $castToIntegers Determines whether the values in the list should be cast to integers.
     *
     * @return string[][] | int[][] The two-dimensional array.
     */
    public function convertStringListToMap(array $list, bool $castToIntegers = false): array
    {
        return $castToIntegers
            ? array_map(fn ($row) => array_map('intval', str_split(trim($row))), $list)
            : array_map(fn ($row) => str_split(trim($row)), $list);
    }

    /**
     * Prints two formatted values, usually the day's Part 1 and Part 2 solutions.
     *
     * @param string|int $alpha The first printed result.
     * @param string|int $beta The second printed result.
     *
     * @return void
     */
    public function printSolutions(string | int $alpha, string | int $beta): void
    {
        $endTime = microtime(true);
        $duration = $endTime - $this->startTime;
        $milliseconds = number_format($duration * 1000, 2);
        $time = "| Time: $milliseconds ms";

        $solutionAlpha = "| Solution #1: $alpha";
        $solutionBeta = "| Solution #2: $beta";

        $lengthAlpha = strlen($solutionAlpha);
        $lengthBeta = strlen($solutionBeta);
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
