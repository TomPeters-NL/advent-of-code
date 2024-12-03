<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/1');

#################
### Solutions ###
#################

/**
 * Converts the string of directions to the destination into an array.
 *
 * @param string[] $input The puzzle input.
 *
 * @return string[] The list of directions towards Easter Bunny Headquarters.
 */
function listDirections(array $input): array
{
    return explode(', ', trim($input[0]));
}

/**
 * Provides the direction one would face after making the provided turn.
 *
 * @param string $facing The current travel direction.
 * @param string $turn The left or right turn to be made.
 *
 * @return string The new travel direction.
 */
function getNewFacing(string $facing, string $turn): string
{
    return match (true) {
        $facing === 'north' && $turn === 'R', $facing === 'south' && $turn === 'L' => 'east',
        $facing === 'east' && $turn === 'R', $facing === 'west' && $turn === 'L' => 'south',
        $facing === 'south' && $turn === 'R', $facing === 'north' && $turn === 'L' => 'west',
        $facing === 'west' && $turn === 'R', $facing === 'east' && $turn === 'L' => 'north',
    };
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $directions = listDirections($input);

    $position = ['x' => 0, 'y' => 0];
    $facing = 'north';

    foreach ($directions as $direction) {
        $turn = substr($direction, 0, 1);
        $steps = (int) substr($direction, 1);

        $facing = getNewFacing($facing, $turn);

        switch ($facing) {
            case 'north':
                $position['y'] += $steps;
                break;
            case 'east':
                $position['x'] += $steps;
                break;
            case 'south':
                $position['y'] -= $steps;
                break;
            case 'west':
                $position['x'] -= $steps;
                break;
        }
    }

    return abs($position['x']) + abs($position['y']);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $directions = listDirections($input);

    $trackingLog = ['0,0'];
    $position = ['x' => 0, 'y' => 0];
    $facing = 'north';

    foreach ($directions as $direction) {
        $turn = substr($direction, 0, 1);
        $steps = (int) substr($direction, 1);

        $facing = getNewFacing($facing, $turn);

        for ($n = $steps; $n > 0; $n--) {
            switch ($facing) {
                case 'north':
                    $position['y']++;
                    break;
                case 'east':
                    $position['x']++;
                    break;
                case 'south':
                    $position['y']--;
                    break;
                case 'west':
                    $position['x']--;
                    break;
            }

            $trackablePosition = implode(',', $position);

            if (in_array($trackablePosition, $trackingLog)) {
                break 2;
            }

            $trackingLog[] = $trackablePosition;
        }
    }

    return abs($position['x']) + abs($position['y']);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));