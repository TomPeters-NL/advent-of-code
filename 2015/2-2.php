<?php

$input = file('./input/2.txt');

$ribbonLength = 0;
foreach ($input as $present) {
    list($length, $width, $height) = explode('x', $present);

    $largestSide = max([$length, $width, $height]);
    $minimalPerimeter = 2 * $length + 2 * $width + 2 * $height - 2 * $largestSide;
    $volume = $length * $width * $height;

    $ribbonLength += $minimalPerimeter + $volume;
}

echo $ribbonLength . PHP_EOL;