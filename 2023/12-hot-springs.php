<?php

$input = file('./input/12-test.txt');

function iterateRecord(int &$sum, string $record, array $groupSizes, array &$cache, int $index = 0): void
{
    $isDamagedRecord = strpos($record, '?') !== false;
    $isInCache = array_key_exists($record . $index, $cache);
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $arrangementSum = 0;
    $cache = [];

    foreach ($input as $springRecord) {
        list($record, $groupSizes) = explode(' ', $springRecord);
        $record = trim($record);
        $groupSizes = array_map('intval', explode(',', trim($groupSizes)));

        iterateRecord($arrangementSum, $record, $groupSizes, $cache);
    }

    return $arrangementSum;
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