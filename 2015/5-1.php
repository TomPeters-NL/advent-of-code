<?php

$input = file('./input/5.txt');

$nice = 0;
$naughty = 0;
foreach ($input as $string) {
    preg_match_all('/[aeiou]/', $string, $vowels);
    preg_match_all('/(.)\1/', $string, $doubles);
    preg_match_all('/ab|cd|pq|xy/', $string, $combinations);

    $hasThreeVowels = count($vowels[0]) >= 3;
    $hasNoDoubles = empty($doubles[0]);
    $hasNoCombinations = empty($combinations[0]);

    $hasThreeVowels === true && $hasNoDoubles === false && $hasNoCombinations === true ? $nice++ : $naughty++;
}

echo $nice . PHP_EOL;