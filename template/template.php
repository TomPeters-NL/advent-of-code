<?php

declare(strict_types=1);

namespace AdventOfCode\Year202X;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class DayX
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/X', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    # Add logic here. :)

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): int
    {
        return 1;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        return 2;
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

(new DayX())->printSolutions();