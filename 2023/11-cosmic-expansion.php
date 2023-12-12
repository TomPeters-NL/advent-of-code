<?php

$input = file('./input/11.txt');

###############
### Classes ###
###############

class Cosmos
{
    /** @var Galaxy[] $galaxies */
    public array $galaxies;

    /** @var int[] $galaxies */
    public array $emptyRows;

    /** @var int[] $galaxies */
    public array $emptyColumns;

    /** @var int[] $galaxies */
    public array $galaxyColumns;

    public function addGalaxy(Galaxy $galaxy): void
    {
        $this->galaxies[] = $galaxy;
        $this->galaxyColumns[] = $galaxy->column;
    }
}

class Galaxy
{
    public int $row;
    public int $column;

    public function __construct(int $row, int $column)
    {
        $this->row = $row;
        $this->column = $column;
    }
}

###############
### Methods ###
###############

/**
 * @param string[] $input
 */
function analyzeCosmos(array $input, bool $ancientGalaxies = false): Cosmos
{
    $input = array_map('trim', $input);

    $cosmos = new Cosmos();
    foreach ($input as $row => $space) {
        // Find empty rows.
        $emptyRow = strlen($space) === substr_count($space, '.');
        if ($emptyRow === true) {
            $cosmos->emptyRows[] = $row;
            continue;
        }

        // Find galaxies.
        $quadrants = str_split($space);
        foreach ($quadrants as $column => $quadrant) {
            if ($quadrant === '#') {
                $cosmos->addGalaxy(new Galaxy($row, $column));
            }
        }
    }

    // Find empty columns.
    $columns = strlen($input[0]);
    for ($column = 0; $column < $columns; $column++) {
        $containsGalaxy = in_array($column, $cosmos->galaxyColumns);
        if ($containsGalaxy === false) {
            $cosmos->emptyColumns[] = $column;
        }
    }

    // Expand space.
    foreach ($cosmos->galaxies as $galaxy) {
        $galaxyRow = $galaxy->row;
        $rowExpansion = array_reduce($cosmos->emptyRows, function ($expansion, $emptyRow) use ($galaxyRow) {
            return $expansion += $emptyRow < $galaxyRow;
        });

        $galaxyColumn = $galaxy->column;
        $columnExpansion = array_reduce($cosmos->emptyColumns, function ($expansion, $emptyColumn) use ($galaxyColumn) {
            return $expansion += $emptyColumn < $galaxyColumn;
        });

        if ($ancientGalaxies === false) {
            $galaxy->row = $galaxyRow + $rowExpansion;
            $galaxy->column = $galaxyColumn + $columnExpansion;
        } else {
            $ancientGalaxyFactor = 1000000;

            $galaxy->row = match ($rowExpansion > 0) {
                false => $galaxyRow + $rowExpansion,
                true => $galaxyRow + ($ancientGalaxyFactor * $rowExpansion - $rowExpansion),
            };

            $galaxy->column = match ($columnExpansion > 0) {
                false => $galaxyColumn + $columnExpansion,
                true => $galaxyColumn + ($ancientGalaxyFactor * $columnExpansion - $columnExpansion),
            };
        }
    }

    return $cosmos;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $cosmos = analyzeCosmos($input);

    $pathSum = 0;
    $galaxyCount = count($cosmos->galaxies);

    foreach ($cosmos->galaxies as $index => $galaxy) {
        for ($i = $index + 1; $i < $galaxyCount; $i++) {
            $targetGalaxy = $cosmos->galaxies[$i];

            $shortestPath = abs($targetGalaxy->row - $galaxy->row) + abs($targetGalaxy->column - $galaxy->column);

            $pathSum += $shortestPath;
        }
    }

    return $pathSum;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $cosmos = analyzeCosmos($input, true);

    $pathSum = 0;
    $galaxyCount = count($cosmos->galaxies);

    foreach ($cosmos->galaxies as $index => $galaxy) {
        for ($i = $index + 1; $i < $galaxyCount; $i++) {
            $targetGalaxy = $cosmos->galaxies[$i];

            $shortestPath = abs($targetGalaxy->row - $galaxy->row) + abs($targetGalaxy->column - $galaxy->column);

            $pathSum += $shortestPath;
        }
    }

    return $pathSum;
}

###############
### Results ###
###############

$start = microtime(true);
$solutionOne = partOne($input);
$solutionTwo = partTwo($input);
$end = microtime(true);

echo '*-------------------------*' . PHP_EOL;
echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
echo PHP_EOL;
echo 'Completed in ' . number_format(($end - $start) * 1000, 2) . ' milliseconds!' . PHP_EOL;
echo '*-------------------------*' . PHP_EOL;