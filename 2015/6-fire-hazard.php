<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/6');

#################
### Solutions ###
#################

/**
 * @return string[]
 */
function createGrid(): array
{
    $gridX = range(0, 999);
    $gridY = array_fill(0, 1000, 0);

    return array_fill_keys($gridX, $gridY);
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $grid = createGrid();

    foreach ($input as $instruction) {
        preg_match('/^(\D+) (\d+),(\d+) through (\d+),(\d+)$/', $instruction, $components);
        list($instruction, $command, $startX, $startY, $endX, $endY) = $components;

        $rangeX = range($startX, $endX);
        $rangeY = range($startY, $endY);

        switch ($command) {
            case 'turn on':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = 1;
                    }
                }

                break;
            case 'turn off':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = 0;
                    }
                }

                break;
            default:
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = !$grid[$x][$y];
                    }
                }
        }
    }

    $lit = 0;
    foreach ($grid as $x) {
        $lit += array_sum($x);
    }

    return $lit;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $grid = createGrid();

    foreach ($input as $instruction) {
        preg_match('/^(\D+) (\d+),(\d+) through (\d+),(\d+)$/', $instruction, $components);
        list($instruction, $command, $startX, $startY, $endX, $endY) = $components;

        $rangeX = range($startX, $endX);
        $rangeY = range($startY, $endY);

        switch ($command) {
            case 'turn on':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y]++;
                    }
                }

                break;
            case 'turn off':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        if ($grid[$x][$y] > 0) {
                            $grid[$x][$y]--;
                        }
                    }
                }

                break;
            default:
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = $grid[$x][$y] + 2;
                    }
                }
        }
    }

    $totalBrightness = 0;
    foreach ($grid as $x) {
        $totalBrightness += array_sum($x);
    }

    return $totalBrightness;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));
