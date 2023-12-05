<?php

$input = file('./input/6.txt');

/**
 * @return string[]
 */
function createGrid(): array
{
    $gridX = range(0, 999);
    $gridY = array_fill(0, 1000, 0);

    return array_fill_keys($gridX, $gridY);
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $grid = createGrid();

    foreach ($input as $instruction) {
        preg_match('/^(\D+)(\d+),(\d+) through (\d+),(\d+)$/', $instruction, $components);
        $command = trim($components[1]);
        $startX = (int) $components[2];
        $startY = (int) $components[3];
        $endX = (int) $components[4];
        $endY = (int) $components[5];

        $rangeX = range($startX, $endX);
        $rangeY = range($startY, $endY);

        switch ($command) {
            case 'turn on':
                # code...
                break;
            case 'turn off':
                # code...
                break;
            default:
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] 
                    }
                }
        }

        return 1;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    return 2;
}

$solutionOne = partOne($input);
$solutionTwo = partTwo($input);

echo 'Part 1: ' . $solutionOne . PHP_EOL;
echo 'Part 2: ' . $solutionTwo . PHP_EOL;
