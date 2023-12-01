<?php

$input = file('./input/1.txt');

$output = 0;
foreach ($input as $line) {
    preg_match_all('/\d/', $line, $matches);
    $numbers = $matches[0];

    $firstDigit = $numbers[array_key_first($numbers)];
    $secondDigit = $numbers[array_key_last($numbers)];

    $output += (int)($firstDigit . $secondDigit);
}

echo $output . PHP_EOL;