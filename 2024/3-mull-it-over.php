<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/3');

#################
### Solutions ###
#################

/**
 * @param string[] $input
 *
 * @return string[]
 */
function parseInstructions(array $input, bool $includeConditionals = false): array
{
    $uncorruptedInstructions = [];

    foreach ($input as $line) {
        $regex = $includeConditionals ? "/don't|do|mul\(\d+,\d+\)/" : "/mul\(\d+,\d+\)/";

        preg_match_all($regex, $line, $instructions);

        $uncorruptedInstructions = array_merge($uncorruptedInstructions, $instructions[0]);
    }

    return $uncorruptedInstructions;
}

/**
 * @param string[] $instructions
 */
function processInstructions(array $instructions): int
{
    $total = 0;
    $activeCalculator = true;

    foreach ($instructions as $instruction) {
        if (!$activeCalculator && str_starts_with($instruction, 'mul')) {
            continue;
        }

        if ($instruction === 'do') {
            $activeCalculator = true;
            continue;
        }

        if ($instruction === 'don\'t') {
            $activeCalculator = false;
            continue;
        }

        preg_match("/mul\((\d+),(\d+)\)/", $instruction, $operands);

        $total += (int) $operands[1] * (int) $operands[2];
    }

    return $total;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $instructions = parseInstructions($input);

    return processInstructions($instructions);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 *
 * @throws Exception
 */
function partTwo(array $input): int
{
    $instructions = parseInstructions($input, true);

    return processInstructions($instructions);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));