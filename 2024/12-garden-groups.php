<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

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

    private function generateCropMap(): array
    {
        $crops = [];

        return $crops;
    }

    private function mapPlot(array $garden, int $x, int $y, array &$mapped): array
    {
        $crops = [];

        $crops[] = [
            'crop' => '',
            'position' => $x . ',' . $y,
            'sides' => 0,
        ];

        return $crops;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): int
    {
        $price = 0;

        return $price;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        $price = 0;

        return $price;
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

(new Day12())->printSolutions();