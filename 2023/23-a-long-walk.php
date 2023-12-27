<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/23', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Find the starting coordinates for the puzzle input.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[] The X and Y coordinates of the starting position.
 */
function findStartPosition(array $input): array
{
    $x = strpos($input[0], '.');

    return [$x, 0];
}

/**
 * Find the ending coordinates for the puzzle input.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[] The X and Y coordinates of the end position.
 */
function findEndPosition(array $input): array
{
    $lastLine = array_key_last($input);

    $x = strpos($input[$lastLine], '.');

    return [$x, $lastLine];
}

/**
 * Identifies valid next steps for the forest hike.
 *
 * @param string[] $map              The forest map.
 * @param int[]    $currentPosition  The X and Y coordinates of the current position.
 * @param int[]    $previousPosition The X and Y coordinates of the previous position.
 *
 * @return int[] A list of X and Y coordinates of valid next steps.
 */
function findNextSteps(array $map, array $currentPosition, array $previousPosition, bool $slipperySlopes = true): array
{
    $steps = [];

    # Define all potential directions.
    $directions = [[0, -1], [1, 0], [0, 1], [-1, 0]];

    [$x, $y] = $currentPosition;
    foreach ($directions as [$dX, $dY]) {
        $nX = $x + $dX;
        $nY = $y + $dY;

        # Check if the new coordinates are within the map boundaries.
        if ($nX < 0 || $nX >= strlen($map[0]) || $nY < 0 || $nY >= count($map)) {
            continue;
        }

        $currentType = $map[$y][$x];
        $nextType = $map[$nY][$nX];

        $isForest = $nextType === '#';
        $isBackwards = [$nX, $nY] === $previousPosition;

        if ($isForest === true || $isBackwards === true) {
            continue;
        }

        $isOneWayNorth = in_array('^', [$currentType, $nextType]) === true && $dY !== -1;
        $isOneWayEast = in_array('>', [$currentType, $nextType]) === true && $dX !== 1;
        $isOneWaySouth = in_array('v', [$currentType, $nextType]) === true && $dY !== 1;
        $isOneWayWest = in_array('<', [$currentType, $nextType]) === true && $dX !== -1;

        if ($slipperySlopes === true && ($isOneWayNorth === true || $isOneWayEast === true || $isOneWaySouth === true || $isOneWayWest === true)) {
            continue;
        }

        $steps[] = [$nX, $nY];
    }

    return $steps;
}

/**
 * Find the lengths of potential paths through the forest.
 *
 * @param string[] $map   The forest map.
 * @param int[]    $start The X and Y coordinates of the starting position.
 * @param int[]    $end   The X and Y coordinates of the ending position.
 *
 * @return int[] The lengths of potential forest paths.
 */
function findPaths(array $map, array $start, array $end): array
{
    # The list keeping track of the lengths of forest routes.
    $paths = [];

    # The pathfinding queue: [[x, y], [previous x, previous y], path length]
    $queue = [];

    # Initialize the start of the path.
    $queue[] = [$start, $start, 0];

    while (empty($queue) === false) {
        # Get the current position.
        [[$x, $y], [$pX, $pY], $length] = array_shift($queue);

        # If the end of the forest has been reached, log the route's length.
        if ([$x, $y] === $end) {
            $paths[] = $length;
            continue;
        }

        # Find the next possible steps from the current position.
        $nextSteps = findNextSteps($map, [$x, $y], [$pX, $pY]);

        # Add each possible step to the queue.
        foreach ($nextSteps as [$nX, $nY]) {
            $nextStep = [[$nX, $nY], [$x, $y], $length + 1];

            $queue[] = $nextStep;
        }
    }

    return $paths;
}

/**
 * Maps the distances between all intersections bidirectionally.
 *
 * @param string[] $map   The forest map.
 * @param int[]    $start The X and Y coordinates of the starting position.
 * @param int[]    $end   The X and Y coordinates of the ending position.
 *
 * @return int[][] A list of path lengths between pairs of intersections.
 */
function mapIntersections(array $map, array $start, array $end): array
{
    # The list tracking intersections and the distance between them.
    $intersections = [];

    # The pathfinding queue: [[x, y], [previous x, previous y], [intersection x, intersection y], path length]
    $queue = [];

    # Initialize the start of the path.
    $queue[] = [$start, $start, $start, 0];

    while (empty($queue) === false) {
        # Get the current position.
        [[$x, $y], [$pX, $pY], [$iX, $iY], $length] = array_shift($queue);

        # Find the next possible steps from the current position.
        $nextSteps = findNextSteps($map, [$x, $y], [$pX, $pY], false);

        # See whether the current position is an intersection (or the end position).
        $isIntersection = count($nextSteps) > 1 || [$x, $y] === $end;
        if ($isIntersection === true) {
            # Log the distance between the nodes.
            $intersections["$iX,$iY"]["$x,$y"] = $length;

            # Reset the intersection details and path length to the new, current intersection.
            [$niX, $niY, $newLength] = [$x, $y, 1];
        } else {
            # Keep mapping the current path to the next intersection.
            [$niX, $niY, $newLength] = [$iX, $iY, $length + 1];
        }

        # Add each possible step to the queue.
        foreach ($nextSteps as [$nX, $nY]) {
            $nextStep = [[$nX, $nY], [$x, $y], [$niX, $niY], $newLength];

            # Only keep going if this path has not been mapped yet.
            if (isset($intersections["$iX,$iY"]["$nX,$nY"]) === false) {
                $queue[] = $nextStep;
            }
        }
    }

    return $intersections;
}

/**
 * @param int[][] $intersections A list of path lengths between pairs of intersections.
 * @param int[]   $start         The X and Y coordinates of the starting position.
 * @param int[]   $end           The X and Y coordinates of the ending position.
 *
 * @return int[] A list of paths and their lengths.
 */
function generatePathsFromIntersections(array $intersections, array $start, array $end): array
{
    # Create the keys used to recognize the start and end points of the path.
    $startKey = "$start[0],$start[1]";
    $endKey = "$end[0],$end[1]";

    # Initiate the paths list and initialize it with the first intersections.
    $paths = [];
    foreach ($intersections[$startKey] as $intersection => $pathLength) {
        $newPath = $startKey . '->' . $intersection;
        $paths[$newPath] = $pathLength;
    }

    $pathfinding = true;
    while ($pathfinding === true) {
        $newPaths = [];

        foreach ($paths as $path => $totalPathLength) {
            # Retrieve the intersections visited so far.
            $progress = explode('->', $path);
            $latestIntersection = $progress[array_key_last($progress)];

            # Extract a list of reachable intersections from the current position.
            $nextIntersections = $intersections[$latestIntersection] ?? [];

            # Only proceed with pathfinding if there are reachable intersections left and the end hasn't been reached.
            if (empty($nextIntersections) === false && $latestIntersection !== $endKey) {
                # If the end position is an option, only that option matters, as retreading is not allowed.
                $hasEndPosition = in_array($endKey, $nextIntersections);

                # Generate all possible paths to take without retreading old ones.
                foreach ($nextIntersections as $intersection => $pathLength) {
                    # Prevent circular (infinite) paths.
                    if (in_array($intersection, $progress) === true) {
                        continue;
                    }

                    # Prevent not taking the path to the end when available.
                    if ($hasEndPosition === true && $intersection !== $endKey) {
                        continue;
                    }

                    # Generate the new path and calculate the paths length so far.
                    $newPath = $path . '->' . $intersection;
                    $newPaths[$newPath] = $totalPathLength + $pathLength;
                }

                # Remove the old, incomplete path from the dataset.
                unset($paths[$path]);
            }
        }

        # If there are no more path updates, stop generation.
        if (empty($newPaths) === true) {
            $pathfinding = false;
        }

        # Update the paths list.
        $paths = array_merge($paths, $newPaths);
    }

    return $paths;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $start = findStartPosition($input);
    $end = findEndPosition($input);

    $paths = findPaths($input, $start, $end);

    return max($paths);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $start = findStartPosition($input);
    $end = findEndPosition($input);

    $intersections = mapIntersections($input, $start, $end);
    $paths = generatePathsFromIntersections($intersections, $start, $end);

    $relevantPaths = array_filter(array_keys($paths), fn ($x) => str_ends_with($x, "$end[0],$end[1]"));
    $relevantPathLengths = array_intersect_key($paths, array_flip($relevantPaths));

    return max($relevantPathLengths);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));