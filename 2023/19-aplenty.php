<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/19.txt', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

class RatingRange
{
    public int $startX = 1;
    public int $endX = 4000;
    public int $startM = 1;
    public int $endM = 4000;
    public int $startA = 1;
    public int $endA = 4000;
    public int $startS = 1;
    public int $endS = 4000;

    public function product(): int
    {
        return ($this->endX - $this->startX + 1) * ($this->endM - $this->startM + 1) * ($this->endA - $this->startA + 1) * ($this->endS - $this->startS + 1);
    }
}

/**
 * Retrieves a list of part ratings.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] A list of part ratings, separated per part.
 */
function getParts(array $input): array
{
    # The workflows and parts are separated by an empty line.
    $separator = array_search('', $input);

    $partCount = count($input) - $separator;
    $unprocessedParts = array_slice($input, $separator + 1, $partCount);
    $parts = [];

    # Extract the category ratings per part and list them under the category as index.
    foreach ($unprocessedParts as $rawPart) {
        preg_match('/^{x=(\d+),m=(\d+),a=(\d+),s=(\d+)}$/', $rawPart, $ratings);

        [$ignore, $x, $m, $a, $s] = $ratings;

        $parts[] = ['x' => (int)$x, 'm' => (int)$m, 'a' => (int)$a, 's' => (int)$s];
    }

    return $parts;
}

/**
 * Retrieves a list of workflows and their rules.
 *
 * @param string[] $input The puzzle input.
 *
 * @return string[][] A list of workflow rules, separated and indexed by workflow name.
 */
function getWorkflows(array $input): array
{
    # The workflows and parts are separated by an empty line.
    $separator = array_search('', $input);

    $unprocessedWorkflows = array_slice($input, 0, $separator);
    $workflows = [];

    # Extract the name and rules from the raw string, and add them to the workflows.
    foreach ($unprocessedWorkflows as $rawWorkflow) {
        $name = substr($rawWorkflow, 0, strpos($rawWorkflow, '{'));

        $rawRules = preg_replace('/^[a-z]+{|}/', '', $rawWorkflow);
        $rules = explode(',', $rawRules);

        foreach ($rules as $rule) {
            $workflows[$name][] = $rule;
        }
    }

    return $workflows;
}

/**
 * Processes the list of parts using the workflow rules to determine whether they are approved or rejected.
 *
 * @param int[][]    $parts     A list of part ratings, separated per part.
 * @param string[][] $workflows A list of workflow rules, separated and indexed by workflow name.
 *
 * @return int[][] A list of approved part ratings, separated per part.
 */
function getApprovedParts(array $parts, array $workflows): array
{
    $approvedParts = [];
    $workflowQueue = ['in' => $parts]; # "in" is the name of the first workflow.

    while (empty($workflowQueue) === false) {
        $workflow = $workflows[array_key_first($workflowQueue)];
        $queuedParts = array_shift($workflowQueue);

        foreach ($workflow as $rule) {
            $isFallback = str_contains($rule, ':') === false;
            $comparisonType = str_contains($rule, '<') ? '<' : '>';

            if ($isFallback === true) { # If a fallback rule, immediately add all remaining parts to the queue for the fallback workflow.
                $queuedWorkflow = $workflowQueue[$rule] ?? [];
                $workflowQueue[$rule] = array_merge($queuedWorkflow, $queuedParts);
            } else { # If it is a "normal" rule, extract the category comparison and the potential next workflow.
                [$comparison, $nextWorkflow] = explode(':', $rule);
                $category = substr($comparison, 0, 1);
                $ruleValue = (int)substr($comparison, 2);

                foreach ($queuedParts as $partNumber => $queuedPart) { # Compare each part against the rule criteria.
                    $match = match ($comparisonType) {
                        '<' => $queuedPart[$category] < $ruleValue,
                        '>' => $queuedPart[$category] > $ruleValue,
                    };

                    if ($match === true) { # When the part matches, queue it for the next workflow and remove it from the queued parts.
                        $workflowQueue[$nextWorkflow][] = $queuedPart;
                        unset($queuedParts[$partNumber]);
                    }
                }
            }
        }

        # Before continuing to the next workflow, process rejected and approved parts.
        if (array_key_exists('A', $workflowQueue) === true) {
            $approvedParts = array_merge($approvedParts, $workflowQueue['A']);
        }

        unset($workflowQueue['A']);
        unset($workflowQueue['R']);
    }

    return $approvedParts;
}

function getApprovedRatingRanges(array $workflows): array
{
    $approvedRanges = [];

    $workflowQueue = ['in' => new RatingRange()];

    while (empty($workflowQueue) === false) {
        $workflowName = array_key_first($workflowQueue);
        $workflow = $workflows[$workflowName];

        $queuedRange = array_shift($workflowQueue);

        foreach ($workflow as $rule) {
            $isFallback = str_contains($rule, ':') === false;
            $comparisonType = substr($rule, 1, 1);

            $range = clone $queuedRange;

            if ($isFallback === true) {
                if ($rule === 'A') {
                    $approvedRanges[] = $range;
                } elseif ($rule !== 'R') {
                    $workflowQueue[$rule] = $range;
                }
            } else {
                [$comparison, $nextWorkflow] = explode(':', $rule);
                $category = substr($comparison, 0, 1);
                $ruleValue = (int)substr($comparison, 2);

                $startProperty = 'start' . strtoupper($category);
                $endProperty = 'end' . strtoupper($category);


                if ($comparisonType === '<') {
                    $range->$endProperty = $ruleValue - 1;
                    $queuedRange->$startProperty = $ruleValue;
                } else {
                    $range->$startProperty = $ruleValue + 1;
                    $queuedRange->$endProperty = $ruleValue;
                }

                if ($nextWorkflow === 'A') {
                    $approvedRanges[] = $range;
                } elseif ($nextWorkflow !== 'R') {
                    $workflowQueue[$nextWorkflow] = $range;
                }
            }
        }
    }

    return $approvedRanges;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $workflows = getWorkflows($input);
    $parts = getParts($input);

    $approvedParts = getApprovedParts($parts, $workflows);

    return array_reduce($approvedParts, fn($sum, $ratings) => $sum + array_sum($ratings));
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $workflows = getWorkflows($input);

    $approvedRanges = getApprovedRatingRanges($workflows);

    return array_reduce($approvedRanges, fn($sum, $range) => $sum + $range->product());
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));