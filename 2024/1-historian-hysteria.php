<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day1
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/1', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Organizes the two lists of location IDs provided by the historians.
     *
     * @param string[] $input The puzzle input.
     *
     * @return int[][] Two lists containing sorted location IDs from both historian groups.
     */
    function organizeLocationIds(array $input): array
    {
        $organizedLists = [];

        foreach ($input as $line) {
            list($first, $second) = explode('   ', $line);

            $organizedLists[0][] = (int) $first;
            $organizedLists[1][] = (int) $second;
        }

        sort($organizedLists[0]);
        sort($organizedLists[1]);

        return $organizedLists;
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
        $totalDistance = 0;

        list($firstList, $secondList) = $this->organizeLocationIds($input);

        for ($i = 0; $i < count($firstList); $i++) {
            $totalDistance += abs($secondList[$i] - $firstList[$i]);
        }

        return $totalDistance;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $similarityScore = 0;

        list($firstList, $secondList) = $this->organizeLocationIds($input);

        foreach ($firstList as $locationId) {
            $similarityScore += $locationId * count(array_keys($secondList, $locationId));
        }

        return $similarityScore;
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

(new Day1())->printSolutions();