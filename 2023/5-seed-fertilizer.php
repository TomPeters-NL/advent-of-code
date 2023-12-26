<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/5');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 *
 * @return int[]
 */
function getSeeds(array $input, bool $ranges = false): array
{
    $row = $input[0];

    if ($ranges === false) {
        preg_match_all('/\d+/', $row, $matches);

        $seeds = array_map(fn ($x) => intval(trim($x)), $matches[0]);
    } else {
        preg_match_all('/\d+ \d+/', $row, $matches);
        array_walk($matches[0], 'trim');

        $seeds = [];
        foreach ($matches[0] as $seedData) {
            list($seedStart, $rangeLength) = explode(' ', $seedData);

            $seeds[] = [
                (int) $seedStart,                       // Start of seed range.
                (int) $seedStart + $rangeLength - 1,    // End of seed range.
            ];
        }
    }

    return $seeds;
}

/**
 * @param string[] $input
 *
 * @return int[][]
 */
function getMaps(array $input): array
{
    $rows = array_slice($input, 2);

    $maps = [];
    $header = null;
    foreach ($rows as $row) {
        $trimmedRow = trim($row);

        $isEmpty = empty($trimmedRow);
        $isHeader = str_contains($trimmedRow, 'map');

        if ($isHeader === true) {
            $header = explode(' ', $trimmedRow)[0];

            $maps[$header] = [];
        } elseif ($isEmpty === false) {
            list($destination, $source, $rangeLength) = explode(' ', $row);

            $maps[$header][] = [
                (int) $source,                          // Start of source range.
                (int) $source + $rangeLength - 1,       // End of source range.
                (int) $destination,                     // Start of destination range.
                (int) $destination + $rangeLength - 1,  // End of destination range.
                (int) $rangeLength,                     // Length of range.
            ];
        }
    }

    foreach ($maps as $header => &$map) {
        usort($map, fn($x, $y) => $x[0] <=> $y[0]);
    }

    return $maps;
}

function isPartialTopOverlap(int $inputStart, int $inputEnd, int $sourceStart, int $sourceEnd): bool
{
    return $inputStart > $sourceStart && $inputStart <= $sourceEnd && $inputEnd > $sourceEnd;
}

function isPartialBottomOverlap(int $inputStart, int $inputEnd, int $sourceStart, int $sourceEnd): bool
{
    return $inputStart < $sourceStart && $inputEnd < $sourceEnd && $inputEnd >= $sourceStart;
}

function isCompleteInnerOverlap(int $inputStart, int $inputEnd, int $sourceStart, int $sourceEnd): bool
{
    return $inputStart <= $sourceStart && $inputEnd > $sourceEnd || $inputStart < $sourceStart && $inputEnd >= $sourceEnd;
}

function isCompleteOuterOverlap(int $inputStart, int $inputEnd, int $sourceStart, int $sourceEnd): bool
{
    return $inputStart >= $sourceStart && $inputEnd <= $sourceEnd;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $seeds = getSeeds($input);
    $maps = getMaps($input);

    $locations = [];
    foreach ($seeds as $index => $seed) {
        $source = $seed;

        foreach ($maps as $map) {
            $locations[$index] = $source;

            foreach ($map as [$sourceStart, $sourceEnd, $destinationStart, $destinationEnd, $rangeLength]) {
                if ($source >= $sourceStart && $source <= $sourceEnd) {
                    $locations[$index] = $destinationStart + ($source - $sourceStart);
                    break;
                }
            }

            $source = $locations[$index];
        }
    }

    return min($locations);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $seeds = getSeeds($input, true);
    $maps = getMaps($input);

    $header = $inputHeader = 'seeds';
    $inputRanges = [$header => $seeds];

    foreach ($maps as $header => $map) { // Loop through the various maps.
        foreach ($map as [$sourceStart, $sourceEnd, $destinationStart, $destinationEnd, $rangeLength]) { // Loop through the map ranges.
            foreach ($inputRanges[$inputHeader] as $index => [$inputStart, $inputEnd]) { // Loop through the currently relevant input ranges.

                if (isPartialTopOverlap($inputStart, $inputEnd, $sourceStart, $sourceEnd) === true) {
                    $destinationRangeStart = $destinationEnd - ($sourceEnd - $inputStart);
                    $destinationRangeEnd = $destinationEnd;

                    $remainingRangeStart = $sourceEnd + 1;
                    $remainingRangeEnd = $inputEnd;

                    $inputRanges[$header][] = [$destinationRangeStart, $destinationRangeEnd]; // Register the transformed range for the next round.
                    $inputRanges[$inputHeader][$index] = [$remainingRangeStart, $remainingRangeEnd]; // Update the current input ranges.
                }

                if (isPartialBottomOverlap($inputStart, $inputEnd, $sourceStart, $sourceEnd) === true) {
                    $destinationRangeStart = $destinationStart;
                    $destinationRangeEnd = $destinationStart + ($inputEnd - $sourceStart);

                    $remainingRangeStart = $inputStart;
                    $remainingRangeEnd = $sourceStart - 1;

                    $inputRanges[$header][] = [$destinationRangeStart, $destinationRangeEnd]; // Register the transformed range for the next round.
                    $inputRanges[$inputHeader][$index] = [$remainingRangeStart, $remainingRangeEnd]; // Update the current input ranges.
                }

                if (isCompleteInnerOverlap($inputStart, $inputEnd, $sourceStart, $sourceEnd) === true) {
                    $destinationRangeStart = $destinationStart;
                    $destinationRangeEnd = $destinationEnd;

                    if ($inputStart < $sourceStart) { // Update the current input ranges.
                        $remainingTopRangeStart = $inputStart;
                        $remainingTopRangeEnd = $sourceStart - 1;
                        $inputRanges[$inputHeader][] = [$remainingTopRangeStart, $remainingTopRangeEnd];
                    }

                    if ($inputEnd > $sourceEnd) { // Update the current input ranges.
                        $remainingBottomRangeStart = $sourceEnd + 1;
                        $remainingBottomRangeEnd = $inputEnd;
                        $inputRanges[$inputHeader][] = [$remainingBottomRangeStart, $remainingBottomRangeEnd];
                    }

                    $inputRanges[$header][] = [$destinationRangeStart, $destinationRangeEnd]; // Register the transformed range for the next round.
                    unset($inputRanges[$inputHeader][$index]); // Update the current input ranges.
                }

                if (isCompleteOuterOverlap($inputStart, $inputEnd, $sourceStart, $sourceEnd) === true) {
                    $destinationRangeStart = $destinationStart + ($inputStart - $sourceStart);
                    $destinationRangeEnd = $destinationEnd - ($sourceEnd - $inputEnd);

                    $inputRanges[$header][] = [$destinationRangeStart, $destinationRangeEnd]; // Register the transformed range for the next round.
                    unset($inputRanges[$inputHeader][$index]); // Update the current input ranges.
                }

            } // Done looping through the input ranges.
        } // Done looping through the map ranges.


        // Move any remaining input ranges to the next round.
        foreach($inputRanges[$inputHeader] as $index => $inputRange) {
            $inputRanges[$header][] = $inputRange;
            unset($inputRanges[$inputHeader][$index]);
        }

        // Move on to the next map (header).
        $inputHeader = $header;

    } // Done looping through the maps.

    $locationRanges = $inputRanges['humidity-to-location'];
    usort($locationRanges, fn ($x, $y) => $x[0] <=> $y[0]);

    return $locationRanges[0][0];
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));