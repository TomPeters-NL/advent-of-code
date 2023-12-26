<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/10');

#################
### Solutions ###
#################

/**
 * Reverses the grid's Y-axis, so positive changes in Y correspond to northbound movement.
 *
 * @param string[] $input
 *
 * @return string[]
 */
function getGrid(array $input): array
{
    $keys = array_keys($input);
    $reversedKeys = array_reverse($keys);

    return array_combine($reversedKeys, $input);
}

/**
 * @param string[] $grid
 * @param array[]<int,string> $loop
 *
 * @return string[]
 */
function simplifyGrid(array $grid, array $loop, bool $visualizeGrid = false): array
{
    foreach ($grid as $y => $row) {
        $positions = str_split($row);

        ksort($positions);
        foreach ($positions as $x => $identity) {
        $isPartOfLoop = array_key_exists($y, $loop) && array_key_exists($x, $loop[$y]);

            if ($isPartOfLoop === false) {
                $positions[$x] = '.';
            }
        }

        $grid[$y] = implode('', $positions);
    }

    if ($visualizeGrid === true) {
        foreach ($grid as $row) {
            var_dump($row);
        }
    }

    return $grid;
}

/**
 * Retrieves the loop's starting X and Y coordinates and replaces the "S" character with its actual character.
 *
 * @param string[] $grid
 *
 * @return array<int,string>
 */
function identifyStart(array &$grid): array
{
    // Determine the starting X and Y coordinates.
    foreach ($grid as $y => $row) {
        $x = strpos($row, 'S');

        if ($x !== false) {
            break;
        }
    }

    // Determine potential destinations.
    $potentialDestinations = '';
    $potentialDestinations .= strpos('|F7', $grid[$y + 1][$x]) !== false ? 'N' : '';
    $potentialDestinations .= strpos('-J7', $grid[$y][$x + 1]) !== false ? 'E' : '';
    $potentialDestinations .= strpos('|LJ', $grid[$y - 1][$x]) !== false ? 'S' : '';
    $potentialDestinations .= strpos('-LF', $grid[$y][$x - 1]) !== false ? 'W' : '';

    // Determine the true identity of S.
    $identity = match($potentialDestinations) {
        'NE' => 'L',
        'NS' => '|',
        'NW' => 'J',
        'ES' => 'F',
        'EW' => '-',
        'SW' => '7',
    };

    // Replace the "S" character with its true identity.
    $grid[$y][$x] = $identity;

    return [$x, $y, $identity];
}

/**
 * Identifies a potential valid move from or to the starting position.
 *
 * @param string[] $grid
 * @param array<int,string> $start
 *
 * @return array<int,string>
 */
function identifyPotentialPosition(array $grid, array $start): array
{
    list($x, $y, $identity) = $start;

    match ($identity) {
        '|' => $y++,
        '-' => $x++,
        'L' => $y++,
        'J' => $y++,
        '7' => $y--,
        'F' => $y--,
    };

    $identity = $grid[$y][$x];

    return [$x, $y, $identity];
}

/**
 * @param string[] $grid
 * @param array<int,string> $currentPosition
 * @param array<int,string> $previousPosition
 *
 * @return array<int,string>
 */
function identifyNextPosition(array $grid, array $currentPosition, array $previousPosition): array
{
    list($currentX, $currentY, $currentCharacter) = $currentPosition;
    list($previousX, $previousY, $previousCharacter) = $previousPosition;

    // Determine the direction from whence the loop originated.
    $horizontalChange = $previousX - $currentX;
    $horizontalOrigin = $horizontalChange === 1 ? 'E' : ($horizontalChange === -1 ? 'W' : '');

    $verticalChange = $previousY - $currentY;
    $verticalOrigin = $verticalChange === 1 ? 'N' : ($verticalChange === -1 ? 'S' : '');

    $origin = $horizontalOrigin . $verticalOrigin;

    // Determine the next X and Y coordinates.
    switch ($currentCharacter) {
        case '|':
            $x = $currentX;
            $y = $origin === 'N' ? $currentY - 1 : $currentY + 1;

            break;
        case '-':
            $x = $origin === 'E' ? $currentX - 1 : $currentX + 1;
            $y = $currentY;

            break;
        case 'L':
            $x = $origin === 'N' ? $currentX + 1 : $currentX;
            $y = $origin === 'E' ? $currentY + 1 : $currentY;

            break;
        case 'J':
            $x = $origin === 'N' ? $currentX - 1 : $currentX;
            $y = $origin === 'W' ? $currentY + 1 : $currentY;

            break;
        case '7':
            $x = $origin === 'S' ? $currentX - 1 : $currentX;
            $y = $origin === 'W' ? $currentY - 1 : $currentY;

            break;
        case 'F':
            $x = $origin === 'S' ? $currentX + 1 : $currentX;
            $y = $origin === 'E' ? $currentY - 1 : $currentY;

            break;
        default:
            throw new Exception('Oh no, the loop! It\'s broken!');
    }

    // Determine the next character's identity.
    $identity = $grid[$y][$x];

    return [$x, $y, $identity];
}

/**
 * Determines the amount of tiles inside of the loop, using the ray-casting algorithm.
 *
 * @param string[] $cleanGrid
 */
function getInnerTiles(array $cleanGrid): int
{
    $innerTiles = 0;
    foreach ($cleanGrid as $y => $row) {
        $skippable = strlen($row) === substr_count($row, '.');
        if ($skippable === true) { // Skip if row consists solely of periods.
            continue;
        }
        $cleanRow = preg_replace('/^\.+|-|\.+$/', '', $row); // Replace outside periods and horizontal characters.

        $positions = str_split($cleanRow);
        foreach ($positions as $x => $identity) {
            if ($identity !== '.') { // Skip if the current character is part of the loop.
                continue;
            }

            $fragment = substr($cleanRow, 0, $x);
            preg_match_all('/(L7|FJ)|(LJ|F7)|(\||L|F|J|7)/', $fragment, $matches);

            $verticals = count(array_filter($matches[1])); // Verticals going in opposite directions (L <-> 7 & F <-> 7).
            $verticals += count(array_filter($matches[2])) * 2; // Verticals going in the same directions (L <-> J & F <-> J).
            $verticals += count(array_filter($matches[3])); // Singular verticals (L, F, J & 7).

            if ($verticals % 2 !== 0) {
                $innerTiles++;
            }
        }
    }

    return $innerTiles;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $grid = getGrid($input);
    $start = identifyStart($grid);

    $loopLength = 0;
    $previousPosition = identifyPotentialPosition($grid, $start);
    $currentPosition = $start;

    do {
        $nextPosition = identifyNextPosition($grid, $currentPosition, $previousPosition);

        $previousPosition = $currentPosition;
        $currentPosition = $nextPosition;

        $loopLength++;
    } while ($start[0] !== $currentPosition[0] || $start[1] !== $currentPosition[1]);

    return $loopLength / 2;
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $grid = getGrid($input);
    $start = identifyStart($grid);

    $loop = [];
    $previousPosition = identifyPotentialPosition($grid, $start);
    $currentPosition = $start;

    do {
        list($x, $y, $identity) = $currentPosition;
        $loop[$y][$x] = $identity;

        $nextPosition = identifyNextPosition($grid, $currentPosition, $previousPosition);

        $previousPosition = $currentPosition;
        $currentPosition = $nextPosition;
    } while ($start[0] !== $currentPosition[0] || $start[1] !== $currentPosition[1]);

    $cleanGrid = simplifyGrid($grid, $loop, true);

    return getInnerTiles($cleanGrid);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));