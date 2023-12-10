<?php

$input = file('./input/2.txt');

/**
 * @return int[]
 */
function calculateMaximumDice(string $displays): array
{
    $maximums = ['red' => 0, 'green' => 0, 'blue' => 0];

    preg_match_all('/(\d+) (\w+)/', $displays, $matches);
    $amounts = $matches[1];
    $colors = $matches[2];

    for ($i = 0; $i < count($colors); $i++) {
        $amount = (int)$amounts[$i];
        $color = $colors[$i];

        if ($amount > $maximums[$color]) {
            $maximums[$color] = $amount;
        }
    }

    return $maximums;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $sum = 0;
    foreach ($input as $game) {
        list($gameName, $displays) = explode(':', $game);

        $gameId = (int)explode(' ', $gameName)[1];
        $maximums = calculateMaximumDice($displays);

        $hasEnoughRed = $maximums['red'] <= 12;
        $hasEnoughGreen = $maximums['green'] <= 13;
        $hasEnoughBlue = $maximums['blue'] <= 14;
        if ($hasEnoughRed === true && $hasEnoughGreen === true && $hasEnoughBlue === true) {
            $sum += $gameId;
        }
    }

    return $sum;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $power = 0;
    foreach ($input as $game) {
        $displays = explode(':', $game)[1];

        $maximums = calculateMaximumDice($displays);

        $power += $maximums['red'] * $maximums['green'] * $maximums['blue'];
    }

    return $power;
}

###############
### Results ###
###############

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;