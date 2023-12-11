<?php

$input = file('./input/10-test.txt');

/**
 * @param string[] $grid
 */
function identifyStart(array $grid, int $x, int $y): string
{
    $possibleDirections = '';

    $north = ['x' => $x, 'y' => $y + 1];
    $destination = $grid[$north['y']][$north['x']];
    if (strpos('|7F', $destination) !== false) {
        $possibleDirections .= 'N';
    }

    $east = ['x' => $x + 1, 'y' => $y];
    $destination = $grid[$east['y']][$east['x']];
    if (strpos('-J7', $destination) !== false) {
        $possibleDirections .= 'E';
    }

    $south = ['x' => $x, 'y' => $y - 1];
    $destination = $grid[$south['y']][$south['x']];
    if (strpos('|LJ', $destination) !== false) {
        $possibleDirections .= 'S';
    }

    $west = ['x' => $x - 1, 'y' => $y];
    $destination = $grid[$west['y']][$west['x']];
    if (strpos('-LF', $destination) !== false) {
        $possibleDirections .= 'W';
    }

    return match($possibleDirections) {
        'NE' => 'L',
        'NS' => '|',
        'NW' => 'J',
        'ES' => 'F',
        'EW' => '-',
        'SW' => '7',
    };
}

/**
 * @param string[] $grid
 *
 * @return int[]
 */
function findStartingLocation(array $grid): array
{
    foreach ($grid as $y => $row) {
        $x = strpos($row, 'S');

        if ($x !== false) {
            return ['x' => $x, 'y' => $y];
        }
    }
}

/**
 * @param string[] $grid
 *
 * @return int[]
 */
function findFirstLocation(array $grid, int $x, int $y): array
{
    $north = ['x' => $x, 'y' => $y + 1];
    $destination = $grid[$north['y']][$north['x']];
    if (strpos('|7F', $destination) !== false) {
        return $north;
    }

    $east = ['x' => $x + 1, 'y' => $y];
    $destination = $grid[$east['y']][$east['x']];
    if (strpos('-J7', $destination) !== false) {
        return $east;
    }

    $south = ['x' => $x, 'y' => $y - 1];
    $destination = $grid[$south['y']][$south['x']];
    if (strpos('|LJ', $destination) !== false) {
        return $south;
    }

    $west = ['x' => $x - 1, 'y' => $y];
    $destination = $grid[$west['y']][$west['x']];
    if (strpos('-LF', $destination) !== false) {
        return $west;
    }
}

/**
 * @param string[] $grid
 * @param int[] $currentXY
 * @param int[] $previousXY
 *
 * @return int[]
 */
function findNextLocation(array $grid, array $currentXY, array $previousXY): array
{
    $currentX = $currentXY['x'];
    $currentY = $currentXY['y'];
    $previousX = $previousXY['x'];
    $previousY = $previousXY['y'];

    $currentLocation = $grid[$currentY][$currentX];
    $nextX = $currentX;
    $nextY = $currentY;

    switch ($currentLocation) {
        case '|':
            if ($previousY < $currentY) { // South -> North
                $nextY++;
            } else { // North -> South
                $nextY--;
            }

            break;
        case '-':
            if ($previousX < $currentX) { // West -> East
                $nextX++;
            } else { // East -> West
                $nextX--;
            }

            break;
        case 'L':
            if ($previousY > $currentY) { // North -> East
                $nextX++;
            } else { // East -> North
                $nextY++;
            }

            break;
        case 'J':
            if ($previousY > $currentY) { // North -> West
                $nextX--;
            } else { // West -> North
                $nextY++;
            }

            break;
        case '7':
            if ($previousY < $currentY) { // South -> West
                $nextX--;
            } else { // West -> South
                $nextY--;
            }

            break;
        case 'F':
            if ($previousY < $currentY) { // South -> East
                $nextX++;
            } else { // East -> South
                $nextY--;
            }

            break;
        case 'S':
            throw new Exception('You are back at the start. You really shouldn\'t be here.');
        default:
            throw new Exception('You took a wrong direction somewhere, partner.');
    }

    return ['x' => $nextX, 'y' => $nextY];
}

/**
 * @param string[] $grid
 * @param int[][] $loop
 */
function applyRayCastingAlgorithm(array $grid, array $loop, int $y): int
{
    // Isolate target row.
    $gridRow = $grid[$y];
    $loopCharacters = $loop[$y];
    $loopCoordinates = array_keys($loopCharacters);

    // Remove edge tiles.
    $firstLoopCoordinate = min($loopCoordinates);
    $loopWidth = max($loopCoordinates) - $firstLoopCoordinate + 1;
    $sample = array_slice(str_split($gridRow), $firstLoopCoordinate, $loopWidth, true);

    $insideTiles = 0;
    foreach ($sample as $x => $character) {
        if (in_array($x, $loopCoordinates) === false) {
            $westLength = array_search($x, array_keys($sample));
            $westLoop = array_slice($sample, 0, $westLength, true);
            $pureWestLoop = array_reverse(array_intersect_key($loopCharacters, $westLoop), true);
            preg_match_all('/(L7|FJ)|(LJ|F7)|(\||L|F|J|7)/', str_replace('-', '', implode('', $pureWestLoop)), $matches);

            $walls = count(array_filter($matches[1]));     // Opposites: L7 F7
            $walls += count(array_filter($matches[2])) * 2; // Similars : LJ FJ
            $walls += count(array_filter($matches[3]));     // Singulars: | L F J 7
            if ($walls % 2 !== 0) {
                $insideTiles++;
            }
        }
    }

    return $insideTiles;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    // Reverse grid keys to make Y axis more readable.
    $reversedKeys = array_reverse(array_keys($input));
    $grid = array_combine($reversedKeys, $input);

    $start = findStartingLocation($grid);
    $firstLocation = findFirstLocation($grid, $start['x'], $start['y']);

    $steps = 1;
    $previousLocation = $start;
    $currentLocation = $firstLocation;

    while ($currentLocation !== $start) {
        $nextLocation = findNextLocation($grid, $currentLocation, $previousLocation);

        $previousLocation = $currentLocation;
        $currentLocation = $nextLocation;
        $steps++;
    }

    return $steps / 2;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    // Reverse grid keys to make Y axis more readable.
    $reversedKeys = array_reverse(array_keys($input));
    $grid = array_combine($reversedKeys, $input);

    $start = findStartingLocation($grid);
    $firstLocation = findFirstLocation($grid, $start['x'], $start['y']);

    $previousLocation = $start;
    $currentLocation = $firstLocation;

    $currentX = $currentLocation['x'];
    $currentY = $currentLocation['y'];
    $loop = [$currentY => [$currentX => $grid[$currentY][$currentX]]]; // Record first location.
    while ($currentLocation !== $start) {
        $nextLocation = findNextLocation($grid, $currentLocation, $previousLocation);

        $previousLocation = $currentLocation;
        $currentLocation = $nextLocation;

        $currentX = $currentLocation['x'];
        $currentY = $currentLocation['y'];
        $loop[$currentY][$currentX] = $grid[$currentY][$currentX]; // Record current location.
    }


    $startX = $start['x'];
    $startY = $start['y'];
    $grid[$startY][$startX] = identifyStart($grid, $startX, $startY);
    krsort($loop);


    $insideTiles = 0;
    foreach ($loop as $y => &$row) {
        ksort($row);

        $insideTiles += applyRayCastingAlgorithm($grid, $loop, $y);
    }

    return $insideTiles;
    # 552 is too high.
}

###############
### Results ###
###############

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;