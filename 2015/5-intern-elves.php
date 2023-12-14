<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/5.txt');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $nice = 0;
    foreach ($input as $string) {
        preg_match_all('/[aeiou]/', $string, $vowels);
        preg_match_all('/(.)\1/', $string, $doubles);
        preg_match_all('/ab|cd|pq|xy/', $string, $combinations);

        $hasThreeVowels = count($vowels[0]) >= 3;
        $hasNoDoubles = empty($doubles[0]);
        $hasNoCombinations = empty($combinations[0]);

        if ($hasThreeVowels === true && $hasNoDoubles === false && $hasNoCombinations === true) {
            $nice++;
        }
    }

    return $nice;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $nice = 0;
    foreach ($input as $string) {
        preg_match_all('/(..).*\1/', $string, $pairs);
        preg_match_all('/(?=(.).\1)/', $string, $repeaters);

        $hasNoPairs = empty($pairs[0]);
        $hasNoRepeaters = empty($repeaters[0]);

        if ($hasNoPairs === false && $hasNoRepeaters === false) {
            $nice++;
        }
    }

    return $nice;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));