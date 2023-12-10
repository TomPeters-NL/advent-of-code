<?php

$input = file('./input/4.txt');

/**
 * @param string[] $input
 */
function findSecretNumber(array $input, string $targetPrefix): int
{
    $secretKey = $input[0];
    $secretNumber = 0;
    $solved = false;

    do {
        $compositeKey = $secretKey . $secretNumber;
        $md5Hash = md5($compositeKey);

        if (str_starts_with($md5Hash, $targetPrefix) === false) {
            $secretNumber++;
        } else {
            $solved = true;
        }
    } while ($solved === false);

    return $secretNumber;
}

###############
### Results ###
###############

$start = microtime(true);
$solutionOne = findSecretNumber($input, '00000');
$solutionTwo = findSecretNumber($input, '000000');
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;