<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = [];

#################
### Solutions ###
#################

/**
 * Calculates which elves delivered presents to the provided house number.
 *
 * @param int $house The house number.
 *
 * @return int[] The elves' delivery identifiers.
 */
function identifyElves(int $house, bool $lazyElves = false): array
{
    $elves = [];

    $root = sqrt($house);
    for ($elf = 1; $elf < $root; $elf++) {
        if ($house % $elf === 0) {
            $elves[] = $elf;
            $elves[] = $house / $elf;
        }
    }

    sort($elves);
    if ($lazyElves === true) {
        foreach ($elves as $index => $elf) {
            if ($house / $elf > 50) {
                unset($elves[$index]);
            } else {
                break;
            }
        }
    }

    return $elves;
}

/**
 * Find the first house to receive at least the specified amount of presents.
 *
 * @param int $presentGoal The minimum amount of presents received by the target house.
 *
 * @return int The house number of the target house.
 */
function findFirstHouseWithPresents(int $presentGoal, int $presentsPerElf, bool $lazyElves = false): int
{
    $targetHouse = 0;

    for ($house = 1; $house < INF; $house++) {
        $elves = identifyElves($house, $lazyElves);

        $presents = array_sum($elves) * $presentsPerElf;

        if ($presents >= $presentGoal) {
            $targetHouse = $house;
            break;
        }
    }

    return $targetHouse;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    return findFirstHouseWithPresents(34000000, 10);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    return findFirstHouseWithPresents(34000000, 11, true);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));