<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day11
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/11', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Blinks and counts the amount of stones observed.
     *
     * @param int   $stone The current number engraved on the stone.
     * @param int   $blinks The amount of blinks (recursion) remaining.
     * @param int[] $stoneCache A list of the number of stones per combination of stone number and remaining blinks.
     *
     * @return int The total number of stones observed.
     */
    function blinkAndObserve(int $stone, int $blinks, array &$stoneCache): int
    {
        # If no blinks remain, simply return the final stone.
        if ($blinks === 0) {
            return 1;
        }

        $cacheKey = $stone . '-' . $blinks;

        # If the combination of current stone number and the amount of blinks remaining is already in the cache, the amount of stones is known without need of further recursion.
        if (array_key_exists($cacheKey, $stoneCache)) {
            return $stoneCache[$cacheKey];
        }

        $blinks--;

        if ($stone === 0) {
            $stones = $this->blinkAndObserve(1, $blinks, $stoneCache);
        } elseif (($digits = strlen((string) $stone)) % 2 === 0) {
            $firstHalf = (int) substr((string) $stone, 0, $digits / 2);
            $secondHalf = (int) substr((string) $stone, $digits / 2);

            $stones = $this->blinkAndObserve($firstHalf, $blinks, $stoneCache) + $this->blinkAndObserve($secondHalf, $blinks, $stoneCache);
        } else {
            $stones = $this->blinkAndObserve(2024 * $stone, $blinks, $stoneCache);
        }

        # Cache the result to prevent unnecessary recursion in future blinks.
        $stoneCache[$cacheKey] = $stones;

        return $stones;
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
        $totalStones = 0;

        $stoneCache = [];
        $stones = array_map('intval', explode(' ', $input[0]));

        foreach ($stones as $stone) {
            $totalStones += $this->blinkAndObserve($stone, 25, $stoneCache);
        }

        return $totalStones;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $totalStones = 0;

        $stoneCache = [];
        $stones = array_map('intval', explode(' ', $input[0]));

        foreach ($stones as $stone) {
            $totalStones += $this->blinkAndObserve($stone, 75, $stoneCache);
        }

        return $totalStones;
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

(new Day11())->printSolutions();