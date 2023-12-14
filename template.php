<?php

$input = file('./input/1.txt');

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    return 1;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    return 2;
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