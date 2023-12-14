<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/3.txt');

#################
### Solutions ###
#################

/**
 * @param int[] $presentGrid
 * @param string[] $flightPlan
 *
 * @return int[]
 */
function executeFlightPlan(array $presentGrid, array $flightPlan): array
{
    $x = $y = 0;
    foreach ($flightPlan as $direction) {
        match ($direction) {
            '^' => $y++,
            '>' => $x++,
            'v' => $y--,
            '<' => $x--,
        };

        $coordinates = $x . ',' . $y;
        array_key_exists($coordinates, $presentGrid) === true
            ? $presentGrid[$coordinates]++
            : $presentGrid[$coordinates] = 0;
    }

    return $presentGrid;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $flightPlan = str_split($input[0]);

    $presents = ['0,0' => 1];
    $presents = executeFlightPlan($presents, $flightPlan);

    return count($presents);
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $completeFlightPlan = str_split($input[0]);
    $fleshSantaFlightPlan = [];
    $robotSantaFlightPlan = [];
    foreach ($completeFlightPlan as $index => $direction) {
        if ($index % 2 === 0) {
            $fleshSantaFlightPlan[] = $direction;
        } else {
            $robotSantaFlightPlan[] = $direction;
        }
    }

    $flightPlans = [$fleshSantaFlightPlan, $robotSantaFlightPlan];
    $presents = ['0,0' => 2];
    foreach ($flightPlans as $flightPlan) {
        $presents = executeFlightPlan($presents, $flightPlan);
    }

    return count($presents);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));