<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/17-test.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

class Space {
    public int $x;
    public int $y;
    public int $cost;
    public string $coordinates;

    public function __construct(int $x, int $y, int $cost = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->cost = $cost;
        $this->coordinates = $x . ',' . $y;
    }
}

/**
 * Prepares the input map for heuristic pathfinding fun.
 *
 * @param string[] $input
 *
 * @return int[][]
 */
function prepareMap(array $input): array
{
    $heatLossMap = [];

    foreach ($input as $row => $line) {
        $columns = str_split($line);
        $integers = array_map('intval', $columns);

        $heatLossMap[$row] = $integers;
    }

    return $heatLossMap;
}

/**
 * Calculates a heuristic score to indicate a location's proximity to the goal.
 */
function targetProximity(Space $location, Space $target): int
{
    return abs($location->x - $target->x) + abs($location->y - $target->y);
}

/**
 * Find all neighboring spaces for a given space.
 *
 * @return Space[]
 */
function findNeighbours(array $heatLossMap, Space $space): array
{
    $potentialNeighbours = [
        [$space->x - 1, $space->y],
        [$space->x + 1, $space->y],
        [$space->x, $space->y - 1],
        [$space->x, $space->y + 1],
    ];

    $neighbours = [];
    foreach ($potentialNeighbours as [$x, $y]) {
        $neighbourExists = isset($heatLossMap[$y][$x]);

        if ($neighbourExists === true) {
            $neighbours[] = new Space($x, $y, $heatLossMap[$y][$x]);
        }
    }

    return $neighbours;
}

function findOptimalPath(array $heatLossMap, Space $start, Space $target): array
{
    #Reference: https://www.redblobgames.com/pathfinding/a-star/introduction.html

    # Initialize the frontier and add the start location.
    /** @var Space[] $frontier */
    $frontier = [$start->coordinates];

    # Initialize the path and cost trackers.
    $path = [$start->coordinates => null];
    $cost = [$start->coordinates => 0];

    while (empty($frontier) === false) {
        # Retrieve the current location from the front of the frontier queue.
        $current = $frontier[array_key_first($frontier)];

        # Check whether the goal has been reached.
        if ($current->coordinates === $target->coordinates) {
            break;
        }

        $neighbours = findNeighbours($heatLossMap, $current);
    }

    /*
    for next in graph.neighbors(current):
        new_cost = cost_so_far[current] + graph.cost(current, next)
        if next not in cost_so_far or new_cost < cost_so_far[next]:
            cost_so_far[next] = new_cost
            priority = new_cost + heuristic(next, goal)
            frontier.put(next, priority)
            came_from[next] = current

    return came_from, cost_so_far
    */

    return [];
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $map = prepareMap($input);

    $start = new Space(0, 0);

    $targetX = array_key_last($map[0]);
    $targetY = array_key_last($map);
    $targetCost = $map[$targetY][$targetX];
    $target = new Space($targetX, $targetY, $targetCost);

    $optimalPath = findOptimalPath($map, $start, $target);

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

$adventHelper->printSolutions(partOne($input), partTwo($input));