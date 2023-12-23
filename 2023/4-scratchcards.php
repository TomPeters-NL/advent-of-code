<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/4.txt');

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
    $scratchCards = preg_replace('/ *Card \d+: /', '', $input);

    $points = 0;
    foreach ($scratchCards as $scratchCard) {
        $scoringNumbers = getScoringNumbers($scratchCard);
        $scoringAmount = count($scoringNumbers);

        if ($scoringAmount > 0) {
            $points += 2 ** ($scoringAmount - 1);
        }
    }

    return $points;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $scratchCards = preg_replace('/ *Card \d+: /', '', $input);

    $originalAmount = count($scratchCards);
    $scratchCards = array_combine(range(1, $originalAmount), $scratchCards);
    $inventory = array_fill(1, $originalAmount, 1);

    foreach ($scratchCards as $cardNumber => $scratchCard) {
        $frequency = $inventory[$cardNumber];
        $scoringNumbers = getScoringNumbers($scratchCard);
        $scoringCount = count($scoringNumbers);
        for ($a = 1; $a <= $frequency; $a++) {
            for ($b = 1; $b <= $scoringCount; $b++) {
                $inventory[$cardNumber + $b]++;
            }
        }
    }

    return array_sum($inventory);
}

/**
 * @return int[]
 */
function getScoringNumbers(string $scratchCard): array
{
    list($winningString, $potentialString) = explode('|', $scratchCard);

    $winningNumbers = explode(' ', trim($winningString));
    $winningNumbers = array_filter($winningNumbers);
    array_walk($winningNumbers, 'intval');

    $potentialNumbers = explode(' ', trim($potentialString));
    $potentialNumbers = array_filter($potentialNumbers);
    array_walk($potentialNumbers, 'intval');

    return array_intersect($winningNumbers, $potentialNumbers);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));