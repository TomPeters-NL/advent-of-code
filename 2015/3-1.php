<?php

$input = file('./input/3.txt');

$flightPlan = str_split($input[0]);

$x = 0;
$y = 0;
$presents = ['0,0' => 1];

foreach ($flightPlan as $direction) {
    match($direction) {
        '^' => $y++,
        '>' => $x++,
        'v' => $y--,
        '<' => $x--,
    };

    $coordinates = $x . ',' . $y;
    array_key_exists($coordinates, $presents) === true ? $presents[$coordinates]++ : $presents[$coordinates] = 0;
}

$housesVisited = count($presents);

echo $housesVisited . PHP_EOL;