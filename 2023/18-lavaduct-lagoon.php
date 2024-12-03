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
 * Prepare the dig plan in a more useful format.
 *
 * @param string[] $input
 *
 * @return array<int,string>
 */
function prepareDigPlan(array $input, bool $theElvesAreIdiots = false): array
{
    $digPlan = [];

    foreach ($input as $instructions) {
        [$direction, $length, $color] = explode(' ', $instructions);

        if ($theElvesAreIdiots === false) {
            $digPlan[] = [$direction, (int)$length];
        } else {
            # Remove unnecessary from the hexadecimal string.
            $pureColor = preg_replace('/[#()]/', '', $color);

            # Convert the hexadecimal digits to integer length.
            $hexadecimal = substr($pureColor, 0, 5);
            $length = (int)hexdec($hexadecimal);

            # Convert the last hexadecimal digit to a direction.
            $directionDigit = (int)substr($pureColor, 5, 1);
            $direction = match ($directionDigit) {
                0 => 'R',
                1 => 'D',
                2 => 'L',
                3 => 'U',
            };

            $digPlan[] = [$direction, $length];
        }
    }

    return $digPlan;
}

/**
 * Creates a map of the lagoon corners as dug per the dig plan instructions.
 *
 * @param array<int,string> $digPlan
 *
 * @return array<array<string,bool>>
 */
function determineLagoonCorners(array $digPlan): array
{
    $lagoonCorners = [];

    # Set starting location.
    $diggerLocation = [0, 0];

    # Diggy, diggy, hole.
    foreach ($digPlan as [$direction, $length]) {
        [$x, $y] = $diggerLocation;

        [$endX, $endY] = match ($direction) {
            'U' => [$x, $y - $length],
            'D' => [$x, $y + $length],
            'L' => [$x - $length, $y],
            'R' => [$x + $length, $y],
        };

        # Log the lagoon corner's coordinates and color.
        $lagoonCorners[] = [$endX, $endY];

        # Update the digger's coordinates.
        $diggerLocation = [$endX, $endY];
    }

    return $lagoonCorners;
}

/**
 * Calculates the lagoon volume using the Shoelace formula.
 *
 * @param array<int,string> $digPlan
 *
 * @return int
 */
function calculateLagoonVolume(array $digPlan): int
{
    # Retrieve the corner coordinates for the lagoon polygon.
    $lagoonCorners = determineLagoonCorners($digPlan);

    # As the Shoelace formula is circular, copy the first item to the end.
    $firstCornerKey = array_key_first($lagoonCorners);
    $lagoonCorners[] = $lagoonCorners[$firstCornerKey];

    # Calculate the area inside the corners.
    $shoelace = 0;
    foreach ($lagoonCorners as $index => [$x, $y]) {
        [$nextX, $nextY] = $lagoonCorners[$index + 1] ?? [0, 0, ''];

        $shoelace += $x * $nextY - $y * $nextX;
    }
    $shoelace /= 2;

    # The Shoelace formula calculates the area of the lagoon from the middle of its edges.
    # Currently, it's missing:
    #     1. Half of the non-corner perimeter squares.
    #     2. A quarter of all inside corner blocks.
    #     3. Three quarters of all outside corner blocks.
    #
    # This modifies the area formula to: area = shoelace + 0.5 * non-corners + 0.25 * inside-corners + 0.75 * outside-corners.
    #
    # Excluding the 4 outside corners of the polygons, each outside corner is matched by an inside corner.
    # This means we can simplify the corner area (excluding the 4 "outer" corners) to 0.75 - 0.25 = 0.5 * corners.
    # Furthermore, treating the 4 "outer" corners as non-corners leaves us with 4 * (0.75 - 0.5) = 4 * 0.25 = 1.
    #
    # This means we can simplify the extended Shoelace formula as follows:
    #    1. area = shoelace + 0.5 * non-corners + 0.25 * inside-corners + 0.75 * outside-corners.
    #    2. area = shoelace + 0.5 * non-corners + 0.5 * corners + 1.
    #    3. area = shoelace + 0.5 * (non-corners + corners) + 1.
    #    4. area = shoelace + 0.5 * perimeter + 1.
    $perimeterArea = array_reduce($digPlan, fn($area, $plan) => $area + $plan[1]);

    return $shoelace + 0.5 * $perimeterArea + 1;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $digPlan = prepareDigPlan($input);

    return calculateLagoonVolume($digPlan);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $digPlan = prepareDigPlan($input, true);

    return calculateLagoonVolume($digPlan);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));