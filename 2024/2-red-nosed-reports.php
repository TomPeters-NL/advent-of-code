<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class Day2
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/2', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Organizes the list of reports and their tracked activity levels into processable data.
     *
     * @param string[] $input The puzzle input.
     *
     * @return int[][] A list of reports, each containing a list of levels of reactor activity.
     */
    function compileReports(array $input): array
    {
        $reports = [];

        foreach ($input as $line) {
            $reports[] = explode(' ', trim($line));
        }

        return $reports;
    }

    /**
     * Checks whether the report contains a series of (un)safe reactor activity.
     *
     * @param int[][] $report The report containing a single list of reactor activity levels.
     */
    function isSafeReport(array $report, bool $hasProblemDampener = false): bool
    {
        $trend = 'unknown';

        foreach ($report as $index => $level) {
            $nextLevel = $report[$index + 1] ?? null;

            # If there is no next level available, stop processing.
            if ($nextLevel === null) {
                break;
            }

            $change = $nextLevel - $level;

            if ($trend === 'unknown') {
                $trend = $change > 0 ? 'positive' : 'negative';
            }

            $isGradualChange = abs($change) >= 1 && abs($change) <= 3;
            $changeFollowsTrend = $trend === 'positive' ? $change > 0 : $change < 0;

            # If this level is safe, proceed to the next one.
            if ($isGradualChange && $changeFollowsTrend) {
                continue;
            }

            # Mark the entire report as unsafe for disruptive changes without a problem dampener.
            if (!$hasProblemDampener) {
                return false;
            }

            # Either the current or the next level is disruptive.
            # Duplicate the entire report thrice, remove a single level from each copy, and evaluate the alternative reports.
            # If any of the reports is safe, this means the report is safe with the problem dampener.
            # If none of the reports is safe, the entire original report is unsafe regardless.
            $reportAlpha = array_values($report);
            $reportBeta = array_values($report);
            $reportGamma = array_values($report);

            array_splice($reportAlpha, $index, 1);
            array_splice($reportBeta, $index - 1, 1);
            array_splice($reportGamma, $index + 1, 1);

            return $this->isSafeReport($reportAlpha) || $this->isSafeReport($reportBeta) || $this->isSafeReport($reportGamma);
        }

        return true;
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
        $reports = $this->compileReports($input);

        return array_reduce(
            $reports,
            function (int $safeReports, array $report) {
                return $safeReports + (int) $this->isSafeReport($report);
            },
            0,
        );
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $reports = $this->compileReports($input);

        return array_reduce(
            $reports,
            function (int $safeReports, array $report) {
                return $safeReports + (int) $this->isSafeReport($report, true);
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

(new Day2())->printSolutions();