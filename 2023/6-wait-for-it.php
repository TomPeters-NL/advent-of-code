<?php

$input = file('./input/6.txt');

/**
 * @return int[]
 */
function calculateDistance(int $raceDuration): array
{
    $distances = [];
    for ($chargeTime = 0; $chargeTime <= $raceDuration; $chargeTime++) {
        $speed = $chargeTime; // 1 ms of charging increases speed by 1 mm/ms.
        $distance = $speed * ($raceDuration - $chargeTime);

        $distances[$chargeTime] = $distance;
    }

    return $distances;
}

function calculateViableStrategies(int $time, int $distance): int
{
    $minimumChargingTime = ($time - sqrt(($time ** 2) - (4 * $distance))) / 2;
    $maximumChargingTime = ($time + sqrt(($time ** 2) - (4 * $distance))) / 2;

    return ceil($maximumChargingTime) - floor($minimumChargingTime) - 1;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    preg_match_all('/\d+/', $input[0], $times);
    preg_match_all('/\d+/', $input[1], $distances);

    $viableStrategies = 1;
    $targetTimes = array_map('intval', $times[0]);
    $targetDistances = array_map('intval', $distances[0]);

    for ($i = 0; $i < count($targetTimes); $i++) {
        $viableStrategies *= calculateViableStrategies($targetTimes[$i], $targetDistances[$i]);
    }

    return $viableStrategies;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    preg_match_all('/\d+/', $input[0], $timeFragments);
    preg_match_all('/\d+/', $input[1], $distanceFragments);

    $targetTime = (int)implode('', $timeFragments[0]);
    $targetDistance = (int)implode('', $distanceFragments[0]);

    return calculateViableStrategies($targetTime, $targetDistance);
}

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