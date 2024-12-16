<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class WarehouseRobot
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}

class Warehouse
{
    /** @var string[] */
    public array $walls;

    /** @var string[] */
    public array $emptySpaces;

    /** @var string[] */
    public array $boxes;

    public WarehouseRobot $robot;

    public function isWall(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->walls);
    }

    public function isEmptySpace(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->emptySpaces);
    }

    public function isBox(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->boxes);
    }

    public function addEmptySpace(int $x, int $y): void
    {
        $this->emptySpaces[] = $x . ',' . $y;
        sort($this->emptySpaces);
    }

    public function removeEmptySpace(int $x, int $y): void
    {
        $index = array_search($x . ',' . $y, $this->emptySpaces);
        unset($this->emptySpaces[$index]);
    }

    public function addBox(int $x, int $y): void
    {
        $this->boxes[] = $x . ',' . $y;
        sort($this->boxes);
    }

    public function removeBox(int $x, int $y): void
    {
        $index = array_search($x . ',' . $y, $this->boxes);
        unset($this->boxes[$index]);
    }
}

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day15
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/15', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    private function mapWarehouse(): Warehouse
    {
        $separator = array_search('', $this->input);
        $map = array_slice($this->input, 0, $separator);

        $warehouse = new Warehouse();

        foreach ($map as $y => $row) {
            foreach (str_split($row) as $x => $space) {
                switch ($space) {
                    case '#':
                        $warehouse->walls[] = $x . ',' . $y;
                        break;
                    case '.':
                        $warehouse->addEmptySpace($x, $y);
                        break;
                    case 'O':
                        $warehouse->addBox($x, $y);
                        break;
                    case '@':
                        $warehouse->robot = new WarehouseRobot($x, $y);
                }
            }
        }

        return $warehouse;
    }

    private function readInstructions(): array
    {
        $separator = array_search('', $this->input);
        $baseInstructions = str_split(implode('', array_slice($this->input, $separator)));

        $instructions = [];

        foreach ($baseInstructions as $direction) {
            [$dX, $dY] = match ($direction) {
                '^' => [0, -1],
                '>' => [1, 0],
                'v' => [0, 1],
                '<' => [-1, 0],
            };

            $instructions[] = [$dX, $dY];
        }

        return $instructions;
    }

    private function pushBox(Warehouse $warehouse, int $x, int $y, int $dX, int $dY): bool
    {
        $nX = $x + $dX;
        $nY = $y + $dY;

        if ($warehouse->isWall($nX, $nY)) {
            return false;
        }

        if ($warehouse->isBox($nX, $nY)) {
            $success = $this->pushBox($warehouse, $nX, $nY, $dX, $dY);

            if ($success === false) {
                return false;
            }
        }

        if ($warehouse->isEmptySpace($nX, $nY)) {
            $warehouse->removeEmptySpace($nX, $nY);
            $warehouse->addBox($nX, $nY);

            $warehouse->removeBox($x, $y);
            $warehouse->addEmptySpace($x, $y);

            return true;
        }

        return false;
    }

    private function followInstructions(array $instructions, Warehouse $warehouse): void
    {
        foreach ($instructions as [$dX, $dY]) {
            $x = $warehouse->robot->x;
            $y = $warehouse->robot->y;

            $nX = $x + $dX;
            $nY = $y + $dY;

            if ($warehouse->isWall($nX, $nY)) {
                continue;
            }

            if ($warehouse->isBox($nX, $nY)) {
                $success = $this->pushBox($warehouse, $nX, $nY, $dX, $dY);

                if ($success === false) {
                    continue;
                }
            }

            $warehouse->removeEmptySpace($nX, $nY);
            $warehouse->robot->x = $nX;
            $warehouse->robot->y = $nY;
            $warehouse->addEmptySpace($x, $y);
        }
    }

    private function getGpsCoordinates(string $coordinates): int
    {
        [$x, $y] = explode(',', $coordinates);

        return 100 * (int) $y + (int) $x;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): int
    {
        $warehouse = $this->mapWarehouse();
        $instructions = $this->readInstructions();

        $this->followInstructions($instructions, $warehouse);

        return array_reduce($warehouse->boxes, fn($total, $coordinates) => $total + $this->getGpsCoordinates($coordinates));
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        return 2;
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $this->adventHelper->printSolutions(
            $this->partOne(),
            $this->partTwo(),
        );
    }
}

(new Day15())->printSolutions();