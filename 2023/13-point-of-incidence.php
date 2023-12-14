<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/13.txt');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 *
 * @return string[][]
 */
function extractPatterns(array $input): array
{
    $patterns = [];

    $index = 0;
    foreach ($input as $row) {
        $row = trim($row);

        if ($row !== '') {
            $patterns[$index][] = $row;
        } else {
            $index++;
        }
    }

    return $patterns;
}

function findDifferences(string $alpha, string $beta): int
{
    $differences = 0;

    // If the strings are equal, skip the differentiation process.
    $equality = $alpha === $beta;

    if ($equality === false) {
        $length = strlen($alpha);

        // Determine and count differences between strings.
        for ($index = 0; $index < $length; $index++) {
            if ($alpha[$index] !== $beta[$index]) {
                $differences++;
            }
        }
    }

    // var_dump("Comparing '$alpha' and '$beta'. Found $differences differences.");

    return $differences;
}

/**
 * @param string[] $pattern
 */
function isPerfectReflection(array $pattern, int $mirrorIndex, bool $smudgedMirrors = false): bool
{
    $isPerfectReflection = false;

    $patternStart = array_key_first($pattern);
    $patternEnd = count($pattern);
    $counterIndex = $mirrorIndex - 1;
    $smudges = 0;

    for ($index = $mirrorIndex; $index < $patternEnd; $index++) {
        // Check if there is still room at the start of the pattern.
        $isValidCounterIndex = $patternStart <= $counterIndex;
        if ($isValidCounterIndex === false) {
            break;
        }

        // Extract the patterns.
        $currentPattern = $pattern[$index];
        $counterPattern = $pattern[$counterIndex];

        // Compare the patterns.
        $patternDifferences = findDifferences($currentPattern, $counterPattern);
        $smudges += $patternDifferences;

        // Depending on whether mirrors are expected to have a single smudge or not, the tolerance for differences is either 0 or 1.
        if (($smudgedMirrors === false && $smudges === 0) || ($smudgedMirrors === true && $smudges <= 1)) {
            $isPerfectReflection = true;
        }

        if (($smudgedMirrors === false && $smudges > 0) || ($smudgedMirrors === true && $smudges > 1)) {
            break;
        }

        // Reduce the counter index for the next loop.
        $counterIndex--;
    }

    // Perform a final reflection check, expected either exactly 0 or 1 smudges, depending on the settings.
    if (($smudgedMirrors === false && $smudges !== 0) || ($smudgedMirrors === true && $smudges !== 1)) {
        $isPerfectReflection = false;
    }

    return $isPerfectReflection;
}

/**
 * @param string[] $pattern
 */
function findHorizontalReflection(array $pattern, bool $smudgedMirrors = false): bool|int
{
    $mirrorIndex = false;

    // Find the mirror point.
    $previousLine = str_repeat('x', strlen($pattern[0]));
    foreach ($pattern as $index => $line) {
        if ($line === $previousLine || findDifferences($line, $previousLine) === 1) {
            $mirrorIndex = $index;

            // Check if it is a perfect reflection.
            $isPerfectReflection = isPerfectReflection($pattern, $mirrorIndex, $smudgedMirrors);
            if ($isPerfectReflection === true) {
                break;
            } else {
                // If not, reset the mirror index.
                $mirrorIndex = false;
            }
        }

        $previousLine = $line;
    }

    return $mirrorIndex;
}

/**
 * @param string[] $pattern
 */
function findVerticalReflection(array $pattern, bool $smudgedMirrors = false): bool|int
{
    // Enable the pattern to be transposed clockwise.
    $reversedPattern = array_reverse($pattern);
    $horizontalPatternLength = strlen($pattern[0]);
    $transposedPattern = array_fill(0, $horizontalPatternLength, '');

    // Transpose the pattern clockwise.
    foreach ($reversedPattern as $line) {
        for ($subIndex = 0; $subIndex < $horizontalPatternLength; $subIndex++) {
            $transposedPattern[$subIndex] .= $line[$subIndex];
        }
    }

    // Find the mirror index, if any.
    return findHorizontalReflection($transposedPattern, $smudgedMirrors);
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $patterns = extractPatterns($input);

    $mirrors = [];
    foreach ($patterns as $pattern) {
        // Find a horizontal reflection pattern.
        $horizontal = findHorizontalReflection($pattern);

        // If no horizontal pattern was found, find the vertical reflection pattern.
        $vertical = $horizontal === false ? findVerticalReflection($pattern) : false;

        if ($horizontal !== false) {
            $mirrors['horizontal'][] = $horizontal;
        } elseif ($vertical !== false) {
            $mirrors['vertical'][] = $vertical;
        }
    }

    return array_sum($mirrors['vertical']) + (100 * array_sum($mirrors['horizontal']));
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $patterns = extractPatterns($input);

    $mirrors = ['horizontal' => [], 'vertical' => []];
    foreach ($patterns as $pattern) {
        // Find a horizontal reflection pattern.
        $horizontal = findHorizontalReflection($pattern, true);

        // If no horizontal pattern was found, find the vertical reflection pattern.
        $vertical = $horizontal === false ? findVerticalReflection($pattern, true) : false;

        if ($horizontal !== false) {
            $mirrors['horizontal'][] = $horizontal;
        } elseif ($vertical !== false) {
            $mirrors['vertical'][] = $vertical;
        }
    }

    return array_sum($mirrors['vertical']) + (100 * array_sum($mirrors['horizontal']));
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));