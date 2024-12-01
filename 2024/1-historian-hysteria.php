<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/1');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 *
 * @return int[][]
 */
function organizeLocationIds(array $input): array
{
    $organizedLists = [];

    foreach ($input as $line) {
        list($first, $second) = explode('   ', $line);

        $organizedLists[0][] = (int) $first;
        $organizedLists[1][] = (int) $second;
    }

    sort($organizedLists[0]);
    sort($organizedLists[1]);

    return $organizedLists;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $totalDistance = 0;

    list($firstList, $secondList) = organizeLocationIds($input);

    for ($i = 0; $i < count($firstList); $i++) {
        $totalDistance += abs($secondList[$i] - $firstList[$i]);
    }

    return $totalDistance;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $similarityScore = 0;

    list($firstList, $secondList) = organizeLocationIds($input);

    foreach ($firstList as $locationId) {
        $similarityScore += $locationId * count(array_keys($secondList, $locationId));
    }

    return $similarityScore;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));