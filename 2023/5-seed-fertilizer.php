<?php

$input = file('./input/5.txt');

class Map
{
    public string $name;

    /**
     * @var Range[] $ranges
     */
    public array $ranges;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addRange(Range $range): void
    {
        $this->ranges[] = $range;
    }

    public function getDestination(int $value): int
    {
        foreach ($this->ranges as $range) {
            $destination = $range->getDestination($value);

            if ($destination !== $value) {
                break;
            }
        }

        return $destination;
    }
}

class Range
{
    public int $source;
    public int $destination;
    public int $rangeLength;

    public function __construct(int|string $source, int|string $destination, int|string $rangeLength)
    {
        $this->source = (int)$source;
        $this->destination = (int)$destination;
        $this->rangeLength = (int)$rangeLength;
    }

    public function getDestination(int $value): int
    {
        $destination = $value;
        if ($value >= $this->source && $value < $this->source + $this->rangeLength) {
            $destination = $this->destination + ($value - $this->source);
        }

        return $destination;
    }
}

/**
 * @param string[] $input
 *
 * @return int[]
 */
function getSeeds(array $input): array
{
    $seedLines = $input[0];

    preg_match_all('/\d+/', $seedLines, $seeds);
    $seeds = array_map('intval', $seeds[0]);

    return $seeds;
}

/**
 * @param string[] $input
 *
 * @return Map[]
 */
function getMaps(array $input): array
{
    $mapLines = array_slice($input, 2);

    $maps = [];
    $mapIndex = 0;
    foreach ($mapLines as $mapLine) {
        $line = trim($mapLine);

        $isSeparator = empty($line);
        $isNextMap = str_contains($line, 'map');

        if ($isSeparator === false && $isNextMap === false) {
            list($destination, $source, $rangeLength) = explode(' ', $line);
            $range = new Range($source, $destination, $rangeLength);

            $maps[$mapIndex]->addRange($range);
        } elseif ($isSeparator === false  && $isNextMap === true) {
            $mapIndex++;

            $mapName = explode(' ', $line)[0];
            $maps[$mapIndex] = new Map($mapName);
        }
    }

    return $maps;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $seeds = getSeeds($input);
    $maps = getMaps($input);

    $destinations = [];
    foreach ($seeds as $seed) {
        $latestSource = $seed;

        foreach ($maps as $map) {
            $destination = $map->getDestination($latestSource);

            $latestSource = $destination;
            $destinations[$map->name][] = $destination;
        }
    }

    return min($destinations['humidity-to-location']);
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    return 80085;
}

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*';