<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A robot patrolling the lobby of Easter Bunny Headquarters. */
class Robot
{
    /**
     * @param int $x  The current horizontal position in the lobby.
     * @param int $y  The current vertical position in the lobby.
     * @param int $dX The horizontal movement per second.
     * @param int $dY The vertical movement per second.
     */
    public function __construct(
        public int $x,
        public int $y,
        public int $dX,
        public int $dY,
    ) {
    }
}

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day14
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/14', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Generates a list of all robots patrolling the lobby.
     *
     * @return Robot[] A list of robots.
     */
    private function identifyRobots(): array
    {
        $robots = [];

        foreach ($this->input as $description) {
            preg_match_all('/-?\d+/', $description, $numbers);

            $robots[] = new Robot(
                (int) $numbers[0][0],
                (int) $numbers[0][1],
                (int) $numbers[0][2],
                (int) $numbers[0][3],
            );
        }

        return $robots;
    }

    /**
     * Calculates the position of each of the provided robots after the specified amount of seconds have passed.
     *
     * @param Robot[] $robots      A list of robots patrolling the lobby.
     * @param int     $seconds     Determines after how many seconds the positions should be calculated.
     * @param int     $lobbyWidth  The width of the lobby.
     * @param int     $lobbyLength The length of the lobby.
     *
     * @return Robot[] A list of robots with updated positions.
     */
    private function updateRobotLocations(array $robots, int $seconds, int $lobbyWidth, int $lobbyLength): array
    {
        foreach ($robots as $robot) {
            $nX = ($robot->x + $seconds * $robot->dX) % $lobbyWidth;
            $nY = ($robot->y + $seconds * $robot->dY) % $lobbyLength;

            if ($nX < 0) $nX += $lobbyWidth;
            if ($nX >= $lobbyWidth) $nX -= $lobbyWidth;
            if ($nY < 0) $nY += $lobbyLength;
            if ($nY >= $lobbyLength) $nY -= $lobbyLength;

            $robot->x = $nX;
            $robot->y = $nY;
        }

        return $robots;
    }

    /**
     * Calculates the lobby safety factor based on the positions of the patrolling robots.
     *
     * @param Robot[] $robots      A list of robots patrolling the lobby.
     * @param int     $lobbyWidth  The width of the lobby.
     * @param int     $lobbyLength The length of the lobby.
     *
     * @return int The safety factor.
     */
    private function calculateSafetyFactor(array $robots, int $lobbyWidth, int $lobbyLength): int
    {
        $northWestQuadrant = $northEastQuadrant = $southWestQuadrant = $southEastQuadrant = 0;
        $medianX = (int) ceil($lobbyWidth / 2) - 1;
        $medianY = (int) ceil($lobbyLength / 2) - 1;

        foreach ($robots as $robot) {
            $quadrantY = match (true) {
                $robot->y < $medianY => 'north',
                $robot->y > $medianY => 'south',
                default => null,
            };

            $quadrantX = match (true) {
                $robot->x < $medianX => 'west',
                $robot->x > $medianX => 'east',
                default => null,
            };

            if ($quadrantX === null || $quadrantY === null) {
                continue;
            }

            match ($quadrantY . $quadrantX) {
                'northwest' => $northWestQuadrant++,
                'northeast' => $northEastQuadrant++,
                'southwest' => $southWestQuadrant++,
                'southeast' => $southEastQuadrant++,
            };
        }

        return $northWestQuadrant * $northEastQuadrant * $southWestQuadrant * $southEastQuadrant;
    }

    /**
     * Visualizes and stores the positions of all robots in a file, for each second in the specified time limit.
     *
     * @param Robot[] $robots      A list of robots patrolling the lobby.
     * @param int     $lobbyWidth  The width of the lobby.
     * @param int     $lobbyLength The length of the lobby.
     * @param int     $timeLimit   The limit for the amount of visualizations in seconds.
     *
     * @return void No return value, as finding the Easter Egg requires human analysis.
     */
    private function printRobotLocations(array $robots, int $lobbyWidth, int $lobbyLength, int $timeLimit): void
    {
        if (file_exists('output/14')) {
            unlink('output/14');
        }

        file_put_contents('output/14', '');

        $room = array_fill(0, $lobbyLength, str_repeat('.', $lobbyWidth));

        $second = 0;
        while ($second <= $timeLimit) {
            # Create an empty room.
            $currentRoom = $room;

            # Set the positions of each of the robots.
            foreach ($robots as $robot) {
                $currentRoom[$robot->y][$robot->x] = '#';
            }

            # Add the current second to the rendering and write it to the output file.
            array_unshift($currentRoom, "Second: $second");
            file_put_contents('output/14', print_r($currentRoom, true), FILE_APPEND);

            # Calculate the positions of the robots after another second and increase the second counters.
            $robots = $this->updateRobotLocations($robots, 1, $lobbyWidth, $lobbyLength);
            $second++;
        }
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): int
    {
        $robots = $this->identifyRobots();

        $roomWidth = 101;
        $roomLength = 103;

        $robots = $this->updateRobotLocations($robots, 100, $roomWidth, $roomLength);

        return $this->calculateSafetyFactor($robots, $roomWidth, $roomLength);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): string
    {
        $robots = $this->identifyRobots();

        $roomWidth = 101;
        $roomLength = 103;

        $this->printRobotLocations($robots, $roomWidth, $roomLength, 7000);

        return 'N/A';
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $this->adventHelper->printSolutions(
            $this->partOne(),
            $this->partTwo(),
        );
    }
}

(new Day14())->printSolutions();