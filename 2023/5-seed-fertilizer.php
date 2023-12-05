<?php

$input = file('./input/5.txt');
$input = [
    'seeds: 79 14 55 13' . PHP_EOL,
    PHP_EOL,
    'seed-to-soil map:' . PHP_EOL,
    '50 98 2' . PHP_EOL,
    '52 50 48' . PHP_EOL,
    PHP_EOL,
    'soil-to-fertilizer map:' . PHP_EOL,
    '0 15 37' . PHP_EOL,
    '37 52 2' . PHP_EOL,
    '39 0 15' . PHP_EOL,
    PHP_EOL,
    'fertilizer-to-water map:' . PHP_EOL,
    '49 53 8' . PHP_EOL,
    '0 11 42' . PHP_EOL,
    '42 0 7' . PHP_EOL,
    '57 7 4' . PHP_EOL,
    PHP_EOL,
    'water-to-light map:' . PHP_EOL,
    '88 18 7' . PHP_EOL,
    '18 25 70' . PHP_EOL,
    PHP_EOL,
    'light-to-temperature map:' . PHP_EOL,
    '45 77 23' . PHP_EOL,
    '81 45 19' . PHP_EOL,
    '68 64 13' . PHP_EOL,
    PHP_EOL,
    'temperature-to-humidity map:' . PHP_EOL,
    '0 69 1' . PHP_EOL,
    '1 0 69' . PHP_EOL,
    PHP_EOL,
    'humidity-to-location map:' . PHP_EOL,
    '60 56 37' . PHP_EOL,
    '56 93 4' . PHP_EOL,
];

/**
 * @param string[] $input
 *
 * @return int[][][]
 */
function buildMaps(array $input): array
{
    $mapInput = array_slice($input, 2);

    $index = 0;
    $mapStrings = [];
    foreach ($mapInput as $row) {
        $isSeparator = $row === PHP_EOL;
        $isMapHeader = str_ends_with($row, 'map:' . PHP_EOL);

        if ($isSeparator === true) {
            $index++;
        } elseif ($isMapHeader === false) {
            $mapStrings[$index][] = $row;
        }
    }

    foreach ($mapStrings as $mapIndex => $mapString) {
        foreach ($mapString as $string) {
            $mapData = explode(' ', $string);
            $destination = (int)$mapData[0];
            $source = (int)$mapData[1];
            $rangeLength = (int)$mapData[2];

            $maps[$mapIndex][$destination] = [
                'start' => $source,
                'end' => $source + $rangeLength - 1,
            ];
        }
    }

    return $maps;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $seedString = $input[0];
    $seeds = explode(' ', substr($seedString, 7, strlen($seedString) - 8));
    array_walk($seeds, 'intval');

    $maps = buildMaps($input);

    $locations = [];
    foreach ($seeds as $seed) {
        $number = $seed;

        foreach ($maps as $map) {
            foreach ($map as $destination => $range) {
                if ($number >= $range['start'] && $number <= $range['end']) {
                    $number = $destination + ($number - $range['start']);
                    break;
                }
            }
        }

        $locations[] = $number;
    }

    return min($locations);
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    preg_match_all('/\d+ \d+/', $input, $seedPairs);
    $seedRanges = [];
    foreach ($seedPairs as $seedPair) {
        $seedPair = explode(' ', $seedPair);
        $initialSeedNumber = (int)$seedPair[0];
        $rangeLength = (int)$seedPair[1];

        $seedRanges[] = [
            'start' => $initialSeedNumber,
            'end' => $initialSeedNumber  + $rangeLength - 1,
        ];
    }

    $maps = buildMaps($input);

    $locations = [];
    foreach ($seeds as $seed) {
        $number = $seed;

        foreach ($maps as $map) {
            foreach ($map as $destination => $range) {

            }
        }

        $locations[] = $number;
    }

    return min($locations);
}

$solutionOne = partOne($input);
$solutionTwo = partTwo($input);

echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;