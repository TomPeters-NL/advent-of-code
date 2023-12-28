<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/24', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Analyzes the hailstorm and separates each hailstone into a list of coordinates and velocities.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] The positional coordinates and velocities of each individual hailstone.
 */
function analyzeHailstorm(array $input): array
{
    $hailstorm  = [];

    foreach ($input as $line) {
        [$firstPart, $secondPart] = explode('@', $line);

        [$pX, $pY, $pZ] = array_map(fn ($x) => intval(trim($x)), explode(',', $firstPart));
        [$vX, $vY, $vZ] = array_map(fn ($x) => intval(trim($x)), explode(',', $secondPart));

        $hailstorm[] = [$pX, $pY, $pZ, $vX, $vY, $vZ];
    }

    return $hailstorm;
}

/**
 * Finds all intersection hailstone paths within the test area.
 *
 * @param int[][] $hailstorm The positional coordinates and velocities of each individual hailstone.
 * @param int[] $testArea The X and Y coordinates delimiting the test area.
 *
 * @return int The number of intersecting hailstone paths.
 */
function findIntersects2D(array $hailstorm, array $testArea): int
{
    $intersects = [];

    while (empty($hailstorm) === false) {
        [$pX1, $pY1, $pZ1, $vX1, $vY1, $vZ1] = array_shift($hailstorm);
        $m1 = $vY1 / $vX1;

        foreach ($hailstorm as [$pX2, $pY2, $pZ2, $vX2, $vY2, $vZ2]) {
            $m2 = $vY2 / $vX2;

            if ($m1 === $m2) {
                continue;
            };

            # The formula for a linear line can be described as: y = m ∙ (x - Pₓ) + Pᵧ
            #
            # Here, "m" is the slope, and Pₓ & Pᵧ are a set of known X,Y coordinates.
            # To solve for "x" at an intercept, we can derive the following function.
            #
            #    m₁ ∙ (x - Pₓ₁) + Pᵧ₁ = m₂ ∙ (x - Pₓ₂) + Pᵧ₂
            #    m₁∙x - m₁∙Pₓ₁ + Pᵧ₁ = m₂∙x - m₂∙Pₓ₂ + Pᵧ₂
            #    m₁∙x - m₂∙x = m₂∙x - m₂∙Pₓ₂ + Pᵧ₂ + m₁∙Pₓ₁ - Pᵧ₁
            #    (m₁ - m₂) ∙ x = m₂∙x - m₂∙Pₓ₂ + Pᵧ₂ + m₁∙Pₓ₁ - Pᵧ₁
            #    x = (m₂∙x - m₂∙Pₓ₂ + Pᵧ₂ + m₁∙Pₓ₁ - Pᵧ₁) ÷ (m₁ - m₂)
            #
            $iX = ((-$m2 * $pX2) + $pY2 + ($m1 * $pX1) - $pY1) / ($m1 - $m2);
            $iY = $m1 * ($iX - $pX1) + $pY1;

            # As we are only interested in future intersects, check whether either hailstone is past the intersection point.
            $isPastIntersect = ($vX1 < 0 && $pX1 < $iX)
                || ($vX1 > 0 && $pX1 > $iX)
                || ($vX2 < 0 && $pX2 < $iX)
                || ($vX2 > 0 && $pX2 > $iX)
                || ($vY1 < 0 && $pY1 < $iY)
                || ($vY1 > 0 && $pY1 > $iY)
                || ($vY2 < 0 && $pY2 < $iY)
                || ($vY2 > 0 && $pY2 > $iY);

            if ($isPastIntersect === true) {
                continue;
            }

            $intersects[] = [$iX, $iY];
        }
    }

    return array_reduce($intersects, function ($sum, $intersect) use ($testArea) {
        [$startX, $startY, $endX, $endY] = $testArea;
        [$iX, $iY] = $intersect;

        $inTestArea = $iX >= $startX && $iX <= $endX && $iY >= $startY && $iY  <= $endY;

        return $sum + (int) $inTestArea;
    });
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $hailstorm = analyzeHailstorm($input);

    $testArea = [
        200000000000000,
        200000000000000,
        400000000000000,
        400000000000000,
    ];

    return findIntersects2D($hailstorm, $testArea);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    return 2;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));