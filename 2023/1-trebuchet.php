<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/1');

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
    $output = 0;
    foreach ($input as $line) {
        preg_match_all('/\d/', $line, $matches);
        $numbers = $matches[0];

        $firstDigit = $numbers[array_key_first($numbers)];
        $secondDigit = $numbers[array_key_last($numbers)];

        $output += (int)($firstDigit . $secondDigit);
    }

    return $output;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $output = 0;
    foreach ($input as $line) {
        preg_match_all('/(?=(\d|one|two|three|four|five|six|seven|eight|nine))/', $line, $matches);

        $text = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $digits = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $numbers = str_replace($text, $digits, $matches[1]);

        $firstDigit = $numbers[array_key_first($numbers)];
        $secondDigit = $numbers[array_key_last($numbers)];

        $output += (int)($firstDigit . $secondDigit);
    }

    return $output;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));