<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day7
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/7', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Parses the provided input into a usable format.
     *
     * @param string[] $input The puzzle input.
     *
     * @return array{target: int, numbers: int[]} The target value and the list of test numbers.
     */
    function processCalibrationEquations(array $input): array
    {
        $equations = [];

        foreach ($input as $equation) {
            [$target, $numbers] = explode(': ', $equation);

            $equations[] = [
                'target' => (int) $target,
                'numbers' => array_map('intval', explode(' ', trim($numbers))),
            ];
        }

        return $equations;
    }

    /**
     * Calculates the outcomes of all potential operations on the provided test values, assuming left-to-right evaluation.
     *
     * @param int[] $outcomes A list of potential outcomes.
     * @param array $numbers The list of test values.
     * @param bool  $includeConcatenation Determines whether the concatenation operator can be used.
     *
     * @return void The outcomes array is passed by reference.
     */
    function calculatePotentialResults(array &$outcomes, array $numbers, bool $includeConcatenation = false): void
    {
        if (count($numbers) === 1) {
            $outcomes[] = array_shift($numbers);

            return;
        }

        $numberOne = array_shift($numbers);
        $numberTwo = array_shift($numbers);

        $potentialValueOne = $numberOne + $numberTwo;
        $iterationOne = [$potentialValueOne, ...$numbers];
        $this->calculatePotentialResults($outcomes, $iterationOne, $includeConcatenation);

        $potentialValueTwo = $numberOne * $numberTwo;
        $iterationTwo = [$potentialValueTwo, ...$numbers];
        $this->calculatePotentialResults($outcomes, $iterationTwo, $includeConcatenation);

        if ($includeConcatenation === true) {
            $potentialValueThree = (int) ($numberOne . $numberTwo);
            $iterationThree = [$potentialValueThree, ...$numbers];
            $this->calculatePotentialResults($outcomes, $iterationThree, $includeConcatenation);
        }
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
        $calibrationResult = 0;

        $equations = $this->processCalibrationEquations($input);

        foreach ($equations as ['target' => $target, 'numbers' => $numbers]) {
            $potentialResults = [];

            $this->calculatePotentialResults($potentialResults, $numbers);

            if (in_array($target, $potentialResults)) {
                $calibrationResult += $target;
            }
        }

        return $calibrationResult;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $calibrationResult = 0;

        $equations = $this->processCalibrationEquations($input);

        foreach ($equations as ['target' => $target, 'numbers' => $numbers]) {
            $potentialResults = [];

            $this->calculatePotentialResults($potentialResults, $numbers, true);

            if (in_array($target, $potentialResults)) {
                $calibrationResult += $target;
            }
        }

        return $calibrationResult;
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

(new Day7())->printSolutions();