<?php

$input = file('./input/4.txt');

$secretKey = $input[0];
$secretNumber = 0;
$solved = false;

do {
    $compositeKey = $secretKey . $secretNumber;
    $md5Hash = md5($compositeKey);

    if (str_starts_with($md5Hash, '000000') === false) {
        $secretNumber++;
    } else {
        $solved = true;
    }
} while ($solved === false);

echo $secretNumber . PHP_EOL;