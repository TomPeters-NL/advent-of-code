<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/17', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Casts the container values to integers and orders them from smallest to largest.
 *
 * @param string[] $input
 *
 * @return int[]
 */
function orderContainers(array $input): array
{
    $containers = array_map('intval', $input);

    sort($containers);

    return $containers;
}

/**
 * Calculates the number of valid combinations and amount of containers required to store the specified amount of eggnog.
 *
 * @param int[] $volumes     The available container volumes.
 * @param int   $totalVolume The total volume of eggnog to be stored.
 * @param int   $index       The current index of the container to be used.
 * @param int   $containers  The amount of containers currently used.
 * @param int   $volume      The current volume of eggnog stored.
 *
 * @return array A list of valid container counts and the amount of possible combinations.
 */
function calculateCombinations(array $volumes, int $totalVolume, int $index = 0, int $containers = 0, int $volume = 0): array
{
    # Track the amount of possible combinations and their container counts.
    $containerCount = [];
    $combinations = 0;

    for ($i = $index; $i < count($volumes); $i++) {
        # Calculate the volume stored if the current container were to be used.
        $nVolume = $volume + $volumes[$i];

        # If the storage volume exceeds the eggnog to be stored, do not continue the loop.
        # This is possible due to the smallest to largest sorting.
        if ($nVolume > $totalVolume) {
            break;
        }

        # If a valid container size, increment the container count for this iteration.
        $nContainers = $containers + 1;

        # If the storage volume matches the amount of eggnog exactly, log the container count and increment the number of valid combinations.
        if ($nVolume === $totalVolume) {
            $containerCount[] = $nContainers;
            $combinations++;
            continue;
        }

        # Perform a recursive call to this function to test the next containers.
        [$rContainers, $rCombinations] = calculateCombinations($volumes, $totalVolume, $i + 1, $nContainers, $nVolume);
        $containerCount = array_merge($containerCount, $rContainers);
        $combinations += $rCombinations;
    }

    return [$containerCount, $combinations];
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $containers = orderContainers($input);

    [$requiredContainers, $combinations] = calculateCombinations($containers, 150);

    return $combinations;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $containers = orderContainers($input);

    [$requiredContainers, $combinations] = calculateCombinations($containers, 150);

    $counts = array_count_values($requiredContainers);
    ksort($counts);

    return $counts[array_key_first($counts)];
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));