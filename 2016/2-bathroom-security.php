<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/2', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Determines whether a provided set of coordinates contain a valid position of a key on the bathroom keypad.
 *
 * @param string $coordinates A stringified version of the target X and Y coordinates (e.g., "0,1")
 * @param bool   $complexKeypad Whether the complex keypad is in effect.
 *
 * @return bool Whether the provided coordinates target a possible location on the keypad or not.
 */
function isValidPosition(string $coordinates, bool $complexKeypad = false): bool
{
    $baseKeys = ['-1,-1', '0,-1', '1,-1', '-1,0', '0,0', '1,0', '-1,1', '0,1', '1,1'];
    $complexKeys = ['0,-2', '-2,0', '2,0', '0,2'];

    if ($complexKeypad) {
        return in_array($coordinates, $baseKeys) || in_array($coordinates, $complexKeys);
    } else {
        return in_array($coordinates, $baseKeys);
    }
}

/**
 * Translates a set of X and Y coordinates to the button to be pressed on the bathroom keypad.
 *
 * @param string $coordinates A stringified version of the current X and Y coordinates (e.g., "0,1")
 * @param bool   $complexKeypad Whether the complex keypad is in effect.
 *
 * @return string Returns the key to be pressed.
 */
function convertCoordinatesToButton(string $coordinates, bool $complexKeypad = false): string
{
    return match ($coordinates) {
        '0,-2' => '1',
        '-1,-1' => $complexKeypad ? '2' : '1',
        '0,-1' => $complexKeypad ? '3' : '2',
        '1,-1' => $complexKeypad ? '4' : '3',
        '-2,0' => '5',
        '-1,0' => $complexKeypad ? '6' : '4',
        '0,0' => $complexKeypad ? '7' : '5',
        '1,0' => $complexKeypad ? '8' : '6',
        '2,0' => '9',
        '-1,1' => $complexKeypad ? 'A' : '7',
        '0,1' => $complexKeypad ? 'B' : '8',
        '1,1' => $complexKeypad ? 'C' : '9',
        '0,2' => 'D',
    };
}

/**
 * Follows the Elves' instructions from the previous key pressed to the next.
 *
 * @param array{x: int, y: int} $position The current X and Y coordinates on the keypad.
 * @param string                $instructions A string of characters containing the directions towards the next keypress.
 * @param bool                  $complexKeypad Whether the complex keypad is in effect.
 *
 * @return array{x: int, y: int} The new X and Y coordinates after following the instructions.
 */
function followInstructions(array $position, string $instructions, bool $complexKeypad = false): array
{
    foreach (str_split($instructions) as $direction) {
        $nextPosition = $position;

        match ($direction) {
            'U' => $nextPosition['y']--,
            'D' => $nextPosition['y']++,
            'L' => $nextPosition['x']--,
            'R' => $nextPosition['x']++,
        };

        $readableNewPosition = implode(',', $nextPosition);
        $isValidPosition = isValidPosition($readableNewPosition, $complexKeypad);

        if ($isValidPosition) {
            $position = $nextPosition;
        }
    }

    return $position;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): string
{
    $bathroomCode = '';
    $keypadPosition = ['x' => 0, 'y' => 0];

    foreach ($input as $instructions) {
        $keypadPosition = followInstructions($keypadPosition, $instructions);

        $readableKeypadPosition = implode(',', $keypadPosition);
        $bathroomCode .= convertCoordinatesToButton($readableKeypadPosition);
    }

    return $bathroomCode;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): string
{
    $bathroomCode = '';
    $keypadPosition = ['x' => -2, 'y' => 0];

    foreach ($input as $instructions) {
        $keypadPosition = followInstructions($keypadPosition, $instructions, true);

        $readableKeypadPosition = implode(',', $keypadPosition);
        $bathroomCode .= convertCoordinatesToButton($readableKeypadPosition, true);
    }

    return $bathroomCode;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));