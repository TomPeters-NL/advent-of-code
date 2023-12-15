<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/9-test.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    # Dijkstra's Algorithm.

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