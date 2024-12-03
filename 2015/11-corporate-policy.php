<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = ['hxbxwxba', 'hxbxxyzz'];

#################
### Solutions ###
#################

function hasStraight(string $password): bool
{
    preg_match('/abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz/',$password, $matches);

    return empty($matches) === false;
}

function hasInvalidCharacters(string $password): bool
{
    return str_contains($password, 'i') || str_contains($password, 'o') || str_contains($password, 'l');
}

function hasTwoPairs(string $password): bool
{
    preg_match_all('/([a-z])\1/',$password, $matches);

    return count($matches[0]) >= 2;
}

function generateNewPassword(string $currentPassword): string
{
    $password = $currentPassword;
    $isValidPassword = false;

    do {
        # Increment the password string (e.g., xyz -> xza).
        $password++;

        # Requirement 1: Characters "i", "o", and "l" are not allowed.
        $hasInvalidCharacters = hasInvalidCharacters($password);
        if ($hasInvalidCharacters === true) {
            # Find the index of the invalid character.
            $i = str_contains($password, 'i') === true ? strpos($password, 'i') : 999;
            $o = str_contains($password, 'o') === true ? strpos($password, 'o') : 999;
            $l = str_contains($password, 'l') === true ? strpos($password, 'l') : 999;
            $index = min($i, $o, $l);

            # Increment the invalid character to the next letter in the alphabet.
            $slice = substr($password, 0, $index + 1);
            $slice++;

            # Reset every character after the invalid one to "a".
            # Example: abcidsflk -> abcizzzzz -> "abcjaaaaa"
            $password = $slice . str_repeat('a', strlen($password) - strlen($slice));
        }

        # Requirement 2: There must be at least one increasing straight (e.g., abc, def).
        $hasStraight = hasStraight($password);
        if ($hasStraight === false) {
            continue;
        }

        # Requirement 3: There must be at least two different, non-overlapping pairs (e.g., aa & cc).
        $hasTwoPairs = hasTwoPairs($password);
        if ($hasTwoPairs === false) {
            continue;
        }

        $isValidPassword = true;
    } while ($isValidPassword === false);

    return $password;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): string
{
    [$password, $ignore] = $input;

    return generateNewPassword($password);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): string
{
    [$ignore, $password] = $input;

    return generateNewPassword($password);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));