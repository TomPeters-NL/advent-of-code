<?php

$input = file('./input/5.txt');

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

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;