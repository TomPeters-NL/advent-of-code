<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/7');

#################
### Solutions ###
#################

function getHandStrength(string $hand, bool $jokers = false): int
{
    $list = count_chars($hand, 1);
    $amount = count($list);

    if ($jokers === true && str_contains($hand, 'J') === true && $hand !== 'JJJJJ') {
        arsort($list);
        $characters = array_keys($list);
        $mostFrequentCharacter = $characters[0] !== 74 ? $characters[0] : $characters[1];

        $hand = str_replace('J', chr($mostFrequentCharacter), $hand);
        $list = count_chars($hand, 1);
        $amount = count($list);
    }

    return match ($amount) {
        1 => 0, // Five of a kind.
        2 => in_array(4, $list) === true ? 1 : 2, // Four of a kind or full house.
        3 => in_array(3, $list) === true ? 3 : 4, // Three of a kind or two pairs.
        4 => 5, // One pair.
        default => 6, // High card.
    };
}

function solveHandTie(array $handDataA, array $handDataB, bool $jokers = false): int
{
    $handA = $handDataA['hand'];
    $handB = $handDataB['hand'];

    $length = strlen($handA);
    for ($i = 0; $i < $length; $i++) {
        $cardA = $handA[$i];
        $cardB = $handB[$i];

        if ($cardA !== $cardB) {
            $distribution = $jokers === false ? 'AKQJT98765432' : 'AKQT98765432J';

            $strengthA = strpos($distribution, $cardA);
            $strengthB = strpos($distribution, $cardB);

            return $strengthA > $strengthB ? 1 : -1;
        }
    }
}

/**
 * @param string[] $input
 */
function partOneAndTwo(array $input, bool $jokers): int
{
    $leaderboard = [];
    foreach ($input as $row) {
        list($hand, $price) = explode(' ', $row);

        $handStrength = getHandStrength($hand, $jokers);
        $leaderboard[$handStrength][] = ['hand' => $hand, 'price' => (int)$price];
    }

    $simpleLeaderboard = [];
    ksort($leaderboard);
    foreach ($leaderboard as $strengthLevel) {
        usort($strengthLevel, fn($x, $y) => solveHandTie($x, $y, $jokers));

        $simpleLeaderboard = array_merge($simpleLeaderboard, $strengthLevel);
    }

    $winnings = 0;
    $leaderboardLength = count($simpleLeaderboard);
    foreach ($simpleLeaderboard as $hand) {
        $winnings += $hand['price'] * $leaderboardLength--;
    }

    return $winnings;
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));