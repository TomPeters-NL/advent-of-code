<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/18-test.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Prepare the dig plan in a more useful format.
 *
 * @param string[] $input
 * @return array<int,string>
 */
function prepareDigPlan(array $input): array
{
    $digPlan = [];

    foreach ($input as $instructions) {
        [$direction, $length, $color] = explode(' ', $instructions);

        # Cast length to integer.
        $integerLength = (int)$length;

        # Remove the brackets from the color code.
        $pureColor = preg_replace('/[()]/', '', $color);

        $digPlan[] = [$direction, $integerLength, $pureColor];
    }

    return $digPlan;
}

/**
 * Creates a small grid representing the start of the lagoon and the digger.
 *
 * @return string[][]
 */
function placeDigger(): array
{
    # Create a 3x3 grid of edges.
    $lagoonStart = array_fill(-1, 3, '###');

    # "Place" the digger.
    $lagoonStart[0][0] = '.';

    return $lagoonStart;
}

/**
 * @param array<int,string> $digPlan
 *
 * @return string[][]
 */
function digLagoon(array $digPlan): array
{
    $lagoon = placeDigger();

    # Set starting location.
    $diggerLocation = [0, 0];

    # Diggy, diggy, hole.
    /**
     * @var string $direction
     * @var int $length
     * @var string $color
     */
    foreach ($digPlan as [$direction, $length, $color]) {
        [$x, $y] = $diggerLocation;

        # Increase length by one to properly process the starting/ending edge.
        $inclusiveLength = $length + 1;

        # Determine the coordinates to which the digger should dig.
        [$endX, $endY] = match ($direction) {
            'U' => [$x, $y - $inclusiveLength],
            'D' => [$x, $y + $inclusiveLength],
            'L' => [$x - $inclusiveLength, $y],
            'R' => [$x + $inclusiveLength, $y],
        };

        # Define the values that have to be processed for the lagoon and its edges.
        $stepsX = $endX - $x;
        $rangeX = $stepsX === 0 ? range($x - 1, $x + 1) : range($x, $endX);

        $stepsY = $endY - $y;
        $rangeY = $stepsY === 0 ? range($y - 1, $y + 1) : range($y, $endY);

        # Start the digger, praise the Omnissiah!
        foreach ($rangeY as $digY) {
            foreach ($rangeX as $digX) {
                if (in_array($direction, ['U', 'D']) === true) { # Dig up or down.
                    if ($digX === $x && $digY !== $endY) {
                        $lagoon[$digY][$digX] = '.';
                    } else {
                        $lagoon[$digY][$digX] = $color;
                    }
                } elseif(in_array($direction, ['L', 'R']) === true) { # Dig left or right.
                    if ($digY === $y && $digX !== $endX) {
                        $lagoon[$digY][$digX] = '.';
                    } else {
                        $lagoon[$digY][$digX] = $color;
                    }
                }
            }
        }
    }

    return $lagoon;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $digPlan = prepareDigPlan($input);

    $lagoon = digLagoon($digPlan);

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