<?php

$input = file('./input/3.txt');

$completeFlightPlan = str_split($input[0]);
$fleshSantaFlightPlan = [];
$robotSantaFlightPlan = [];
foreach ($completeFlightPlan as $index => $direction) {
    if ($index % 2 === 0) {
        $fleshSantaFlightPlan[] = $direction;
    } else {
        $robotSantaFlightPlan[] = $direction;
    }
}

$flightPlans = [$fleshSantaFlightPlan, $robotSantaFlightPlan];
$presents = $presents = ['0,0' => 2];
foreach ($flightPlans as $flightPlan) {
    $x = 0;
    $y = 0;

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
}

$housesVisited = count($presents);

echo $housesVisited . PHP_EOL;