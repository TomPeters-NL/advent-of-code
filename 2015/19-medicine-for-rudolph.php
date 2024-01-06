<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/19-test', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Extracts the medicine molecule and molecule replacements from the puzzle input.
 *
 * @param string[] $input The puzzle input.
 *
 * @return array Returns the medicine molecule and all the possible molecule replacements.
 */
function processInstructions(array $input): array
{
    # Extract the medicine molecule.
    $medicine = array_pop($input);

    # Remove the empty line from the end of the input.
    array_pop($input);

    # Determine all potential replacements.
    $replacements = [];
    foreach ($input as $line) {
        [$original, $replacement] = explode(' => ', $line);

        $replacements[$original][] = $replacement;
    }

    return [$medicine, $replacements];
}

/**
 * Finds all possible iterations of the medicine molecule, assuming a single replacement at a time.
 *
 * @param string     $molecule     The medicine molecule.
 * @param string[][] $replacements A list of atoms and their potential replacement atoms.
 *
 * @return string[] All unique potential iterations of the medicine molecule.
 */
function findMoleculeIterations(string $molecule, array $replacements): array
{
    $iterations = [];

    foreach ($replacements as $source => $destinations) {
        # Extract all occurrences of the source atom in the molecule.
        preg_match_all('/' . $source . '/', $molecule, $matches, PREG_OFFSET_CAPTURE);
        $targets = $matches[0];

        # For each occurrence of the source atom, replace it with the destination atom, and log the new molecule.
        foreach ($destinations as $destination) {
            foreach ($targets as [$atom, $index]) {
                $index = (int) $index;

                $start = substr($molecule, 0, $index);
                $end = substr($molecule, $index + 1);

                $iterations[] = $start . $destination . $end;
            }
        }
    }

    return array_unique($iterations);
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    [$medicine, $replacements] = processInstructions($input);

    $medicineIterations = findMoleculeIterations($medicine, $replacements);

    return count($medicineIterations);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    [$medicine, $replacements] = processInstructions($input);

    return 2;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));