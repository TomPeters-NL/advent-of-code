<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/6.txt');

#################
### Solutions ###
#################

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

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));