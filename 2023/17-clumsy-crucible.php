<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/17-test.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Prepares the input map for Dijkstra's algorithm.
 *
 * @param string[] $input
 *
 * @return int[][]
 */
function prepareMap(array $input): array
{
    $heatLossMap = [];

    foreach ($input as $row => $line) {
        $columns = str_split($line);
        $integers = array_map('intval', $columns);

        $heatLossMap[$row] = $integers;
    }

    return $heatLossMap;
}

function findOptimalPath(array $heatLossMap, array $start, array $target): array
{
    return [];
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    # Dijkstra's Algorithm
    # --------------------
    # Pathfinding optimization towards a global solution by continuously making the locally optimal choices.
    # For each point, retrieve the cost of all surrounding points.
    # Make the locally optimal choice, in this case, minimal heat loss.
    # Make previous location inaccessible.
    # Can only move in a straight line for three points at a time.
    #
    # Check: https://www.redblobgames.com/pathfinding/a-star/introduction.html


    return 1;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    return 2;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));