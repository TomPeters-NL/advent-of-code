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
function prepareHeatMap(array $input): array
{
    $heatLossMap = [];

    foreach ($input as $row => $line) {
        $columns = str_split($line);
        $integers = array_map('intval', $columns);

        $heatLossMap[$row] = $integers;
    }

    return $heatLossMap;
}

function determineMinimalHeatLoss(array $heatMap, array $lavaPool, array $machinePartsFactory): array
{
    # Extract the city dimensions.
    $minX = array_key_first($heatMap[0]);
    $maxX = array_key_last($heatMap[0]);
    $minY = array_key_first($heatMap);
    $maxY = array_key_last($heatMap);

    # Process the start location.
    [$startX, $startY] = $lavaPool;

    # The list/queue that tracks which blocks to visit by prioritizing lower heat loss.
    $queue = [];
    $queue[] = ['heatLoss' => 0, 'x' => $startX, 'y' => $startY];

    # The list that tracks the tentative heat loss to each visited block.
    $blocks = [];
    $blocks["$startX,$startY"] = 0;

    # The list tracking the paths between blocks.
    $paths = [];
    $paths["$startX,$startY"] = null;

    while(empty($queue) === false) {
        # Prioritize the queue.
        uasort($queue, fn ($alpha, $beta) => $alpha['heatLoss'] <=> $beta['heatLoss']);

        # Retrieve the current block details.
        ['heatLoss' => $heatLoss, 'x' => $x, 'y' => $y] = array_shift($queue);

        # Stop pathfinding upon reaching the destination.
        if ([$x, $y] === $machinePartsFactory) {
            break;
        }

        # Find the neighbouring blocks.
        $neighbours = [
            [$x + 1, $y],
            [$x - 1, $y],
            [$x, $y + 1],
            [$x, $y - 1],
        ];

        foreach ($neighbours as [$newX, $newY]) {
            # Validate the new block coordinates.
            if ($newX < $minX || $newX > $maxX || $newY < $minY || $newY > $maxY || in_array("$newX,$newY", array_keys($paths)) === true) {
                continue;
            }

            # Calculate the heat loss in the neighbouring block.
            $newHeatLoss = $blocks["$x,$y"] + $heatMap[$newY][$newX];

            # Determine whether the neighbouring block should be considered.
            if (isset($block["$newX,$newY"]) === false || $newHeatLoss < $blocks["$newX,$newY"]) {
                # Update the heat loss for the block.
                $blocks["$newX,$newY"] = $newHeatLoss;

                # Update the queue.
                $queue[] = ['heatLoss' => $newHeatLoss, 'x' => $newX, 'y' => $newY];

                # Update the path so far.
                $paths["$newX,$newY"] = "$x,$y";
            }
        }
    }

    return [$blocks, $paths];
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    # Process the heat (loss) map.
    $heatMap = prepareHeatMap($input);

    # The starting and ending city blocks: [x, y].
    $lavaPool = [0, 0];
    $machinePartFactory = [array_key_last($heatMap[0]), array_key_last($heatMap)];

    return determineMinimalHeatLoss($heatMap, $lavaPool, $machinePartFactory);
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