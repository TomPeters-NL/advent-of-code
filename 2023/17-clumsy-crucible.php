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

class CityBlock
{
    public string $coordinates;

    public function __construct(
        public int $x,              # The X coordinate on the city map.
        public int $y,              # The Y coordinate on the city map.
        public int $dX,             # The change in X coordinate required to get here: -1, 0, 1.
        public int $dY              # The change in Y coordinate required to get here: -1, 0, 1.
    )
    {
        $this->coordinates = $x . ',' . $y;
    }
}

class Crucible
{
    public function __construct(
        public int $x,              # The X coordinate on the city map.
        public int $y,              # The Y coordinate on the city map.
        public int $dX,             # The change in X coordinate required to get here: -1, 0, 1.
        public int $dY,              # The change in Y coordinate required to get here: -1, 0, 1.
        public int $distance = 1,   # The amount of moves in a straight line.
        public int $CHL = 0         # Cumulative heat loss.
    ) {
    }

    public function getCityBlock(): CityBlock
    {
        return new CityBlock($this->x, $this->y, $this->dX, $this->dY);
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
    # Determine the city map limits.
    $minX = array_key_first($map);
    $maxX = array_key_last($map);
    $minY = array_key_first($map[0]);
    $maxY = array_key_last($map[0]);

    # Track which city blocks to explore.
    /** @var Crucible[] $queue */
    $queue = [];

    # Configure the starting location.
    [$startX, $startY] = $lavaPool;
    $queue[] = new Crucible($startX, $startY, 1, 0, 1, 0);
    $queue[] = new Crucible($startX, $startY, 0, 1, 1, 0);

    # Track the optimal heat loss per city block.
    $heatLoss = [];

    # Track which city blocks have been visited.
    /** @var CityBlock[] $visited */
    $visited = [];

    while (empty($queue) === false) {
        # Sort the queue by (ascending) CHL.
        uasort($queue, fn ($a, $b) => $a->CHL <=> $b->CHL);

        # Retrieve the current location.
        $crucible = array_shift($queue);

        # Check if the location has been visited before.
        $currentCityBlock = $crucible->getCityBlock();
        if (in_array($currentCityBlock, $visited) === true) {
            continue;
        } else {
            # If not, add it to the visited list now.
            $visited[] = $currentCityBlock;
        }

        # Log the cumulative heat loss for the current city block.
        $heatLoss[$currentCityBlock->coordinates] = $crucible->CHL;

        # Check if the goal location has been reached.
        if ([$currentCityBlock->x, $currentCityBlock->y] === $machinePartsFactory) {
            break;
        }

        # List the potential directions towards other city blocks.
        $potentialDirections = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        # Generate potential neighbouring city blocks.
        foreach ($potentialDirections as [$ndX, $ndY]) {
            # Set the new X and Y coordinates.
            [$newX, $newY] = [$currentCityBlock->x + $ndX, $currentCityBlock->y + $ndY];

            # Validate whether the neighbouring city block is on the map.
            if ($newX < $minX || $newX > $maxX || $newY < $minY || $newY > $maxY) {
                continue;
            }

            # Create the new city block.
            $newCityBlock = new CityBlock($newX, $newY, $ndX, $ndY);

            # Calculate the new cumulative heat loss.
            $newCHL = $crucible->CHL + $map[$newX][$newY];

            # Validate whether the neighbouring city block has not been visited and the new CHL is irrelevant.
            if (in_array($newCityBlock, $visited) === true) {
                continue;
            }

            # Add the neighbouring city block to the queue.
            $queue[] = new Crucible($newX, $newY, $ndX, $ndY, 1, $newCHL);
        }
    }

    return $heatLoss;
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

    # A list of optimal heat loss per block.
    $optimalHeatLoss = findPath($map, $lavaPool, $machinePartFactory);

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