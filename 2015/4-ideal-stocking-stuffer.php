<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/4');

#################
### Solutions ###
#################

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

$adventHelper->printSolutions(partOne($input), partTwo($input));