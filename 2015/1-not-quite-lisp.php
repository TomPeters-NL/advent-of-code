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

$solutionOne = partOne($input);
$solutionTwo = partTwo($input);

echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;