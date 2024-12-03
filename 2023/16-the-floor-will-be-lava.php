<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/16', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Calculates the path of the light as it travels through the contraption.
 *
 * @param string[] $contraptionMap
 * @param array<int,string> $initialStep
 *
 * @return string[]
 */
function mapLightTraversal(array $contraptionMap, array $initialStep): array
{
    # Initialize the map, cache, and queue.
    $energyMap = $lightCache = [];
    $directionQueue = [$initialStep];

    # Calculate the map dimensions.
    $mapLength = count($contraptionMap);
    $mapWidth = strlen($contraptionMap[0]);

    while (empty($directionQueue) === false) {
        # Retrieve the current position and direction.
        [$x, $y, $direction] = array_shift($directionQueue);

        # Log the contraption tile as energized.
        $coordinate = "$x,$y";
        $energyMap[$coordinate] = ':)';

        # Check if the coordinates and direction are already in the cache.
        # If so, skip this step to prevent an infinite loop.
        # If not, add them to the cache.
        $directionCoordinate = "$coordinate,$direction";
        if (in_array($directionCoordinate, $lightCache) === true) {
            continue;
        } else {
            $lightCache[] = $directionCoordinate;
        }

        # Determine the current position and determine the new direction(s).
        $currentSpace = $contraptionMap[$y][$x];
        $newDirections = match ($currentSpace) {
            '-' => match ($direction) {
                'S', 'N' => ['W', 'E'],
                default => [$direction]
            },
            '|' => match ($direction) {
                'W', 'E' => ['S', 'N'],
                default => [$direction]
            },
            '/' => match ($direction) {
                'S' => ['W'],
                'N' => ['E'],
                'W' => ['S'],
                'E' => ['N']
            },
            '\\' => match ($direction) {
                'S' => ['E'],
                'N' => ['W'],
                'W' => ['N'],
                'E' => ['S']
            },
            default => [$direction],
        };

        # Based on the new direction(s), calculate the new coordinates.
        foreach ($newDirections as $newDirection) {
            [$x, $y] = match ($newDirection) {
                'S' => [$x, $y + 1],
                'N' => [$x, $y - 1],
                'W' => [$x - 1, $y],
                'E' => [$x + 1, $y],
            };

            # Check if the new coordinates are actually on the map.
            if ($y >= 0 && $y < $mapLength && $x >= 0 && $x < $mapWidth) {
                $directionQueue[] = [$x, $y, $newDirection];
            }
        }
    }

    return $energyMap;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $lightMap = mapLightTraversal($input, [0, 0, 'E']);

    return count($lightMap);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $mapRows = count($input);
    $mapColumns = strlen($input[0]);

    $energyConfigurations = [];

    # For each row of the map, calculate the light's path from both edges.
    for ($row = 0; $row < $mapRows; $row++) {
        $eastwardEnergyMap = mapLightTraversal($input, [0, $row, 'E']);
        $energyConfigurations[] = count($eastwardEnergyMap);

        $westwardEnergyMap = mapLightTraversal($input, [$mapColumns - 1, $row, 'W']);
        $energyConfigurations[] = count($westwardEnergyMap);
    }

    # For each column of the map, calculate the light's path from both edges.
    for ($column = 0; $column < $mapColumns; $column++) {
        $southwardEnergyMap = mapLightTraversal($input, [$column, 0, 'S']);
        $energyConfigurations[] = count($southwardEnergyMap);

        $northwardEnergyMap = mapLightTraversal($input, [$column, $mapRows - 1, 'N']);
        $energyConfigurations[] = count($northwardEnergyMap);
    }

    return max($energyConfigurations);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));