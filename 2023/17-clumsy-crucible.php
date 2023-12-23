<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/17.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Prepares the input map for pathfinding.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] The heat loss map split into X and Y axes.
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
 * Helps to find the direct opposite wind direction of the one provided.
 *
 * @param string $direction The direction for which the opposite has to be found.
 *
 * @return string The opposite direction.
 */
function getOppositeDirection(string $direction): string
{
    return match ($direction) {
        'N' => 'S',
        'E' => 'W',
        'S' => 'N',
        'W' => 'E',
        default => 'NA',
    };
}

/**
 * Finds the most optimal path to the machine parts factory.
 *
 * @param int[][] $map                 The heat loss map of the city.
 * @param int[]   $lavaPool            The coordinates of the starting point.
 * @param int[]   $machinePartsFactory The coordinates of the endpoint.
 * @param int     $minimumSteps        The minimum amount of steps before turning is allowed.
 * @param int     $maximumSteps        The maximum amount of steps without turning are allowed.
 *
 * @return int The optimal cumulative heat loss for reaching the destination.
 */
function findPath(array $map, array $lavaPool, array $machinePartsFactory, int $minimumSteps, int $maximumSteps): int
{
    # Determine the city map limits.
    $minX = array_key_first($map);
    $maxX = array_key_last($map);
    $minY = array_key_first($map[0]);
    $maxY = array_key_last($map[0]);

    # Track which city blocks have been visited.
    $visited = [];

    # Track which city blocks to explore.
    $queue = new SplMinHeap();

    # Configure the starting location: [cumulative heat loss, [x, y, direction, step count]].
    [$startX, $startY] = $lavaPool;
    $queue->insert([0, [$startX, $startY, 'E', 0]]);
    $queue->insert([0, [$startX, $startY, 'S', 0]]);

    # Configure the potential turns per block.
    $directions = [
        'N' => [0, -1],
        'E' => [1, 0],
        'S' => [0, 1],
        'W' => [-1, 0],
    ];

    while ($queue->isEmpty() === false) {
        # Extract the city block with the highest priority.
        [$CHL, [$x, $y, $direction, $steps]] = $queue->extract();

        # Check if the destination has been reached.
        if ([$x, $y] === $machinePartsFactory && $steps >= $minimumSteps) {
            return $CHL;
        }

        foreach ($directions as $newDirection => [$dX, $dY]) {
            # Determine the new block coordinates.
            $newX = $x + $dX;
            $newY = $y + $dY;

            # Check if the new block is either out of bounds or moving backwards.
            $isOutOfBounds = $newX < $minX || $newX > $maxX || $newY < $minY || $newY > $maxY;
            $isMovingBackwards = $newDirection === getOppositeDirection($direction);
            if ($isOutOfBounds === true || $isMovingBackwards === true) {
                continue;
            }

            # Calculate the new step count.
            $newSteps = $newDirection === $direction ? $steps + 1 : 1;

            # Check whether this relocation adheres to the minimum and maximum step limits.
            $exceedsMaximum = $newSteps > $maximumSteps;
            $underMinimum = $newDirection !== $direction && $steps < $minimumSteps;
            if ($exceedsMaximum === true || $underMinimum === true) {
                continue;
            }

            # Calculate the new cumulative heat loss in the new city block.
            $newCHL = $CHL + $map[$newY][$newX];

            # Check whether this new step is an improvement over previous ones. If so, log it and add it to the queue.
            $blockKey = "$newX-$newY-$newDirection-$newSteps";
            if ($newCHL < ($visited[$blockKey] ?? INF)) {
                $visited[$blockKey] = $newCHL;
                $queue->insert([$newCHL, [$newX, $newY, $newDirection, $newSteps]]);
            }
        }
    }

    return -1;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    # Process the heat (loss) map.
    $map = prepareMap($input);

    # The starting and ending city blocks: [x, y].
    $lavaPool = [0, 0];
    $machinePartFactory = [array_key_last($map), array_key_last($map[0])];

    return findPath($map, $lavaPool, $machinePartFactory, 0, 3);
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    # Process the heat (loss) map.
    $map = prepareMap($input);

    # The starting and ending city blocks: [x, y].
    $lavaPool = [0, 0];
    $machinePartFactory = [array_key_last($map), array_key_last($map[0])];

    return findPath($map, $lavaPool, $machinePartFactory, 4, 10);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));