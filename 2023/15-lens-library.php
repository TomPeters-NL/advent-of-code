<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/15', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Apply the Holiday ASCII String Helper algorithm.
 */
function runHashAlgorithm(string $sample): int
{
    $fragments = str_split($sample);

    # 1. Get the ASCII value of the current character.
    # 2. Add the current sum to that value.
    # 3. Multiply by 17.
    # 4. Find the remainder of dividing by 256.
    return array_reduce($fragments, fn($sum, $character) => (17 * ($sum + ord($character))) % 256);
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $verificationNumber = 0;

    $sequenceSteps = explode(',', $input[0]);
    foreach ($sequenceSteps as $sequenceStep) {
        $verificationNumber += runHashAlgorithm($sequenceStep);
    }

    return $verificationNumber;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $focusingPower = 0;

    $boxes = $labelCache = [];
    $lensConfigurations = explode(',', $input[0]);

    foreach ($lensConfigurations as $configuration) {
        preg_match('/^([a-z]+)([=-])([0-9]*)$/', $configuration, $matches);
        list($configuration, $label, $operator, $focalLength) = $matches;

        $boxNumber = $labelCache[$label] ?? $labelCache[$label] = runHashAlgorithm($label);

        switch ($operator) {
            case '=': # Either add or overwrite a lens in a box.
                $boxes[$boxNumber][$label] = (int)$focalLength;
                break;
            case '-': # If in the specified box, remove the lens from the sequence.
                $existingBoxNumber = array_key_exists($boxNumber, $boxes);
                $existingLensLabel = $existingBoxNumber === true && array_key_exists($label, $boxes[$boxNumber]);

                if ($existingLensLabel === true) {
                    unset($boxes[$boxNumber][$label]);
                }
        }
    }

    $boxes = array_filter($boxes);
    foreach ($boxes as $boxNumber => $box) {
        $lenses = array_values($box);
        foreach ($lenses as $lensNumber => $focalLength) {
            $focusingPower += ($boxNumber + 1) * ($lensNumber + 1) * $focalLength;
        }
    }

    return $focusingPower;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));