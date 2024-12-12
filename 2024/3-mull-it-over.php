<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day3
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/3', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * @param string[] $input
     *
     * @return string[]
     */
    function parseInstructions(array $input, bool $includeConditionals = false): array
    {
        $uncorruptedInstructions = [];

        foreach ($input as $line) {
            $regex = $includeConditionals ? "/don't|do|mul\(\d+,\d+\)/" : "/mul\(\d+,\d+\)/";

            preg_match_all($regex, $line, $instructions);

            $uncorruptedInstructions = array_merge($uncorruptedInstructions, $instructions[0]);
        }

        return $uncorruptedInstructions;
    }

    /**
     * @param string[] $instructions
     */
    function processInstructions(array $instructions): int
    {
        $total = 0;
        $activeCalculator = true;

        foreach ($instructions as $instruction) {
            if (!$activeCalculator && str_starts_with($instruction, 'mul')) {
                continue;
            }

            if ($instruction === 'do') {
                $activeCalculator = true;
                continue;
            }

            if ($instruction === 'don\'t') {
                $activeCalculator = false;
                continue;
            }

            preg_match("/mul\((\d+),(\d+)\)/", $instruction, $operands);

            $total += (int) $operands[1] * (int) $operands[2];
        }

        return $total;
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
        $instructions = $this->parseInstructions($input);

        return $this->processInstructions($instructions);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $instructions = $this->parseInstructions($input, true);

        return $this->processInstructions($instructions);
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

(new Day3())->printSolutions();