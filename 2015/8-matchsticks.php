<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/8.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Removes all non-memory characters from a string.
 */
function purifyString(string $string): string
{
    $length = strlen($string);

    # Remove the prefixed and suffixed double quotes.
    $unquote = substr($string, 1, $length - 2);

    # Eliminate double backslashes.
    $doubleBackslash = str_replace('\\\\', 'A', $unquote);

    # Eliminate hexadecimal characters.
    $hexadecimal = preg_replace('/\\\\x[a-z0-9A-Z]{2}/', 'Z', $doubleBackslash);

    # Remove all escaping backslashes.
    return str_replace('\\', '', $hexadecimal);
}

/**
 * Escapes all non-literal characters in a string.
 */
function encodeString(string $string): string
{
    # Add backslashes to backslashes.
    $backslashes = str_replace('\\', '\\\\', $string);

    # Add backslashes to the double quotes.
    $quotes = str_replace('"', '\"', $backslashes);

    return '"' . $quotes . '"';
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $literal = $memory = 0;

    foreach ($input as $string) {
        $literal += strlen($string);
        $literalToMemory = purifyString($string);
        $memory += strlen($literalToMemory);
    }

    return $literal - $memory;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $literal = $encoded = 0;

    foreach ($input as $string) {
        $literal += strlen($string);
        $literalToEncoded = encodeString($string);
        $encoded += strlen($literalToEncoded);
    }

    return $encoded - $literal;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));