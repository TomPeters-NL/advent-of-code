<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/19', FILE_IGNORE_NEW_LINES);

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
    preg_match_all('/[A-Z]([a-z]+)?/', $medicine, $matches);
    $medicineAtoms = $matches[0];

    # Remove the empty line from the end of the input.
    array_pop($input);

    # Determine all potential replacements.
    $replacements = [];
    foreach ($input as $line) {
        [$original, $replacement] = explode(' => ', $line);

        $replacements[$original][] = $replacement;
    }

    return [$medicineAtoms, $replacements];
}

/**
 * Finds all possible iterations of the medicine molecule, assuming a single replacement at a time.
 *
 * @param string[] $medicine A list of atoms present in the medicine molecule.
 * @param string[][] $replacements A list of atoms and their potential replacement atoms.
 * @param bool $implode Determines whether the iteration atoms are imploded or not.
 *
 * @return string[] | string[][] All unique potential iterations of the medicine molecule.
 */
function findMoleculeIterations(array $medicine, array $replacements, bool $implode = true): array
{
    $iterations = [];

    $targetAtoms = array_keys($replacements);
    foreach ($medicine as $index => $atom) {
        if (in_array($atom, $targetAtoms) === false) {
            continue;
        }

        foreach ($replacements[$atom] as $replacement) {
            $newMedicine = $medicine;
            $newMedicine[$index] = $replacement;

            $iteration = implode('', $newMedicine);
            if ($implode === false) {
                preg_match_all('/[A-Z]([a-z]+)?/', $iteration, $matches);
                $iteration = $matches[0];
            }

            $iterations[] = $iteration;
        }
    }

    return array_unique($iterations, SORT_REGULAR);
}

/**
 * Fabricates a target medicine molecule from an initial electron.
 *
 * @param string[][] $medicine The list of atoms present in the medicine molecule.
 * @param string[][] $replacements A list of atoms and their potential replacement atoms.
 *
 * @return int The amount of steps required for the fabrication of the medicine.
 */
function fabricateMolecule(array $medicine, array $replacements): int
{
    $pastMolecules = [];
    $molecules = [['e']];
    $steps = 0;

    while (count($molecules) > 0 && in_array($medicine, $molecules) === false) {
        $newMolecules = [];
        $steps++;

        foreach ($molecules as $molecule) {
            $iterations = findMoleculeIterations($molecule, $replacements, false);

            $newMolecules = array_merge($newMolecules, $iterations);
        }

        foreach ($newMolecules as $index => $newMolecule) {
            $string = implode('', $newMolecule);

            if (in_array($newMolecule, $pastMolecules) === true) {
                unset($newMolecules[$index]);
            } else {
                $pastMolecules[] = $string;
            }
        }

        $molecules = array_unique($newMolecules, SORT_REGULAR);
    }

    return $steps;
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

    return fabricateMolecule($medicine, $replacements);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));