<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/21.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Finds the Elf's starting position in garden plot.
 *
 * @param string[] $input The puzzle input.
 *
 * @return array The processed map along with the starting position's X and Y coordinates.
 */
function generateMapAndStartingPosition(array $input): array
{
    $map = [];
    $position = ['x' => 0, 'y' => 0];

    foreach ($input as $y => $line) {
        $split = str_split($line);

        $x = array_search('S', $split);
        if ($x !== false) {
            $position = ['x' => $x, 'y' => $y];
        }

        $map[$y] = $split;
    }

    return [$map, $position];
}

/**
 * Translates a coordinate from the infinite grid back to a coordinate on the original grid.
 *
 * @param int $coordinate The coordinate to be translated.
 * @param int $modulo     Usually the map width or height, depending on the coordinate.
 *
 * @return int The translated coordinate.
 */
function translate(int $coordinate, int $modulo): int
{
    return ($modulo + ($coordinate % $modulo)) % $modulo;
}

/**
 * Generates a list of potential destinations the Elf could end up after reaching his daily step goal.
 *
 * @param string[][] $map           The puzzle input, a map of a garden.
 * @param int[]      $startPosition The starting X and Y coordinates.
 * @param int        $maximumSteps  The amount of steps to be taken.
 *
 * @return int[] The amount of potential positions per step.
 */
function performBreadthFirstSearch(array $map, array $startPosition, int $maximumSteps): array
{
    $mapHeight = count($map);
    $mapWidth = count($map[0]);
    $directions = [[0, -1], [1, 0], [0, 1], [-1, 0]];

    $plots = [];
    $visited = [];
    $queue = [[0, $startPosition]];

    while (empty($queue) === false) {
        [$step, $currentPosition] = array_shift($queue);

        if ($step > $maximumSteps || in_array($currentPosition, $visited) === true) {
            continue;
        }

        array_key_exists($step, $plots) === true ? $plots[$step] += 1 : $plots[$step] = 1;
        $visited[] = $currentPosition;

        foreach ($directions as [$dX, $dY]) {
            $newPosition = ['x' => $currentPosition['x'] + $dX, 'y' => $currentPosition['y'] + $dY];

            $translatedX = ($mapWidth + ($newPosition['x'] % $mapWidth)) % $mapWidth;
            $translatedY = ($mapHeight + ($newPosition['y'] % $mapHeight)) % $mapHeight;

            if ($map[$translatedY][$translatedX] !== '#') {
                $queue[] = [$step + 1, $newPosition];
            }
        }
    }

    return $plots;
}

/**
 * Calculates the amount of possible positions after the maximum amount of steps.
 *
 * @param string[][] $map              The puzzle input, a map of a garden.
 * @param int[]      $startingPosition The starting X and Y coordinates.
 * @param int        $maximumSteps     The maximum amount of steps.
 *
 * @return int The amount of possible positions.
 */
function calculatePositions(array $map, array $startingPosition, int $maximumSteps): int
{
    $gardenPlots = performBreadthFirstSearch($map, $startingPosition, $maximumSteps);

    $positions = 0;
    foreach ($gardenPlots as $step => $count) {
        if ($step % 2 === $maximumSteps % 2) {
            $positions += $count;
        }
    }

    return $positions;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    [$map, $start] = generateMapAndStartingPosition($input);

    return calculatePositions($map, $start, 64);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    [$map, $startPosition] = generateMapAndStartingPosition($input);

    # In order to determine the possible positions at 26,501,365 steps, interpolation is required.
    # The puzzle input is an infinitely repeating pattern of gardens, with direct paths to the edges from the middle (start position).
    # In short, this means that after reaching the edge of the first pattern repetition, the count will expand exponentially.
    # In order to correctly interpolate the goal, three interpolation points will be required:
    #   - u₀, which occurs at the edge of the first repetition.
    #   - u₁, which occurs at the edge of the second repetition.
    #   - u₂, which occurs at the edge of the third repetition.
    #
    # y = ax² + bx + c
    #
    # u₀ = a∙0² + b∙0 + c
    # u₀ = c
    #
    # u₁ = a∙1² + b∙1 + u₀
    # u₁ = a + b + u₀
    # u₁ - u₀ - a = b
    #
    # u₂ = a∙2² + (u₁ - a - u₀)∙2 + u₀
    # u₂ = 4∙a + 2∙u₁ - 2∙a - 2∙u₀ + u₀
    # u₂ - 2∙u₁ + u₀ = 2∙a
    # (u₂ - 2∙u₁ + u₀) ÷ 2 = a
    $mapWidth = count($map[0]);
    $distanceToMapEdge = array_key_last($map[0]) - $startPosition['x'];

    $u0 = calculatePositions($map, $startPosition, ($distanceToMapEdge + 1 * $mapWidth));
    $u1 = calculatePositions($map, $startPosition, ($distanceToMapEdge + 2 * $mapWidth));
    $u2 = calculatePositions($map, $startPosition, ($distanceToMapEdge + 3 * $mapWidth));

    $a = ($u2 - (2 * $u1) + $u0) / 2;
    $b = $u1 - $u0 - $a;
    $c = $u0;
    $n = (26501365 - $distanceToMapEdge) / $mapWidth;

    return ($a * ($n ** 2)) + ($b * $n) + $c;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));