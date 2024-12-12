<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day8
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/8', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Manipulates the puzzle input into an organized list of antennas.
     *
     * @param string[] $input The puzzle input.
     *
     * @return array<array-key, array<array{x: int, y: int}>> The X and Y coordinates of each antenna, grouped by their frequency.
     */
    function mapAntennas(array $input): array
    {
        $antennas = [];

        foreach ($input as $y => $line) {
            preg_match_all("/[^.]/", $line, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as [$frequency, $x]) {
                $antennas[$frequency][] = ['x' => $x, 'y' => $y];
            }
        }

        return $antennas;
    }

    /**
     * Determines whether an anti-node is positioned within the boundaries of the map.
     *
     * @param array{x: int, y: int} $boundaries The east (X) and south (Y) coordinates indicating the end of the map.
     * @param int                   $x The X coordinate of the anti-node's position.
     * @param int                   $y The Y coordinate of the anti-node's position.
     *
     * @return bool Whether a set of coordinates is within the boundaries of the map.
     */
    function isInBounds(array $boundaries, int $x, int $y): bool
    {
        return $x >= 0 && $x < $boundaries['x'] && $y >= 0 && $y < $boundaries['y'];
    }

    /**
     * Finds all possible anti-nodes for a list of antennas.
     *
     * @param array<array-key, array<array{x: int, y: int}>> $antennas The X and Y coordinates of each antenna, grouped by their frequency.
     * @param array{x: int, y: int}                          $boundaries The east (X) and south (Y) coordinates indicating the end of the map.
     * @param bool                                           $hasResonantHarmonics Determines whether anti-nodes are found whilst taking resonant harmonics into account.
     *
     * @return string[] A list of anti-nodes and their stringified X and Y coordinates.
     */
    function findAntiNodes(array $antennas, array $boundaries, bool $hasResonantHarmonics = false): array
    {
        $antiNodes = [];

        # For each antenna of a certain frequency, determine the anti-nodes with each other antenna of the same frequency.
        foreach ($antennas as $locations) {
            $locationCount = count($locations);

            foreach ($locations as $index => ['x' => $alphaX, 'y' => $alphaY]) {
                for ($n = $index + 1; $n < $locationCount; $n++) {
                    ['x' => $betaX, 'y' => $betaY] = $locations[$n];

                    # If resonant harmonics are enabled, add the original antenna locations.
                    if ($hasResonantHarmonics) {
                        $antiNodes[] = $alphaX . ',' . $alphaY;
                        $antiNodes[] = $betaX . ',' . $betaY;
                    }

                    # Determine the distance between antennas.
                    $dX = $betaX - $alphaX;
                    $dY = $betaY - $alphaY;

                    # Find all anti-nodes before the first antenna.
                    $nX = $alphaX - $dX;
                    $nY = $alphaY - $dY;

                    do {
                        if (!$this->isInBounds($boundaries, $nX, $nY)) {
                            break;
                        }

                        $antiNodes[] = $nX . ',' . $nY;

                        $nX -= $dX;
                        $nY -= $dY;
                    } while ($hasResonantHarmonics);

                    # Find all anti-nodes before the second antenna.
                    $nX = $betaX + $dX;
                    $nY = $betaY + $dY;

                    do {
                        if (!$this->isInBounds($boundaries, $nX, $nY)) {
                            break;
                        }

                        $antiNodes[] = $nX . ',' . $nY;

                        $nX += $dX;
                        $nY += $dY;
                    } while ($hasResonantHarmonics);
                }
            }
        }

        return $antiNodes;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partOne(array $input): int
    {
        $boundaries = ['x' => strlen($input[0]), 'y' => count($input)];

        $antennas = $this->mapAntennas($input);

        $antiNodes = $this->findAntiNodes($antennas, $boundaries);

        return count(array_unique($antiNodes));
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $boundaries = ['x' => strlen($input[0]), 'y' => count($input)];

        $antennas = $this->mapAntennas($input);

        $antiNodes = $this->findAntiNodes($antennas, $boundaries, true);

        return count(array_unique($antiNodes));
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $this->adventHelper->printSolutions(
            $this->partOne($this->input),
            $this->partTwo($this->input),
        );
    }
}

(new Day8())->printSolutions();