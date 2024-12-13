<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day4
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/4', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Manipulates the puzzle input so the word search becomes a two-dimensional array.
     *
     * @param string[] $input The puzzle input.
     *
     * @return string[][] The prepared word search, in which each line has been turned into a list of characters.
     */
    function prepareWordSearch(array $input): array
    {
        $wordSearch = [];

        foreach ($input as $line) {
            $wordSearch[] = str_split(trim($line));
        }

        return $wordSearch;
    }

    /**
     * This recursive method finds specified letters along a provided direction and logs any complete words found (i.e., "XMAS", "MAS").
     *
     * @param string[][] $wordSearch The word search page.
     * @param int        $x The X coordinate of the current letter.
     * @param int        $y The Y coordinate of the current letter.
     * @param int|null   $dX The horizontal direction along which to perform the search.
     * @param int|null   $dY The vertical direction along which to perform the search.
     * @param string     $letter The letter that is being searched for.
     * @param string[]   $occurrences A log of correct words.
     * @param bool       $diagonalsOnly Determines whether straight lines should be excluded from the search.
     *
     * @return void Results are collected via "passing by reference" using the occurrences array.
     */
    function findLetters(array $wordSearch, int $x, int $y, int | null $dX, int | null $dY, string $letter, array &$occurrences, bool $diagonalsOnly = false): void
    {
        $countY = count($wordSearch);
        $countX = count($wordSearch[0]);

        # If no direction has been specified, look all around the letter.
        if ($dX === null && $dY === null) {
            $vectorsX = [-1, 0, 1];
            $vectorsY = [-1, 0, 1];
        } else {
            $vectorsX = [$dX];
            $vectorsY = [$dY];
        }

        foreach ($vectorsY as $dY) {
            foreach ($vectorsX as $dX) {
                # Ignore the current letter, or any straight lines if only diagonals are allowed.
                if ($dX === 0 && $dY === 0 || ($diagonalsOnly && ($dX === 0 || $dY === 0))) {
                    continue;
                }

                $nX = $x + $dX;
                $nY = $y + $dY;

                # Ensure that the next coordinate is not out-of-bounds.
                if ($nX < 0 || $nX >= $countX || $nY < 0 || $nY >= $countY) {
                    continue;
                }

                if ($wordSearch[$nY][$nX] === $letter) {
                    if ($letter === 'S') {
                        # Logs the location of the letter "A", which is relevant for the X-MAS puzzle.
                        $occurrences[] = ($nX - $dX) . ',' . ($nY - $dY);

                        continue;
                    }

                    $nextLetter = $letter === 'M' ? 'A' : 'S';

                    $this->findLetters($wordSearch, $nX, $nY, $dX, $dY, $nextLetter, $occurrences, $diagonalsOnly);
                }
            }
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
        $wordSearch = $this->prepareWordSearch($input);

        $occurrences = [];

        foreach ($wordSearch as $y => $line) {
            foreach ($line as $x => $letter) {
                if ($letter !== 'X') {
                    continue;
                }

                $this->findLetters($wordSearch, $x, $y, null, null, 'M', $occurrences);
            }
        }

        return count($occurrences);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $wordSearch = $this->prepareWordSearch($input);

        $occurrences = [];

        foreach ($wordSearch as $y => $line) {
            foreach ($line as $x => $letter) {
                if ($letter !== 'M') {
                    continue;
                }

                $this->findLetters($wordSearch, $x, $y, null, null, 'A', $occurrences, true);
            }
        }

        $coordinateFrequency = array_count_values($occurrences);

        return array_reduce(
            $coordinateFrequency,
            function ($xmas, $frequency) {
                return $xmas + (int) ($frequency > 1);
            },
            0,
        );
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

(new Day4())->printSolutions();