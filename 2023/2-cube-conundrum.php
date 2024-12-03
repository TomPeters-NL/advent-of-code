<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/2');

#################
### Solutions ###
#################

/**
 * @return int[]
 */
function calculateMaximumDice(string $displays): array
{
    $maximums = ['red' => 0, 'green' => 0, 'blue' => 0];

    preg_match_all('/(\d+) (\w+)/', $displays, $matches);
    $amounts = $matches[1];
    $colors = $matches[2];

    for ($i = 0; $i < count($colors); $i++) {
        $amount = (int)$amounts[$i];
        $color = $colors[$i];

        if ($amount > $maximums[$color]) {
            $maximums[$color] = $amount;
        }
    }

    return $maximums;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $sum = 0;
    foreach ($input as $game) {
        list($gameName, $displays) = explode(':', $game);

        $gameId = (int)explode(' ', $gameName)[1];
        $maximums = calculateMaximumDice($displays);

        $hasEnoughRed = $maximums['red'] <= 12;
        $hasEnoughGreen = $maximums['green'] <= 13;
        $hasEnoughBlue = $maximums['blue'] <= 14;
        if ($hasEnoughRed === true && $hasEnoughGreen === true && $hasEnoughBlue === true) {
            $sum += $gameId;
        }
    }

    return $sum;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $power = 0;
    foreach ($input as $game) {
        $displays = explode(':', $game)[1];

        $maximums = calculateMaximumDice($displays);

        $power += $maximums['red'] * $maximums['green'] * $maximums['blue'];
    }

    return $power;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));