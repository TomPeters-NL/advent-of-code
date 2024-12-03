<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/3', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $validTriangles = 0;

    foreach ($input as $specifications) {
        preg_match_all("/\d+/", $specifications, $matches);
        $sides = array_map('intval', $matches[0]);
        sort($sides);

        $validTriangles += (int) ($sides[0] + $sides[1] > $sides[2]);
    }

    return $validTriangles;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $allSides = [];
    $columnLength = count($input);

    foreach ($input as $index => $specifications) {
        preg_match_all("/\d+/", $specifications, $matches);
        $sides = array_map('intval', $matches[0]);

        $allSides[$index] = $sides[0];
        $allSides[$index + $columnLength] = $sides[1];
        $allSides[$index + 2 * $columnLength] = $sides[2];
    }

    $validTriangles = 0;

    ksort($allSides);
    $chunks = array_chunk($allSides, 3);

    foreach ($chunks as $sides) {
        sort($sides);

        $validTriangles += (int) ($sides[0] + $sides[1] > $sides[2]);
    }

    return $validTriangles;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));