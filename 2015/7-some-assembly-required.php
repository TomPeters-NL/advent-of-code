<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/7', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Processes each wire in a recursive pattern, as the instructions are not in order.
 *
 * @param string[] $wires
 *
 * @return int[]
 */
function processWires(array $wires, array $assembly = []): array
{
    foreach ($wires as $index => $wire) {
        preg_match('/(([a-z0-9]+) )?(([A-Z]+) )?([a-z0-9]+) -> ([a-z]+)/', $wire, $components);

        # Retrieve the different components from the wire specification.
        # Example:   317      AND       x    ->    y
        # Example: (alpha) (operator) (beta)    (target)
        $sourceAlpha = $components[2];
        $sourceBeta = $components[5];
        $operator = $components[4];
        $target = $components[6];

        # Check if the sources are numeric or if not, present in the assembly list.
        $validSources = (is_numeric($sourceAlpha) === true || $sourceAlpha === '' || array_key_exists($sourceAlpha, $assembly))
            && (is_numeric($sourceBeta) === true || array_key_exists($sourceBeta, $assembly));

        # If the source is not (yet) valid, skip the operation for now.
        if ($validSources === false) {
            continue;
        }

        # Perform the operation and retrieve its value.
        $gateValue = getGateValue($assembly, $operator, $sourceAlpha, $sourceBeta);

        # Assign the new value to its assembly target, then remove the finished wire from the list.
        $assembly[$target] = $gateValue;
        unset($wires[$index]);
    }

    # If any gates are left, continue processing them.
    if (empty($wires) === false) {
        $assembly = processWires($wires, $assembly);
    }

    return $assembly;
}

/**
 * Parses the specified operation and provides its resulting value.
 *
 * @param int[] $assembly
 */
function getGateValue(array $assembly, string $operator, string $sourceAlpha, string $sourceBeta): int
{
    # Either cast the already numeric characters to integers, or retrieve their values from the assembly.
    $valueAlpha = is_numeric($sourceAlpha) === true ? (int)$sourceAlpha : ($sourceAlpha !== '' ? $assembly[$sourceAlpha] : null);
    $valueBeta = is_numeric($sourceBeta) === true ? (int)$sourceBeta : $assembly[$sourceBeta];

    # Perform the bitwise operations.
    $gateValue = match ($operator) {
        'AND' => $valueAlpha & $valueBeta,
        'OR' => $valueAlpha | $valueBeta,
        'LSHIFT' => $valueAlpha << $valueBeta,
        'RSHIFT' => $valueAlpha >> $valueBeta,
        'NOT' => ~$valueBeta,
        '' => $valueBeta,
    };

    # Account for negative values in the operation value.
    return $gateValue >= 0 ? $gateValue : 65536 + $gateValue;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $assembly = processWires($input);

    return $assembly['a'];
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $assembly = processWires($input);

    # Retrieve the instructions for wire B and extract the index and instructions.
    $wireBInstruction = preg_grep('/.+ -> b$/', $input);
    $wireBIndex = array_keys($wireBInstruction)[0];
    $wireBGate = $wireBInstruction[$wireBIndex];

    # Replace the gate instructions for wire B with the recently discovered value for wire A.
    $input[$wireBIndex] = preg_replace('/^([0-9a-z]+)( -> b)/', $assembly['a'] . '$2', $wireBGate);

    $reassembly = processWires($input);

    return $reassembly['a'];
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));