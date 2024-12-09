<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/10', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Finds the coordinates of all trailheads on the topographical map.
 *
 * @param string[] $map The puzzle input, a topographical map.
 *
 * @return array<array{x: int, y: int}>
 */
function findTrailheads(array $map): array
{
    $trailheads = [];

    foreach ($map as $y => $line) {
        preg_match_all('/0/', $line, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as [$height, $x]) {
            $trailheads[] = ['x' => $x, 'y' => $y];
        }
    }

    return $trailheads;
}

/**
 * Finds all possible hiking trails and their destinations on the topographical map.
 *
 * @param string[] $map          The topographical map.
 * @param int      $x            The current X coordinate on the map.
 * @param int      $y            The current Y coordinate on the map.
 * @param string[] $destinations The stringified coordinates of possible hike destinations.
 * @param int      $trails       The number of distinct hiking trails leading to any of the destinations.
 *
 * @return void The results are passed by reference to the destinations and trails variables.
 */
function mapHikingTrails(array $map, int $x, int $y, array &$destinations, int &$trails): void
{
    $currentHeight = (int) $map[$y][$x];

    if ($currentHeight === 9) {
        $currentLocation = $x . ',' . $y;

        # Log destinations only once to count the number of possible destinations, regardless of the trail hiked.
        if (!in_array($currentLocation, $destinations)) {
            $destinations[] = $currentLocation;
        }

        # Increase the trails counter as this is the end of another distinct trail.
        $trails++;

        return;
    }

    $maximumX = strlen($map[0]);
    $maximumY = count($map);
    $targetHeight = $currentHeight + 1;

    foreach ([[0, -1], [0, 1], [-1, 0], [1, 0]] as [$dX, $dY]) {
        $nX = $x + $dX;
        $nY = $y + $dY;

        # Continue with the next value if the current position is out of bounds.
        if ($nX < 0 || $nX >= $maximumX || $nY < 0 || $nY >= $maximumY) {
            continue;
        }

        $nextHeight = (int) $map[$nY][$nX];

        # If the next step would be an even, gradual, uphill slope, keep following the trail.
        if ($nextHeight === $targetHeight) {
            mapHikingTrails($map, $nX, $nY, $destinations, $trails);
        }
    }
}

/**
 * Returns the solutions for the first and second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[]
 */
function partOneAndTwo(array $input): array
{
    $trailheads = findTrailheads($input);

    $trails = 0;
    $destinationCount = 0;
    $destinations = [];

    foreach ($trailheads as ['x' => $x, 'y' => $y]) {
        mapHikingTrails($input, $x, $y, $destinations, $trails);

        $destinationCount += count($destinations);

        # Empty the list of possible destinations before mapping the next hiking trails.
        $destinations = [];
    }

    return [$destinationCount, $trails];
}

###############
### Results ###
###############

$adventHelper->printSolutions(...partOneAndTwo($input));