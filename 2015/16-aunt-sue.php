<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/16', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Generates a list of aunts, each containing a list of remembered properties.
 *
 * @param array $input The puzzle input.
 *
 * @return int[][] Each aunt's properties and their values.
 */
function processAunts(array $input): array
{
    $aunts = [];

    foreach ($input as $line) {
        # Isolate aunt Sue's number and their properties.
        preg_match('/^\D+(\d+): (.+)/', $line, $matches);
        [$ignore, $sue, $data] = $matches;
        $sue = (int) $sue;

        # Transform the property list from a string to an associative array.
        $propertyList = explode(',', $data);
        foreach ($propertyList as $property) {
            [$name, $value] = explode(':', $property);

            $name = trim($name);
            $value = (int) trim($value);

            $aunts[$sue][$name] = $value;
        }
    }

    return $aunts;
}

/**
 * Finds the aunt Sue who gifted the MFCSAM, by matching the properties detected by the MFCSAM.
 *
 * @param array $aunts Each aunt's properties and their values.
 * @param array $mfcsam The aunt properties detected by the MFCSAM.
 * @param bool $outdatedRetroencabulator Indicates whether some property values are ranges instead.
 *
 * @return int The identifying number of the aunt Sue that gifted the MFCSAM.
 */
function detectAunt(array $aunts, array $mfcsam, bool $outdatedRetroencabulator = false): int
{
    $matchingAunt = 0;

    $retroencabulatorRanges = ['cats', 'trees', 'pomeranians', 'goldfish'];

    foreach ($aunts as $aunt => $properties) {
        $matchingProperties = 0;

        foreach ($properties as $name => $value) {
            if ($outdatedRetroencabulator === true && in_array($name, $retroencabulatorRanges) === true) {
                $inRange = match($name) {
                    'cats', 'trees' => $value > $mfcsam[$name],
                    'pomeranians', 'goldfish' => $value < $mfcsam[$name],
                };

                if ($inRange === false) {
                    break;
                }
            } elseif ($mfcsam[$name] !== $value) {
                break;
            }

            $matchingProperties++;
        }

        if ($matchingProperties === count($properties)) {
            $matchingAunt = $aunt;
            break;
        }
    }

    return $matchingAunt;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $mfcsam = [
        'children' => 3,
        'cats' => 7,
        'samoyeds' => 2,
        'pomeranians' => 3,
        'akitas' => 0,
        'vizslas' => 0,
        'goldfish' => 5,
        'trees' => 3,
        'cars' => 2,
        'perfumes' => 1,
    ];

    $aunts = processAunts($input);

    return detectAunt($aunts, $mfcsam);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $mfcsam = [
        'children' => 3,
        'cats' => 7,
        'samoyeds' => 2,
        'pomeranians' => 3,
        'akitas' => 0,
        'vizslas' => 0,
        'goldfish' => 5,
        'trees' => 3,
        'cars' => 2,
        'perfumes' => 1,
    ];

    $aunts = processAunts($input);

    return detectAunt($aunts, $mfcsam, true);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));