<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/18', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * The first index on the 100 x 100 grid.
 */
const GRID_START = 0;

/**
 * The last index on the 100 x 100 grid.
 */
const GRID_END = 99;

/**
 * Transforms the input grid by splitting each line into its own array of X coordinates/characters.
 *
 * @param string[] $input The puzzle input.
 * @param bool $frozenLights Indicates whether the lights in the corners are stuck in the "on" state.
 *
 * @return string[][] The light grid.
 */
function prepareGrid(array $input, bool $frozenLights = false): array
{
    $grid = [];

    foreach ($input as $line) {
        $grid[] = str_split($line);
    }

    if ($frozenLights === true) {
        $grid[GRID_START][GRID_START] = '#';
        $grid[GRID_START][GRID_END] = '#';
        $grid[GRID_END][GRID_START] = '#';
        $grid[GRID_END][GRID_END] = '#';
    }

    return $grid;
}

/**
 * Calculates the amount of lights that are turned on around a central light.
 *
 * @param array $grid The grid on which the neighbouring lights' states should be checked.
 * @param int   $x    The X coordinate of the current light.
 * @param int   $y    The Y coordinate of the current light.
 *
 * @return int The number of neighbouring lights that are turned on.
 */
function checkNeighbours(array $grid, int $x, int $y): int
{
    $on = 0;

    $potentialNeighbours = [
        'N' => [$x, $y - 1],
        'NE' => [$x + 1, $y - 1],
        'E' => [$x + 1, $y],
        'SE' => [$x + 1, $y + 1],
        'S' => [$x, $y + 1],
        'SW' => [$x - 1, $y + 1],
        'W' => [$x - 1, $y],
        'NW' => [$x - 1, $y - 1],
    ];

    if ($x === GRID_START) {
        unset($potentialNeighbours['NW']);
        unset($potentialNeighbours['W']);
        unset($potentialNeighbours['SW']);
    }

    if ($x === GRID_END) {
        unset($potentialNeighbours['NE']);
        unset($potentialNeighbours['E']);
        unset($potentialNeighbours['SE']);
    }

    if ($y === GRID_START) {
        unset($potentialNeighbours['NW']);
        unset($potentialNeighbours['N']);
        unset($potentialNeighbours['NE']);
    }

    if ($y === GRID_END) {
        unset($potentialNeighbours['SW']);
        unset($potentialNeighbours['S']);
        unset($potentialNeighbours['SE']);
    }

    foreach ($potentialNeighbours as [$nX, $nY]) {
        if ($grid[$nY][$nX] === '#') {
            $on++;
        }
    }

    return $on;
}

/**
 * Checks whether the specified light is in a corner of the light grid.
 *
 * @param int $x The X coordinate of the light.
 * @param int $y The Y coordinate of the light.
 *
 * @return bool Indicates whether the light is in the corner of the grid.
 */
function isCornerLight(int $x, int $y): bool
{
    return ($x === GRID_START && $y === GRID_START)
        || ($x === GRID_START && $y === GRID_END)
        || ($x === GRID_END && $y === GRID_START)
        || ($x === GRID_END && $y === GRID_END);
}

/**
 * Calculates the amount of lights that are turned on after the specified number of iterations (steps) for the provided grid.
 *
 * @param string[][] $grid  The puzzle input, a 100 x 100 grid of lights that can either be turned on (#) or off (.).
 * @param int        $steps The amount of iterations the grid should undergo.
 * @param bool $frozenLights Indicates whether the lights in the corners are stuck in the "on" state.
 *
 * @return int The amount of lights that are turned on after the final iteration.
 */
function animateGrid(array $grid, int $steps, bool $frozenLights = false): int
{
    # The amount of lights that are turned on.
    $on = 0;

    # Initialize the grid variable used for each iteration.
    $currentGrid = $grid;

    for ($step = 0; $step < $steps; $step++) {
        # Reset the lights on count.
        $on = 0;

        # Create a new grid for the next state of the light grid.
        $newGrid = [];

        for ($y = GRID_START; $y <= GRID_END; $y++) {
            for ($x = GRID_START; $x <= GRID_END; $x++) {
                # Retrieve the current light state and the number of neighbouring lights that are turned on.
                $state = $currentGrid[$y][$x];
                $neighboursOn = checkNeighbours($currentGrid, $x, $y);

                if ($frozenLights === true && isCornerLight($x, $y) === true) {
                    $state = '#';
                } elseif ($state === '.' && $neighboursOn === 3) {
                    # A light which is off turns on if exactly 3 neighbors are on, and stays off otherwise.
                    $state = '#';
                } elseif ($state === '#' && in_array($neighboursOn, [2, 3]) !== true) {
                    # A light which is on stays on when 2 or 3 neighbors are on, and turns off otherwise.
                    $state = '.';
                }

                # If the light is turned on, increase the lights on counter.
                if ($state === '#') {
                    $on++;
                }

                # Add the new state to the new grid.
                $newGrid[$y][$x] = $state;
            }
        }

        # Update the current grid for the next loop.
        $currentGrid = $newGrid;
    }

    return $on;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $grid = prepareGrid($input);

    return animateGrid($grid, 100);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $grid = prepareGrid($input, true);

    return animateGrid($grid, 100, true);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));