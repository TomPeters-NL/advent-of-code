<?php

$input = file('./input/2.txt');

$squareFeet = 0;
foreach ($input as $present) {
    list($length, $width, $height) = explode('x', $present);

    $topBottom = $length * $width;
    $frontBack = $width * $height;
    $leftRight = $length * $height;

    $surface = 2 * $topBottom + 2 * $frontBack + 2 * $leftRight;
    $slack = min([$topBottom, $frontBack, $leftRight]);

    $squareFeet += $surface + $slack;
}

echo $squareFeet . PHP_EOL;