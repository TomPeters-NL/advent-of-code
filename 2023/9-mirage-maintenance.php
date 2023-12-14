<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/9.txt');

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

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));