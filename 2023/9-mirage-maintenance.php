<?php

$input = file('./input/9.txt');

/**
 * @param int[][] $pattern
 */
function analyze(array &$pattern): void
{
    $previousPattern = $pattern[array_key_last($pattern)];

    $subPattern = [];
    $length = count($previousPattern);
    for ($i = 1; $i < $length; $i++) {
        $subPattern[] = $previousPattern[$i] - $previousPattern[$i - 1];
    }
    $pattern[] = $subPattern;

    $isFinalPattern = empty(array_diff($subPattern, [0]));
    if ($isFinalPattern === false) {
        analyze($pattern);
    }
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $oasisOutput = 0;
    foreach ($input as $row) {
        $pattern = [array_map('intval', explode(' ', $row))];
        analyze($pattern);

        foreach ($pattern as $subPattern) {
            $lastKey = array_key_last($subPattern);
            $oasisOutput += $subPattern[$lastKey];
        }
    }

    return $oasisOutput;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $oasisOutput = 0;
    foreach ($input as $row) {
        $pattern = [array_reverse(array_map('intval', explode(' ', $row)))];
        analyze($pattern);

        foreach ($pattern as $subPattern) {
            $lastKey = array_key_last($subPattern);
            $oasisOutput += $subPattern[$lastKey];
        }
    }

    return $oasisOutput;
}

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;