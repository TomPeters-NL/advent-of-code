<?php

$input = file('./input/1.txt');

$instructions = $input[0];

$up = substr_count($instructions, '(');
$down = substr_count($instructions, ')');

echo ($up - $down) . PHP_EOL;