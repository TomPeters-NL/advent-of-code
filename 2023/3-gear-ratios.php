<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/3.txt');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $numbers = $symbols = [];
    foreach ($input as $y => $row) {
        preg_match_all('/\d+/', $row, $matches);
        foreach ($matches[0] as $number) {
            $x = strpos($row, $number);

            $length = strlen($number);
            $row = substr_replace($row, str_repeat('.', $length), $x, $length);

            $coordinates = $x . ',' . $y;
            $numbers[$coordinates] = $length;
        }

        preg_match_all('/[^.\d\s]/', $row, $matches);
        foreach ($matches[0] as $symbol) {
            $x = strpos($row, $symbol);
            $row = substr_replace($row, '.', $x, 1);

            $symbols[$y][] = $x;
        }
    }

    $sizeX = strlen($input[0]) - 1;
    $sizeY = count($input) - 1;
    $partSum = 0;
    foreach ($numbers as $coordinates => $length) {
        $explodedCoordinates = explode(',', $coordinates);
        $x = (int) $explodedCoordinates[0];
        $y = (int) $explodedCoordinates[1];

        $minimumX = $x - 1 >= 0 ? $x - 1 : $x;
        $maximumX = $x + $length <= $sizeX ? $x + $length : $sizeX;
        $minimumY = $y - 1 >= 0 ? $y - 1 : $y;
        $maximumY = $y + 1 <= $sizeY ? $y + 1 : $sizeY;

        for ($a = $minimumY; $a <= $maximumY; $a++) {
            for ($b = $minimumX; $b <= $maximumX; $b++) {
                if (array_key_exists($a, $symbols) === true && in_array($b, $symbols[$a]) === true) {
                    $partSum += (int) substr($input[$y], $x, $length);
                    break 2;
                }
            }
        }
    }

    return $partSum;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $numbers = $asterisks = [];
    foreach ($input as $y => $row) {
        preg_match_all('/\d+/', $row, $matches);
        foreach ($matches[0] as $number) {
            $x = strpos($row, $number);

            $length = strlen($number);
            $row = substr_replace($row, str_repeat('.', $length), $x, $length);

            $coordinates = $x . ',' . $y;
            $numbers[$coordinates] = $length;
        }

        preg_match_all('/\*/', $row, $matches);
        foreach ($matches[0] as $asterisk) {
            $x = strpos($row, $asterisk);
            $row = substr_replace($row, '.', $x, 1);

            $asterisks[$y][] = $x;
        }
    }

    $sizeX = strlen($input[0]) - 1;
    $sizeY = count($input) - 1;
    $potentialGears = [];
    foreach ($numbers as $coordinates => $length) {
        $explodedCoordinates = explode(',', $coordinates);
        $x = (int) $explodedCoordinates[0];
        $y = (int) $explodedCoordinates[1];

        $minimumX = $x - 1 >= 0 ? $x - 1 : $x;
        $maximumX = $x + $length <= $sizeX ? $x + $length : $sizeX;
        $minimumY = $y - 1 >= 0 ? $y - 1 : $y;
        $maximumY = $y + 1 <= $sizeY ? $y + 1 : $sizeY;

        for ($a = $minimumY; $a <= $maximumY; $a++) {
            for ($b = $minimumX; $b <= $maximumX; $b++) {
                if (array_key_exists($a, $asterisks) === true && in_array($b, $asterisks[$a]) === true) {
                    $potentialGearCoordinates = $b . ',' . $a;
                    $number = (int) substr($input[$y], $x, $length);

                    $potentialGears[$potentialGearCoordinates][] = $number;
                }
            }
        }
    }

    $gearRatioSum = 0;
    foreach ($potentialGears as $coordinates => $numbers) {
        if (count($numbers) === 2) {
            $gearRatioSum += array_product($numbers);
        }
    }

    return $gearRatioSum;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));