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
        preg_match('/^(\D+) (\d+),(\d+) through (\d+),(\d+)$/', $instruction, $components);
        list($instruction, $command, $startX, $startY, $endX, $endY) = $components;

        $rangeX = range($startX, $endX);
        $rangeY = range($startY, $endY);

        switch ($command) {
            case 'turn on':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = 1;
                    }
                }

                break;
            case 'turn off':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = 0;
                    }
                }

                break;
            default:
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = !$grid[$x][$y];
                    }
                }
        }
    }

    $lit = 0;
    foreach ($grid as $x) {
        $lit += array_sum($x);
    }

    return $lit;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $grid = createGrid();

    foreach ($input as $instruction) {
        preg_match('/^(\D+) (\d+),(\d+) through (\d+),(\d+)$/', $instruction, $components);
        list($instruction, $command, $startX, $startY, $endX, $endY) = $components;

        $rangeX = range($startX, $endX);
        $rangeY = range($startY, $endY);

        switch ($command) {
            case 'turn on':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y]++;
                    }
                }

                break;
            case 'turn off':
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        if ($grid[$x][$y] > 0) {
                            $grid[$x][$y]--;
                        }
                    }
                }

                break;
            default:
                foreach ($rangeX as $x) {
                    foreach ($rangeY as $y) {
                        $grid[$x][$y] = $grid[$x][$y] + 2;
                    }
                }
        }
    }

    $totalBrightness = 0;
    foreach ($grid as $x) {
        $totalBrightness += array_sum($x);
    }

    return $totalBrightness;
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
