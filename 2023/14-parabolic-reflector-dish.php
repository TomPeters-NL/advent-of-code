<?php

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
 * Rotates a map in a clockwise direction.
 *
 * @param string[] $map
 *
 * @return string[]
 */
function rotateClockwise(array $map): array
{
    # The last row becomes the first column, so reverse the array first.
    $reverseMap = array_reverse($map);

    # Provide the empty transposed map.
    $length = strlen($reverseMap[0]);
    $transposedMap = array_fill(0, $length, '');

    # Concatenate each map row unto the transposed map structure.
    foreach ($reverseMap as $line) {
        for ($column = 0; $column < $length; $column++) {
            $transposedMap[$column] .= $line[$column];
        }
    }

    return $transposedMap;
}

/**
 * Tilts the platform and generates a new map.
 * As the rows of the map are sorted from left to right, ensure that the tilt direction is facing to the right.
 * For example, if you were to tilt the map northwards, rotate it clockwise once (assuming north was up at first).
 *
 * @param string[] $map
 *
 * @return string[]
 */
function tiltPlatform(array $map, array &$cache = []): array
{
    $tiltedMap = [];

    foreach ($map as $row) {
        # Check the cache to prevent unnecessary repetitions.
        if (array_key_exists($row, $cache) === true) {
            $tiltedMap[] = $cache[$row];
        } else {
            $rowFragments = explode('#', $row);

            # Sort the fragments so the spheres (O) end up to the right of the empty spaces (.).
            $sortedFragments = array_map(function ($x) {
                $spaces = str_split($x);
                sort($spaces);
                return implode('', $spaces);
            }, $rowFragments);

            $tiltedRow = implode('#', $sortedFragments);

            $tiltedMap[] = $tiltedRow;

            # Cache the result.
            $cache[$row] = $tiltedRow;
        }
    }

    return $tiltedMap;
}

/**
 * Performs spin tilt cycles on the map until a pattern emerges.
 *
 * @param string[] $map
 */
function findCyclePattern(array $map): array
{
    $completeCycle = false;
    $cycle = [];
    $cache = [];

    while ($completeCycle === false) {
        $cycle[] = $map;

        # Perform northward tilt.
        $map = rotateClockwise($map);
        $map = tiltPlatform($map, $cache);

        # Perform westward tilt.
        $map = rotateClockwise($map);
        $map = tiltPlatform($map, $cache);

        # Perform southward tilt.
        $map = rotateClockwise($map);
        $map = tiltPlatform($map, $cache);

        # Perform eastward tilt.
        $map = rotateClockwise($map);
        $map = tiltPlatform($map, $cache);

        if (in_array($map, $cycle) === true) {
            $cycle[] = $map;
            $completeCycle = true;
        }
    }

    return $cycle;
}

/**
 * Calculates the load of spheres on the platform for a provided map configuration.
 *
 * @param string[] $map
 */
function calculateLoad(array $map): int
{
    $load = 0;

    # Have the map indices reflect the load for each row.
    $keys = range(count($map), 1);
    $loadMap = array_combine($keys, $map);

    foreach ($loadMap as $rowLoad => $line) {
        $spaces = str_split($line);
        $spaceCount = array_count_values($spaces);

        $load += ($spaceCount['O'] ?? 0) * $rowLoad;
    }

    return $load;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $transposed = rotateClockwise($input);
    $tilted = tiltPlatform($transposed);
    $originalOrientation = rotateClockwise(rotateClockwise(rotateClockwise($tilted)));

    return calculateLoad($originalOrientation);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $cycle = findCyclePattern($input);

    # The index at which the pattern first repeats.
    $firstRepeat = array_key_last($cycle);

    # The index at which the pattern first emerges.
    $start = array_search($cycle[$firstRepeat], $cycle);

    # The length of the pattern.
    $length = $firstRepeat - $start;

    # The number of unrelated maps before the pattern emerges.
    $offset = $start;

    # How far into the cycle the pattern is at the billionth spin tilt.
    $progress = (1000000000 - $offset) % $length;

    # The index of the map at the billionth iteration.
    $targetIndex = $start + $progress;
    $targetMap = $cycle[$targetIndex];

    return calculateLoad($targetMap);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));
