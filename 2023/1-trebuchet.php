<?php

$input = file('./input/1.txt');

/**
 * @param string[] $input
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
 * @param string[] $input
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