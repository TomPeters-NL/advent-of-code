<?php

declare(strict_types=1);

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
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $instructions = $input[0];

    $up = substr_count($instructions, '(');
    $down = substr_count($instructions, ')');

    return $up - $down;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $instructions = str_split($input[0]);

    $floor = 0;
    $position = 0;

    do {
        $instructions[$position] === '(' ? $floor++ : $floor--;
        $position++;
    } while ($floor >= 0);

    return $position;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));