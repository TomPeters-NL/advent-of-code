<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/8.txt');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 *
 * @return array[]
 */
function processInput(array $input): array
{
    $directionString = trim(array_shift($input));
    $directions = str_split($directionString);

    $map = [];
    $nodes = array_slice($input, 1);
    foreach ($nodes as $node) {
        // (BBB, BBB)
        list($node, $leftRight) = explode('=', $node);
        preg_match_all('/[0-9A-Z]+/', $leftRight, $matches);

        $node = trim($node);
        $map[$node] = [
            'L' => trim($matches[0][0]),
            'R' => trim($matches[0][1]),
        ];
    }

    return [$directions, $map];
}

/**
 * @param string[] $map
 *
 * @return string[]
 */
function findStartingNodes(array $map): array
{
    $startingNodes = [];
    foreach ($map as $node => $potentialNodes) {
        if (str_ends_with($node, 'A') === true) {
            $startingNodes[] = $node;
        }
    }

    return $startingNodes;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    list($directions, $map) = processInput($input);

    $steps = 0;
    $navigationPlan = [];
    $node = 'AAA';
    while ($node !== 'ZZZ') {
        $potentialNodes = $map[$node];

        if (empty($navigationPlan) === true) {
            $navigationPlan = $directions;
        }

        $direction = array_shift($navigationPlan);
        $node = $potentialNodes[$direction];
        $steps++;
    }

    return $steps;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    list($directions, $map) = processInput($input);

    $steps = 0;
    $navigationPlan = $stepsPerNode = [];
    $nodes = findStartingNodes($map);
    $simultaneousNodes = count($nodes);
    do {
        $steps++;
        if (empty($navigationPlan) === true) {
            $navigationPlan = $directions;
        }

        $direction = array_shift($navigationPlan);
        foreach ($nodes as $index => $node) {
            $potentialNodes = $map[$node];

            $nextNode = $potentialNodes[$direction];

            if (str_ends_with($nextNode, 'Z') === true) {
                $stepsPerNode[] = $steps;
                unset($nodes[$index]);
            } else {
                $nodes[$index] = $nextNode;
            }
        }
    } while (count($stepsPerNode) < $simultaneousNodes);

    $leastCommonMultiple = array_shift($stepsPerNode);
    foreach ($stepsPerNode as $steps) {
        $leastCommonMultiple = gmp_lcm($leastCommonMultiple, $steps);
    }

    return (int)$leastCommonMultiple;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));