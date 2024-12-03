<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/14', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Analyze each reindeer's description for their stats.
 *
 * @param string[] $input The puzzle input.
 *
 * @return array A list of each reindeer's stats.
 */
function analyzeReindeer(array $input): array
{
    $reindeer = [];

    foreach ($input as $line) {
        preg_match('/([A-Z][a-z]+)\D+(\d+)\D+(\d+)\D+(\d+)/', $line, $matches);

        [$ignore, $name, $speed, $stamina, $rest] = $matches;

        $reindeer[$name] = [$name, (int) $speed, (int) $stamina, (int) $rest];
    }

    return $reindeer;
}

/**
 * Calculate the distance traveled for a single reindeer at a given point in time.
 *
 * @param array $reindeer A single reindeer's stats.
 * @param int   $seconds  The time in seconds for which the distance traveled should be calculated.
 *
 * @return int The distance traveled by the reindeer.
 */
function calculateDistanceAtTime(array $reindeer, int $seconds): int
{
    [$name, $speed, $stamina, $rest] = $reindeer;

    # Determine the length of a full cycle, and the distance a reindeer can travel in it.
    $cycle = $stamina + $rest;
    $cycleDistance = $speed * $stamina;

    # Determine the amount of full cycles and the distance traveled in them.
    $fullCycles = (int) floor($seconds / $cycle);
    $fullDistance = $fullCycles * $cycleDistance;

    # Determine the partial cycle length and the distance traveled in it.
    $partialCycle = ($seconds - ($fullCycles * $cycle));
    $partialDistance = $partialCycle > $stamina ? $cycleDistance : $partialCycle * $speed;

    return $fullDistance + $partialDistance;
}

/**
 * Calculate the distance traveled by each reindeer according to Santa's classic rules.
 *
 * @param array $reindeer A list of each reindeer's stats.
 * @param int   $seconds  The race duration in seconds.
 *
 * @return int[]
 */
function calculateDistances(array $reindeer, int $seconds): array
{
    $distances = [];

    foreach ($reindeer as $currentReindeer) {
        $name = $currentReindeer[0];

        $distances[$name] = calculateDistanceAtTime($currentReindeer, $seconds);
    }

    return $distances;
}

/**
 * Calculates the points each reindeer earns during the race under Santa's new rules.
 *
 * @param array $reindeer A list of each reindeer's stats.
 * @param int   $seconds The length of the race in seconds.
 *
 * @return int[] The points each reindeer has earned.
 */
function calculatePoints(array $reindeer, int $seconds): array
{
    $distances = [];
    $time = range(1, $seconds);

    # For each second, determine the reindeer in the lead and log them.
    foreach ($time as $second) {
        $distanceAtTime = [];

        foreach ($reindeer as $focusReindeer) {
            $distanceAtTime[$focusReindeer[0]] = calculateDistanceAtTime($focusReindeer, $second);
        }

        arsort($distanceAtTime);
        $leadValue = $distanceAtTime[array_key_first($distanceAtTime)];

        # As ties are possible, check for multiple occurrences of the same leading distance.
        foreach ($distanceAtTime as $name => $distance) {
            if ($distance === $leadValue) {
                $distances[$second][] = $name;
            } else {
                break;
            }
        }
    }

    $reindeerNames = array_keys($reindeer);
    $reindeerStarts = array_fill(0, count($reindeer), 0);
    $points = array_combine($reindeerNames, $reindeerStarts);

    # Calculate the points for each time a reindeer is in the lead.
    foreach ($distances as $names) {
        foreach ($names as $name) {
            $points[$name]++;
        }
    }

    return $points;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $reindeer = analyzeReindeer($input);

    $distances = calculateDistances($reindeer, 2503);

    return max($distances);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $reindeer = analyzeReindeer($input);

    $points = calculatePoints($reindeer, 2503);

    return max($points);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));