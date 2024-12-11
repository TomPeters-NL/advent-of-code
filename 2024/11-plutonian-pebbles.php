<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/11', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

function blinkAndObserve(int $stone, array &$stoneCache, int $blinks = 0, int $blinkLimit = 25): int
{
    $blinks++;

    $nextStones = $stoneCache[$stone] ?? [];

    if (empty($nextStones)) {
        $digits = strlen((string) $stone);

        if ($stone === 0) {
            $nextStones = [1];
        } elseif ($digits % 2 === 0) {
            $splitStone = array_chunk(str_split((string) $stone), $digits / 2);

            foreach ($splitStone as $stoneFragments) {
                $nextStones[] = (int) implode('', $stoneFragments);
            }
        } else {
            $nextStones = [2024 * $stone];
        }

        $stoneCache[$stone] = $nextStones;
    }

    if ($blinks === $blinkLimit) {
        return count($nextStones);
    }

    $stones = 0;

    foreach ($nextStones as $nextStone) {
        $stones += blinkAndObserve($nextStone, $stoneCache, $blinks);
    }

    return $stones;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $totalStones = 0;

    $stoneCache = [];
    $stones = array_map('intval', explode(' ', $input[0]));

    foreach ($stones as $stone) {
        $totalStones += blinkAndObserve($stone, $stoneCache);
    }

    return $totalStones;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    return 2;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));