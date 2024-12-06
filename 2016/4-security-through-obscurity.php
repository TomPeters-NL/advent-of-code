<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/4', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * Separates each room description into three components: name, sector ID, and checksum.
 *
 * @param array $input The puzzle input.
 *
 * @return array A list of room names, sector IDs, and checksums.
 */
function analyzeRoomList(array $input): array
{
    $rooms = [];

    foreach ($input as $room) {
        preg_match("/([a-z\-]+)(\d+)\[([a-z]+)]/", $room, $analysis);

        list($ignore, $name, $sectorId, $checksum) = $analysis;

        $rooms[] = [$name, (int) $sectorId, $checksum];
    }

    return $rooms;
}

/**
 * Extracts the most common characters in an encrypted room name and verifies the room is real by using a checksum.
 *
 * @param string $name The (encrypted) room name.
 * @param string $checksum The checksum against which the room name should be checked.
 *
 * @return bool Whether a room is real or a decoy.
 */
function isRealRoom(string $name, string $checksum): bool
{
    $characterFrequencies = array_count_values(str_split($name));

    unset($characterFrequencies['-']);

    ksort($characterFrequencies);
    arsort($characterFrequencies);

    $mostCommonLetters = array_keys(array_splice($characterFrequencies, 0, 5));

    return $checksum === implode('', $mostCommonLetters);
}

/**
 * Decrypts room names using an alphabet shift cypher.
 *
 * @param string $name The encrypted room name.
 * @param int    $sectorId The ID of the sector the room is in.
 *
 * @return string The decrypted room name.
 */
function decryptRoomName(string $name, int $sectorId): string
{
    $decryptedName = '';
    $alphabet = range('a', 'z');

    foreach (str_split($name) as $letter) {
        if ($letter === '-') {
            $decryptedName .= ' ';

            continue;
        }

        $index = array_search($letter, $alphabet);

        $transposedIndex = ($index + $sectorId) % count($alphabet);

        $decryptedName .= $alphabet[$transposedIndex];
    }

    return trim($decryptedName);
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $sum = 0;

    $rooms = analyzeRoomList($input);

    foreach ($rooms as [$name, $sectorId, $checksum]) {
        if (isRealRoom($name, $checksum)) {
            $sum += $sectorId;
        }
    }

    return $sum;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $targetSectorId = 0;

    $rooms = analyzeRoomList($input);

    foreach ($rooms as [$name, $sectorId, $checksum]) {
        if (isRealRoom($name, $checksum) && decryptRoomName($name, $sectorId) === 'northpole object storage') {
            $targetSectorId = $sectorId;
        }
    }

    return $targetSectorId;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));