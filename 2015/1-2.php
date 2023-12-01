<?php

$input = file('./input/1.txt');

$instructions = str_split($input[0]);

$floor = 0;
$position = 0;

do {
    $instructions[$position] === '(' ? $floor++ : $floor--;
    $position++;
} while ($floor >= 0);

echo $position . PHP_EOL;