<?php

$input = file('./input/13.txt');

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

/**
 * @param string[] $pattern
 */
function isPerfectReflection(array $pattern, int $mirrorIndex): bool
{
    $isPerfectReflection = false;

    $patternStart = array_key_first($pattern);
    $patternEnd = count($pattern);
    $counterIndex = $mirrorIndex - 1;

    for ($index = $mirrorIndex; $index < $patternEnd; $index++) {
        $isValidCounterIndex = $patternStart <= $counterIndex;
        if ($isValidCounterIndex === false) {
            break;
        }

        $isMirrorImage = $pattern[$index] === $pattern[$counterIndex];
        if ($isMirrorImage === true) {
            $isPerfectReflection = true;
            $counterIndex--;
        } else {
            $isPerfectReflection = false;
            break;
        }
    }

    return $isPerfectReflection;
}

/**
 * @param string[] $pattern
 */
function findHorizontalReflection(array $pattern): bool|int
{
    $mirrorIndex = false;

    // Find the mirror point.
    $previousLine = '';
    foreach ($pattern as $index => $line) {
        if ($line === $previousLine) {
            $mirrorIndex = $index;
            $isPerfectReflection = isPerfectReflection($pattern, $mirrorIndex);
            if ($isPerfectReflection === true) {
                break;
            }
        }

        $previousLine = $line;
    }

    // Check if it is a perfect reflection.
    if ($mirrorIndex !== false) {
        $isPerfectReflection = isPerfectReflection($pattern, $mirrorIndex);

        // If not, do not provide mirror index.
        if ($isPerfectReflection === false) {
            $mirrorIndex = false;
        }
    }

    return $mirrorIndex;
}

/**
 * @param string[] $pattern
 */
function findVerticalReflection(array $pattern): bool|int
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
    return findHorizontalReflection($transposedPattern);
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $patterns = extractPatterns($input);

    $mirrors = [];
    foreach ($patterns as $index => $pattern) {
        findVerticalReflection($pattern);
        $horizontal = findHorizontalReflection($pattern);
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
    // For future Tom.
    // Part 2 might be almost identical to part 1.
    // Try to implement a mandatory error margin of 1 in the perfect reflection validation.

    return 2;
}

###############
### Results ###
###############

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;