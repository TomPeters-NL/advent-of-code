<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/17-test.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

class Space
{
    public string $coordinates;

    public function __construct(
        public int $x,
        public int $y,
        public int $dX,
        public int $dY,
        public int $distance,
        public int $cost = 0
    ) {
        $this->coordinates = $x . ',' . $y;
    }
}

/**
 * Prepares the input map for pathfinding.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] The heat loss map split into X and Y axes.
 */
function prepareMap(array $input): array
{
    $heatLossMap = [];

    foreach ($input as $row => $line) {
        $columns = str_split($line);
        $integers = array_map('intval', $columns);

        $heatLossMap[$row] = $integers;
    }

    return $heatLossMap;
}

/**
 * Finds the most optimal path to the machine parts factory.
 *
 * @param int[][] $map                 The heat loss map of the city.
 * @param int[]   $lavaPool            The coordinates of the starting point.
 * @param int[]   $machinePartsFactory The coordinates of the endpoint.
 *
 * @return int[] A list of cumulative heat loss per block.
 */
function findPath(array $map, array $lavaPool, array $machinePartsFactory): array
{
    return [];
}


/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    # Process the heat (loss) map.
    $map = prepareMap($input);

    # The starting and ending city blocks: [x, y].
    $lavaPool = [0, 0];
    $machinePartFactory = [array_key_last($map), array_key_last($map[0])];

    return 1;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    return 2;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));