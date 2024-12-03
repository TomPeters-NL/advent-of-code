<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/12', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * A recursive method that sums up all integers in a decoded JSON array or object.
 * The method disqualifies any objects that contain the value "red".
 *
 * @param array|stdClass $json The JSON object or array.
 *
 * @return int The sum of the integers in the current array or object.
 */
function calculateNestedSum(array | stdClass $json): int
{
    # Check if the provided JSON value is an object, and if so, whether it contains the value "red".
    $isObject = $json instanceof stdClass;
    $hasRed = $isObject === true && in_array('red', (array) $json);

    $sum = 0;

    # If the JSON is not disqualified, start summing up integers.
    if ($hasRed === false) {
        foreach ((array) $json as $value) {
            # If the value is an integer, simply add it to the sum.
            # If the value is another array or object, make a recursive call to this function.
            if (is_integer($value) === true) {
                $sum += $value;
            } elseif (is_array($value) === true || is_object($value) === true) {
                $sum += calculateNestedSum($value);
            }
        }
    }

    return $sum;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    # Retrieve all numbers from the raw JSON.
    preg_match_all('/-?\d+/', $input[0], $matches);

    $integers = array_map('intval', $matches[0]);

    return array_sum($integers);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $json = $input[0];

    $decodedJson = json_decode($json);

    return calculateNestedSum($decodedJson);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));