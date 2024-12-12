<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;
use UnexpectedValueException;

class Day6
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/6', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Extracts essential information from the puzzle input.
     *
     * @param string[] $input The puzzle input.
     *
     * @return array<array{x: int, y: int, direction: string}, array{column: int[], row: int[]}>
     *     The initial guard location and an organized list of all obstacles.
     */
    function analyzeMap(array $input): array
    {
        $guardLocation = $obstacles = [];

        foreach ($input as $y => $line) {
            $guardX = strpos($line, '^');

            if ($guardX !== false) {
                $guardLocation = ['x' => $guardX, 'y' => $y, 'direction' => 'north'];
            }

            preg_match_all("/#/", $line, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as [$character, $obstacleX]) {
                $obstacles['column'][$obstacleX][] = $y;
                $obstacles['row'][$y][] = $obstacleX;
            }
        }

        return [$guardLocation, $obstacles];
    }

    /**
     * Provides a workaround to the min() and max() methods not allowing for empty arrays as input.
     *
     * @param callable $callback A callback to a min() or max() method.
     * @param int[]    $obstacles A list of obstacle coordinates in a specific part of a row or column.
     *
     * @return int|null The X or Y coordinate of the nearest obstacle, depending on the input.
     */
    function getNearestObstacle(callable $callback, array $obstacles): ?int
    {
        return empty($obstacles) ? null : $callback($obstacles);
    }

    /**
     * Generates a list of all turns made by the guard during their patrol, excluding every individual step.
     *
     * @param array{column: int[], row: int[]}         $obstacles The locations of obstacles on the map.
     * @param int                                      $x The horizontal coordinate at which the guards start walking.
     * @param int                                      $y The vertical coordinate at which the guard starts walking.
     * @param string                                   $direction The direction in which the guard is facing
     * @param array{x: int, y: int}                    $mapDimensions The horizontal and vertical size of the map.
     * @param array{x: int, y: int, direction: string} $turns A list of turns made by the guard.
     *
     * @return void This method passes the turns variable by reference.
     */
    function findGuardTurns(array $obstacles, int $x, int $y, string $direction, array $mapDimensions, array &$turns): void
    {
        # Filter the list of obstacles down to obstacles in the same column or row, depending on the direction.
        $obstacle = match ($direction) {
            'north' => $this->getNearestObstacle('max', array_filter($obstacles['column'][$x] ?? [], fn ($obstacle) => $obstacle < $y)),
            'south' => $this->getNearestObstacle('min', array_filter($obstacles['column'][$x] ?? [], fn ($obstacle) => $obstacle > $y)),
            'east' => $this->getNearestObstacle('min', array_filter($obstacles['row'][$y] ?? [], fn ($obstacle) => $obstacle > $x)),
            'west' => $this->getNearestObstacle('max', array_filter($obstacles['row'][$y] ?? [], fn ($obstacle) => $obstacle < $x)),
        };

        if ($obstacle === null) {
            return;
        }

        # Determine the exact location of this turn and the direction the guard will turn to.
        $turnX = match ($direction) {
            'north', 'south' => $x,
            'east' => $obstacle - 1,
            'west' => $obstacle + 1,
        };

        $turnY = match ($direction) {
            'east', 'west' => $y,
            'north' => $obstacle + 1,
            'south' => $obstacle - 1,
        };

        $newDirection = match ($direction) {
            'north' => 'east',
            'east' => 'south',
            'south' => 'west',
            'west' => 'north',
        };

        $newTurn = ['x' => $turnX, 'y' => $turnY, 'direction' => $newDirection];

        # If this turn has been visited before and the guard is heading in the same direction, they have entered a loop.
        if (in_array($newTurn, $turns)) {
            throw new UnexpectedValueException('A loop has occurred.');
        }

        $turns[] = $newTurn;

        # Perform these same actions until the guard either exits the map or enters a loop.
        $this->findGuardTurns($obstacles, $turnX, $turnY, $newDirection, $mapDimensions, $turns);
    }

    /**
     * Reconstructs the entire path walked by the guard based on the turns made.
     *
     * @param array{x: int, y: int, direction: string} $turns A list of turns made by the guard.
     * @param array{x: int, y: int}                    $mapDimensions The horizontal and vertical size of the map.
     *
     * @return string[] A list of stringified coordinates detailing the guard's patrol path.
     */
    function deduceGuardPath(array $turns, array $mapDimensions): array
    {
        $path = [];

        foreach ($turns as $index => ['x' => $currentX, 'y' => $currentY, 'direction' => $currentDirection]) {
            list('x' => $nextTurnX, 'y' => $nextTurnY, 'direction' => $nextDirection) = $turns[$index + 1] ?? null;

            # If there is no next turn, set the map edge as the final destination.
            if ($nextDirection === null) {
                $nextTurnX = match ($currentDirection) {
                    'north', 'south' => $currentX,
                    'east' => $mapDimensions['x'],
                    'west' => 0,
                };

                $nextTurnY = match ($currentDirection) {
                    'east', 'west' => $currentY,
                    'north' => 0,
                    'south' => $mapDimensions['y'],
                };
            }

            # Log each position in the guard's path until they reach the next turn.
            while ($currentX !== $nextTurnX || $currentY !== $nextTurnY) {
                $path[] = $currentX . ',' . $currentY;

                $currentX += $currentDirection === 'east' ? 1 : ($currentDirection === 'west' ? -1 : 0);
                $currentY += $currentDirection === 'north' ? -1 : ($currentDirection === 'south' ? 1 : 0);
            }
        }

        return $path;
    }

    /**
     * Iterates through all possible added obstacles on the guard's path to determine how many would send the guard into a loop.
     *
     * @param array{column: int[], row: int[]} $obstacles The locations of obstacles on the map.
     * @param string[]                         $path A list of stringified coordinates detailing the guard's patrol path.
     * @param array{x: int, y: int}            $mapDimensions The horizontal and vertical size of the map.
     *
     * @return int The amount of loops that could hypothetically be created by placing obstacles in the guard's path.
     */
    function detectPotentialLoops(array $obstacles, array $path, array $mapDimensions): int
    {
        $loops = 0;

        # Mark each position in the guard's path as a potential spot for an obstacle.
        $potentialObstacles = array_map(fn ($position) => explode(',', $position), array_unique($path));
        array_walk_recursive($potentialObstacles, fn (&$item) => $item = intval($item));

        # Get the guard's initial position, and in doing so, disqualify it as an obstacle.
        list($startX, $startY) = array_shift($potentialObstacles);

        foreach ($potentialObstacles as [$x, $y]) {
            $improvisedObstacles = $obstacles;
            $improvisedObstacles['column'][$x][] = $y;
            $improvisedObstacles['row'][$y][] = $x;

            try {
                $turns = [];
                $this->findGuardTurns($improvisedObstacles, $startX, $startY, 'north', $mapDimensions, $turns);
            } catch (UnexpectedValueException) {
                $loops++;
            }
        }

        return $loops;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    function partOne(array $input): int
    {
        $mapDimensions = ['x' => strlen($input[0]), 'y' => count($input)];
        list($guardPosition, $obstacles) = $this->analyzeMap($input);

        $turns = [$guardPosition];

        try {
            $this->findGuardTurns($obstacles, $guardPosition['x'], $guardPosition['y'], $guardPosition['direction'], $mapDimensions, $turns);
        } catch (UnexpectedValueException) {
        }

        $path = $this->deduceGuardPath($turns, $mapDimensions);

        $distinctPositions = array_unique($path);

        return count($distinctPositions);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    function partTwo(array $input): int
    {
        $mapDimensions = ['x' => strlen($input[0]), 'y' => count($input)];
        list($guardPosition, $obstacles) = $this->analyzeMap($input);

        $turns = [$guardPosition];

        try {
            $this->findGuardTurns($obstacles, $guardPosition['x'], $guardPosition['y'], $guardPosition['direction'], $mapDimensions, $turns);
        } catch (UnexpectedValueException) {
        }

        $path = $this->deduceGuardPath($turns, $mapDimensions);

        return $this->detectPotentialLoops($obstacles, $path, $mapDimensions);
    }

    ###############
    ### Results ###
    ###############

    function printSolutions(): void
    {
        $this->adventHelper->printSolutions(
            $this->partOne($this->input),
            $this->partTwo($this->input),
        );
    }
}

(new Day6())->printSolutions();