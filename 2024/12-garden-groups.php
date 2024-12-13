<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A single plot in the crop garden. */
class Plot
{
    /**
     * @param string $crop             The type of crop planted on this plot.
     * @param int    $x                The horizontal location of the plot in the garden.
     * @param int    $y                The vertical location of the plot in the garden.
     * @param string $location         A stringified version of the X and Y coordinates.
     * @param bool   $relatedNorth     Whether the plot to the top of this one has the same crop planted.
     * @param bool   $relatedNorthEast Whether the plot to the top-right this one has the same crop planted.
     * @param bool   $relatedEast      Whether the plot to the right of this one has the same crop planted.
     * @param bool   $relatedSouthEast Whether the plot to the bottom-right of this one has the same crop planted.
     * @param bool   $relatedSouth     Whether the plot to the bottom of this one has the same crop planted.
     * @param bool   $relatedSouthWest Whether the plot to the bottom-left of this one has the same crop planted.
     * @param bool   $relatedWest      Whether the plot to the left of this one has the same crop planted.
     * @param bool   $relatedNorthWest Whether the plot to the top-right of this one has the same crop planted.
     */
    public function __construct(
        public string $crop,
        public int $x,
        public int $y,
        public string $location = '',
        public bool $relatedNorth = false,
        public bool $relatedNorthEast = false,
        public bool $relatedEast = false,
        public bool $relatedSouthEast = false,
        public bool $relatedSouth = false,
        public bool $relatedSouthWest = false,
        public bool $relatedWest = false,
        public bool $relatedNorthWest = false,
    ) {
        $this->location = $x . ',' . $y;
    }

    /**
     * Determines the total amount of fences required for this plot.
     *
     * @return int The total amount of fences.
     */
    public function countFences(): int
    {
        $fences = 0;

        if (!$this->relatedNorth) $fences++;
        if (!$this->relatedEast) $fences++;
        if (!$this->relatedSouth) $fences++;
        if (!$this->relatedWest) $fences++;

        return $fences;
    }

    /**
     * Determines the total amount of crop zone corners on this plot.
     *
     * @return int The total amount of corners.
     */
    public function countCorners(): int
    {
        $corners = 0;

        if (!$this->relatedNorth && !$this->relatedEast) $corners++;
        if (!$this->relatedEast && !$this->relatedSouth) $corners++;
        if (!$this->relatedSouth && !$this->relatedWest) $corners++;
        if (!$this->relatedWest && !$this->relatedNorth) $corners++;

        if ($this->relatedNorth && $this->relatedEast && !$this->relatedNorthEast) $corners++;
        if ($this->relatedEast && $this->relatedSouth && !$this->relatedSouthEast) $corners++;
        if ($this->relatedSouth && $this->relatedWest && !$this->relatedSouthWest) $corners++;
        if ($this->relatedWest && $this->relatedNorth && !$this->relatedNorthWest) $corners++;

        return $corners;
    }
}

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day12
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/12', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Generates a map of different zones in the garden.
     * Each zone contains a single continuous fenced-off area of plots containing the same type of crop.
     *
     * @return array<array<Plot[]>> A list of plots in each crop zone.
     */
    private function generateZoneMap(): array
    {
        $zones = [];

        $garden = $this->adventHelper->convertStringListToMap($this->input);
        $mapped = [];

        foreach ($garden as $y => $row) {
            foreach ($row as $x => $plot) {
                if (in_array($x . ',' . $y, $mapped)) {
                    continue;
                }

                $zones[$plot][] = $this->mapPlots($garden, $x, $y, $mapped);
            }
        }

        return $zones;
    }

    /**
     * Recursively maps all plots within the current zone, alongside their relations to their neighbors.
     *
     * @param string[][] $garden A map of the garden.
     * @param int        $x      The X coordinate of the current plot.
     * @param int        $y      The Y coordinate of the current plot.
     * @param string[]   $mapped A list of stringified plot coordinates tracking which ones have already been mapped.
     *
     * @return Plot[] A list of plots in the current crop zone.
     */
    private function mapPlots(array $garden, int $x, int $y, array &$mapped): array
    {
        $maximumX = count($garden[0]);
        $maximumY = count($garden);
        $vectors = [
            'north' => [0, -1],
            'northeast' => [1, -1],
            'east' => [1, 0],
            'southeast' => [1, 1],
            'south' => [0, 1],
            'southwest' => [-1, 1],
            'west' => [-1, 0],
            'northwest' => [-1, -1],
        ];

        $plots = [];
        $crop = $garden[$y][$x];
        $plot = new Plot($crop, $x, $y);

        $mapped[] = $plot->location;

        foreach ($vectors as $direction => [$dX, $dY]) {
            $nX = $plot->x + $dX;
            $nY = $plot->y + $dY;

            # Skip this neighbor if the plot is out-of-bounds or the crop type doesn't match the one in the current plot.
            if ($nX < 0 || $nX >= $maximumX || $nY < 0 || $nY >= $maximumY || $crop !== $garden[$nY][$nX]) {
                continue;
            }

            match ($direction) {
                'north' => $plot->relatedNorth = true,
                'northeast' => $plot->relatedNorthEast = true,
                'east' => $plot->relatedEast = true,
                'southeast' => $plot->relatedSouthEast = true,
                'south' => $plot->relatedSouth = true,
                'southwest' => $plot->relatedSouthWest = true,
                'west' => $plot->relatedWest = true,
                'northwest' => $plot->relatedNorthWest = true,
            };

            # Do not map the neighbor if it has already been mapped or is located in a diagonal direction.
            if (in_array($nX . ',' . $nY, $mapped) || in_array($direction, ['northeast', 'southeast', 'southwest', 'northwest'])) {
                continue;
            }

            $plots = array_merge($plots, $this->mapPlots($garden, $nX, $nY, $mapped));
        }

        $plots[] = $plot;

        return $plots;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(array $zones): int
    {
        $price = 0;

        foreach ($zones as $zone) {
            foreach ($zone as $plots) {
                $area = count($plots);

                $fences = array_reduce($plots, fn($total, $plot) => $total + $plot->countFences());

                $price += $area * $fences;
            }
        }

        return $price;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(array $zones): int
    {
        $price = 0;

        foreach ($zones as $zone) {
            foreach ($zone as $plots) {
                $area = count($plots);

                $corners = array_reduce($plots, fn($total, $plot) => $total + $plot->countCorners());

                $price += $area * $corners;
            }
        }

        return $price;
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $zones = $this->generateZoneMap();

        $this->adventHelper->printSolutions(
            $this->partOne($zones),
            $this->partTwo($zones),
        );
    }
}

(new Day12())->printSolutions();