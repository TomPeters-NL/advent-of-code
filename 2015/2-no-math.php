<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/2.txt');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $squareFeet = 0;
    foreach ($input as $present) {
        list($length, $width, $height) = explode('x', $present);

        $topBottom = $length * $width;
        $frontBack = $width * $height;
        $leftRight = $length * $height;

        $surface = 2 * $topBottom + 2 * $frontBack + 2 * $leftRight;
        $slack = min([$topBottom, $frontBack, $leftRight]);

        $squareFeet += $surface + $slack;
    }

    return $squareFeet;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $ribbonLength = 0;
    foreach ($input as $present) {
        list($length, $width, $height) = explode('x', $present);

        $largestSide = max([$length, $width, $height]);
        $minimalPerimeter = 2 * $length + 2 * $width + 2 * $height - 2 * $largestSide;
        $volume = $length * $width * $height;

        $ribbonLength += $minimalPerimeter + $volume;
    }

    return $ribbonLength;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));