<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/13', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Parses each input line for the relevant data to map happiness scores between guests.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] A list of interpersonal happiness scores.
 */
function extractHappinessMapping(array $input, bool $includeMe = false): array
{
    $mapping = [];

    foreach ($input as $line) {
        preg_match('/([A-Z][a-z]+).+(gain|lose)\D+(\d+).+([A-Z][a-z]+)/', $line, $matches);

        [$ignore, $subject, $type, $happiness, $person] = $matches;

        $happinessScore = match ($type) {
            'gain' => $happiness,
            'lose' => '-' . $happiness,
        };

        $mapping[$subject][$person] = (int) $happinessScore;
    }

    if ($includeMe === true) {
        foreach ($mapping as $subject => $subjectMapping) {
            $mapping[$subject]['Me'] = 0;
            $mapping['Me'][$subject] = 0;
        }
    }

    return $mapping;
}

/**
 * Generates all potential combinations of seating arrangements.
 *
 * @param string[][] $arrangements The current list of possible seating arrangements.
 * @param string[]   $guests       The list of all potential guests.
 *
 * @return string[][] The new, expanded list of possible seating arrangements.
 */
function generateArrangements(array $arrangements, array $guests): array
{
    $newArrangements = 0;

    foreach ($arrangements as $index => $arrangement) {
        foreach ($guests as $guest) {
            # Skip if the guest is already in the arrangement.
            if (in_array($guest, $arrangement) === true) {
                continue;
            }

            # Add the current guest to create a new arrangement.
            $newArrangement = $arrangement;
            $newArrangement[] = $guest;

            # Add the new arrangement to the list and increment the counter.
            $arrangements[] = $newArrangement;
            $newArrangements++;
        }

        # Only remove the current arrangement if it produced new arrangements.
        if ($newArrangements > 0) {
            unset($arrangements[$index]);
        }
    }

    # If new arrangements are still being formed, continue the recursive loop.
    if ($newArrangements > 0) {
        $arrangements = generateArrangements($arrangements, $guests);
    }

    return $arrangements;
}

/**
 * Creates a list of possible seating arrangements.
 *
 * @param array $guests The list of guests.
 *
 * @return string[][] A list of all potential seating arrangement combinations.
 */
function createSeatingArrangements(array $guests): array
{
    # Initialize the seating arrangements.
    $seating = [];
    foreach ($guests as $guest) {
        $seating[][] = $guest;
    }

    return generateArrangements($seating, $guests);
}

function calculateHappinessScores(array $mapping, array $seating): array
{
    $scores = [];

    foreach ($seating as $arrangement) {
        $score = 0;

        $first = array_key_first($arrangement);
        $last = array_key_last($arrangement);

        foreach ($arrangement as $guestIndex => $guest) {
            $neighbourRight = $guestIndex === $first ? $arrangement[$last] : $arrangement[$guestIndex - 1];
            $neighbourLeft = $guestIndex === $last ? $arrangement[$first] : $arrangement[$guestIndex + 1];

            $score += $mapping[$guest][$neighbourRight];
            $score += $mapping[$guest][$neighbourLeft];
        }

        $implodedArrangement = implode('-', $arrangement);
        $scores[$implodedArrangement] = $score;
    }

    return $scores;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $happinessMapping = extractHappinessMapping($input);

    $guests = array_keys($happinessMapping);
    $seatingArrangements = createSeatingArrangements($guests);

    $happinessScores = calculateHappinessScores($happinessMapping, $seatingArrangements);

    return max($happinessScores);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $happinessMapping = extractHappinessMapping($input, true);

    $guests = array_keys($happinessMapping);
    $seatingArrangements = createSeatingArrangements($guests);

    $happinessScores = calculateHappinessScores($happinessMapping, $seatingArrangements);

    return max($happinessScores);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));