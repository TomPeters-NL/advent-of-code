<?php

$input = file('./input/5.txt');

$nice = 0;
$naughty = 0;
foreach ($input as $string) {
    preg_match_all('/(..).*\1/', $string, $pairs);
    preg_match_all('/(?=(.).\1)/', $string, $repeaters);

    $hasNoPairs = empty($pairs[0]);
    $hasNoRepeaters = empty($repeaters[0]);

    $hasNoPairs === false && $hasNoRepeaters === false ? $nice++ : $naughty++;
}

echo $nice . PHP_EOL;