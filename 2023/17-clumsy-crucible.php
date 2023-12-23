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
 * Finds the most optimal path to the machine parts factory.
 *
 * @param int[][] $map                 The heat loss map of the city.
 * @param int[]   $lavaPool            The coordinates of the starting point.
 * @param int[]   $machinePartsFactory The coordinates of the endpoint.
 * @param int     $minimumDistance     The minimum amount of steps before turning is allowed.
 * @param int     $maximumDistance     The maximum amount of steps without turning are allowed.
 *
 * @return int The optimal cumulative heat loss for reaching the destination.
 */
function findPath(array $map, array $lavaPool, array $machinePartsFactory, int $minimumDistance, int $maximumDistance): int
{
    # Determine the city map limits.
    $minX = array_key_first($map);
    $maxX = array_key_last($map);
    $minY = array_key_first($map[0]);
    $maxY = array_key_last($map[0]);

    # Track which city blocks to explore.
    $queue = new SplMinHeap();

    # Configure the starting location: [heuristic priority, [cumulative heat loss, x, y, previous x, previous y, delta X, delta Y, distance]].
    [$startX, $startY] = $lavaPool;
    $queue->insert([0, [0, $startX, $startY, 0, 0, 1, 0, 1]]);
    $queue->insert([0, [0, $startX, $startY, 0, 0, 0, 1, 1]]);

    # Track which city blocks have been visited.
    $visited = [];

    while ($queue->isEmpty() === false) {
        # Retrieve the current location.
        [$heuristicPriority, [$CHL, $currentX, $currentY, $previousX, $previousY, $dX, $dY, $distance]] = $queue->extract();

        # Check if the goal location has been reached.
        if ([$currentX, $currentY] === $machinePartsFactory && $distance > $minimumDistance) {
            return $CHL;
        }

        # Check if the location has been visited before.
        $blockSignature = "$currentX-$currentY-$dX-$dY-$distance";
        if (in_array($blockSignature, $visited) === true) {
            continue;
        } else {
            # If not, add it to the visited list now.
            $visited[] = $blockSignature;
        }

        # List the potential directions towards other city blocks.
        $potentialDirections = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        # Generate potential neighbouring city blocks.
        foreach ($potentialDirections as [$ndX, $ndY]) {
            # Set the new X and Y coordinates.
            [$newX, $newY] = [$currentX + $ndX, $currentY + $ndY];

            # Validate whether the neighbouring city block is on the map, and the crucible is not moving backwards.
            $isInBounds = $newX >= $minX && $newX <= $maxX && $newY >= $minY && $newY <= $maxY;
            $isPreviousBlock = $newX === $previousX && $newY === $previousY;
            if ($isInBounds === false || $isPreviousBlock === true) {
                continue;
            }

            # Calculate the new cumulative heat loss.
            $newCHL = $CHL + $map[$newX][$newY];
            $heuristicPriority = $newCHL + abs($newX - $machinePartsFactory[0]) + abs($newY - $machinePartsFactory[1]);

            # Determine whether the crucible is moving straight and its new distance traveled.
            $movingForward = $dX === $ndX || $dY === $ndY;

            if ($movingForward === true) {
                if ($distance < $maximumDistance) {
                    $queue->insert([$heuristicPriority, [$newCHL, $newX, $newY, $currentX, $currentY, $ndX, $ndY, $distance + 1]]);
                }
            } elseif ($distance >= $minimumDistance || [$currentX, $currentY] === [0, 0]) {
                $queue->insert([$heuristicPriority, [$newCHL, $newX, $newY, $currentX, $currentY, $ndX, $ndY, 1]]);
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