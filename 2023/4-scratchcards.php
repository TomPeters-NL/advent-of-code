<?php

$input = file('./input/4.txt');

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $scratchCards = preg_replace('/ *Card \d+: /', '', $input);

    $points = 0;
    foreach ($scratchCards as $scratchCard) {
        $scoringNumbers = getScoringNumbers($scratchCard);
        $scoringAmount = count($scoringNumbers);

        if ($scoringAmount > 0) {
            $points += 2 ** ($scoringAmount - 1);
        }
    }

    return $points;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $scratchCards = preg_replace('/ *Card \d+: /', '', $input);

    $originalAmount = count($scratchCards);
    $scratchCards = array_combine(range(1, $originalAmount), $scratchCards);
    $inventory = array_fill(1, $originalAmount, 1);

    foreach ($scratchCards as $cardNumber => $scratchCard) {
        $frequency = $inventory[$cardNumber];
        $scoringNumbers = getScoringNumbers($scratchCard);
        $scoringCount = count($scoringNumbers);
        for ($a = 1; $a <= $frequency; $a++) {
            for ($b = 1; $b <= $scoringCount; $b++) {
                $inventory[$cardNumber + $b]++;
            }
        }
    }

    return array_sum($inventory);
}

/**
 * @return int[]
 */
function getScoringNumbers(string $scratchCard): array
{
    list($winningString, $potentialString) = explode('|', $scratchCard);

    $winningNumbers = explode(' ', trim($winningString));
    $winningNumbers = array_filter($winningNumbers);
    array_walk($winningNumbers, 'intval');

    $potentialNumbers = explode(' ', trim($potentialString));
    $potentialNumbers = array_filter($potentialNumbers);
    array_walk($potentialNumbers, 'intval');

    return array_intersect($winningNumbers, $potentialNumbers);
}

$solutionOne = partOne($input);
$solutionTwo = partTwo($input);

echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;