<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/9');

#################
### Solutions ###
#################

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
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
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
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
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

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));