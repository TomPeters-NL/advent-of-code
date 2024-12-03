<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = [40, 50, '1321131112'];

#################
### Solutions ###
#################

/**
 * Generates the next sequence according to the Elves' game rules.
 * For example, 111221 consists of 3 1s, 2 2s, and 1 1, making the resulting sequence 312211.
 *
 * @param string $sequence The source sequence to use as the basis for the new one.
 *
 * @return string The resulting new sequence.
 */
function generateNextSequence(string $sequence): string
{
    $newSequence = '';

    preg_match_all('/((\d)\2*)/', $sequence, $matches);
    $subSequences = $matches[1];

    foreach($subSequences as $subSequence) {
        $newSequence .= strlen($subSequence) . $subSequence[0];
    }

    return $newSequence;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    [$repetitions, $ignore, $sequence] = $input;

    $newSequence = $sequence;
    for ($i = 0; $i < $repetitions; $i++) {
        $newSequence = generateNextSequence($newSequence);
    }

    return strlen($newSequence);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    [$ignore, $repetitions, $sequence] = $input;
    $repetitions = 50;

    $newSequence = $sequence;
    for ($i = 0; $i < $repetitions; $i++) {
        $newSequence = generateNextSequence($newSequence);
    }

    return strlen($newSequence);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));