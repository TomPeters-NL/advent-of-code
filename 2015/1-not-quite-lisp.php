<?php

$input = file('./input/1.txt');

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $instructions = $input[0];

    $up = substr_count($instructions, '(');
    $down = substr_count($instructions, ')');

    return $up - $down;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $instructions = str_split($input[0]);

    $floor = 0;
    $position = 0;

    do {
        $instructions[$position] === '(' ? $floor++ : $floor--;
        $position++;
    } while ($floor >= 0);

    return $position;
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