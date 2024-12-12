<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day5
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/5', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Separates the puzzle input into two separate lists, one for page ordering rules and the other for the update pages.
     *
     * @param string[] $input The puzzle input.
     *
     * @return array{<int[][][], int[][]} The rules and update lists.
     */
    function separateInstructions(array $input): array
    {
        $splitIndex = array_search('', $input);

        $firstPart = array_slice($input, 0, $splitIndex);
        $preparedRules = array_map(
            fn ($ruleset) => array_map('intval', explode('|', $ruleset)),
            $firstPart,
        );

        $affectedPages = array_unique(
            array_merge(
                array_column($preparedRules, 0),
                array_column($preparedRules, 1),
            ),
        );

        $rules = array_fill_keys($affectedPages, ['before' => [], 'after' => []]);

        foreach ($preparedRules as [$firstPage, $secondPage]) {
            $rules[$firstPage]['before'][] = $secondPage;
            $rules[$secondPage]['after'][] = $firstPage;
        }

        $secondPart = array_slice($input, $splitIndex + 1);
        $updates = array_map(
            fn ($pages) => array_map('intval', explode(',', $pages)),
            $secondPart,
        );

        return [$rules, $updates];
    }

    /**
     * Determines whether the pages part of an update are in the expected order.
     *
     * @param int[][][] $rules The page ordering rules.
     * @param int[]     $pages The pages part of the update.
     *
     * @return bool Whether the provided pages are organized according to the rulesets.
     */
    function validatePageOrder(array $rules, array $pages): bool
    {
        $pageCount = count($pages);

        foreach ($pages as $pageIndex => $page) {
            $before = $rules[$page]['before'];
            $after = $rules[$page]['after'];

            for ($i = 0; $i < $pageIndex; $i++) {
                if (in_array($pages[$i], $before)) {
                    return false;
                }
            }

            for ($i = $pageIndex + 1; $i < $pageCount; $i++) {
                if (in_array($pages[$i], $after)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sorts the update pages into the correct order according to the provided rulesets.
     *
     * @param int[][][] $rules The page ordering rules.
     * @param int[]     $pages The pages part of the update.
     *
     * @return int[] The corrected version of the update.
     */
    function correctPageOrder(array $rules, array $pages): array
    {
        usort($pages, function ($a, $b) use ($rules) {
            $before = $rules[$a]['before'];
            $after = $rules[$a]['after'];

            if (in_array($b, $before)) {
                return -1;
            }

            if (in_array($b, $after)) {
                return 1;
            }

            return 0;
        });

        return $pages;
    }

    /**
     * Retrieves the median page from an update.
     *
     * @param int[] $pages
     *
     * @return int
     */
    function getMedianPage(array $pages): int
    {
        $median = floor(count($pages) / 2);

        return $pages[$median];
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
        list($rules, $updates) = $this->separateInstructions($input);

        $sum = 0;

        foreach ($updates as $pages) {
            if (!$this->validatePageOrder($rules, $pages)) {
                continue;
            }

            $sum += $this->getMedianPage($pages);
        }

        return $sum;
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        list($rules, $updates) = $this->separateInstructions($input);

        $sum = 0;

        foreach ($updates as $pages) {
            if ($this->validatePageOrder($rules, $pages)) {
                continue;
            }

            $correctedPages = $this->correctPageOrder($rules, $pages);

            $sum += $this->getMedianPage($correctedPages);
        }

        return $sum;
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

(new Day5())->printSolutions();