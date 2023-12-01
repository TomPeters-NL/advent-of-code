<?php

$input = file('./input/1.txt');

$output = 0;
foreach ($input as $line) {
    preg_match_all('/(?=(\d|one|two|three|four|five|six|seven|eight|nine))/', $line, $matches);

    $text = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    $digits = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    $numbers = str_replace($text, $digits, $matches[1]);

    $firstDigit = $numbers[array_key_first($numbers)];
    $secondDigit = $numbers[array_key_last($numbers)];

    $output += (int)($firstDigit . $secondDigit);
}

echo $output . PHP_EOL;