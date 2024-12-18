<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** The robot that manages the lanternfish warehouse's inventory. */
class WarehouseRobot
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}

/** The lanternfish warehouse. */
class Warehouse
{
    /** @var string[] */
    public array $walls;

    /** @var string[] */
    public array $emptySpaces;

    /** @var string[] */
    public array $boxes;

    /** @var string[][] */
    public array $wideBoxes;

    public WarehouseRobot $robot;

    /**
     * Checks whether a wall is located at the provided coordinates.
     *
     * @param int $x The horizontal position of the space.
     * @param int $y The vertical position of the space.
     *
     * @return bool
     */
    public function isWall(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->walls);
    }

    /**
     * Checks whether an empty space is located at the provided coordinates.
     *
     * @param int $x The horizontal position of the space.
     * @param int $y The vertical position of the space.
     *
     * @return bool
     */
    public function isEmptySpace(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->emptySpaces);
    }

    /**
     * Checks whether a box is located at the provided coordinates.
     *
     * @param int $x The horizontal position of the space.
     * @param int $y The vertical position of the space.
     *
     * @return bool
     */
    public function isBox(int $x, int $y): bool
    {
        return in_array($x . ',' . $y, $this->boxes);
    }

    /**
     * Checks whether a wide box is located at the provided coordinates.
     *
     * @param int $x The horizontal position of the space.
     * @param int $y The vertical position of the space.
     *
     * @return bool
     */
    public function isWideBox(int $x, int $y): bool
    {
        $coordinates = $x . ',' . $y;

        foreach ($this->wideBoxes as [$leftSide, $rightSide]) {
            if ($leftSide === $coordinates || $rightSide === $coordinates) {
                return true;
            }
        }

        return false;
    }


    /**
     * Adds an empty space to the warehouse.
     *
     * @param int $x The horizontal position of the empty space.
     * @param int $y The vertical position of the empty space.
     *
     * @return void
     */
    public function addEmptySpace(int $x, int $y): void
    {
        $this->emptySpaces[] = $x . ',' . $y;
    }

    /**
     * Removes an empty space from the warehouse.
     *
     * @param int $x The horizontal position of the empty space.
     * @param int $y The vertical position of the empty space.
     *
     * @return void
     */
    public function removeEmptySpace(int $x, int $y): void
    {
        $index = array_search($x . ',' . $y, $this->emptySpaces);
        unset($this->emptySpaces[$index]);
    }

    /**
     * Adds a box to the warehouse.
     *
     * @param int $x The horizontal position of the box.
     * @param int $y The vertical position of the box.
     *
     * @return void
     */
    public function addBox(int $x, int $y): void
    {
        $this->boxes[] = $x . ',' . $y;
    }

    /**
     * Removes a box from the warehouse.
     *
     * @param int $x The horizontal position of the box.
     * @param int $y The vertical position of the box.
     *
     * @return void
     */
    public function removeBox(int $x, int $y): void
    {
        $index = array_search($x . ',' . $y, $this->boxes);
        unset($this->boxes[$index]);
    }

    /**
     * Adds a wide box to the warehouse.
     *
     * @param int $lX The horizontal position of the left side of the box.
     * @param int $y  The vertical position of the box.
     *
     * @return void
     */
    public function addWideBox(int $lX, int $y): void
    {
        $this->wideBoxes[] = [
            $lX . ',' . $y,
            $lX + 1 . ',' . $y,
        ];
    }

    /**
     * Retrieves the position of a wide box from the warehouse.
     *
     * @param int $x The horizontal position of either side of the box.
     * @param int $y The vertical position of the box.
     *
     * @return string[] The coordinates of the left and right side of the box.
     */
    public function getWideBox(int $x, int $y): array
    {
        $coordinates = $x . ',' . $y;

        foreach ($this->wideBoxes as [$leftSide, $rightSide]) {
            if ($leftSide === $coordinates || $rightSide === $coordinates) {
                return [$leftSide, $rightSide];
            }
        }
    }

    /**
     * Removes a wide box from the warehouse.
     *
     * @param int $x The horizontal position of either side of the box.
     * @param int $y The vertical position of the box.
     *
     * @return void
     */
    public function removeWideBox(int $x, int $y): void
    {
        $coordinates = $x . ',' . $y;

        foreach ($this->wideBoxes as $index => [$leftSide, $rightSide]) {
            if ($leftSide === $coordinates || $rightSide === $coordinates) {
                unset($this->wideBoxes[$index]);
            }
        }
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

    /**
     * Populates the lanternfish warehouse with the positions of all walls, boxes, empty spaces, and its robot.
     *
     * @param bool $isWideWarehouse Whether the boxes are located in the wide variant of the warehouse
     *
     * @return Warehouse The lanternfish warehouse and its contents.
     */
    private function mapWarehouse(bool $isWideWarehouse = false): Warehouse
    {
        $separator = array_search('', $this->input);
        $map = array_slice($this->input, 0, $separator);

        $warehouse = new Warehouse();

        foreach ($map as $y => $row) {
            if ($isWideWarehouse) {
                $row = str_replace(['#', 'O', '.', '@'], ['##', '[]', '..', '@.'], $row);
            }

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
                    case '[':
                        $warehouse->addWideBox($x, $y);
                        break;
                    case '@':
                        $warehouse->robot = new WarehouseRobot($x, $y);
                }
            }
        }

        return $warehouse;
    }

    /**
     * Parses the input instructions and converts them to vectors.
     *
     * @return int[][] A list of horizontal and vertical vectors.
     */
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

    /**
     * A recursive method to push a regular box, and its potential neighbors, in a specified direction.
     *
     * @param Warehouse $warehouse The lanternfish warehouse and its contents.
     * @param int       $x         The horizontal position of the box.
     * @param int       $y         The vertical position of the box.
     * @param int       $dX        The horizontal vector along which the box should be pushed.
     * @param int       $dY        The vertical vector along which the box should be pushed.
     *
     * @return bool Indicates whether the box was successfully pushed.
     */
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

    /**
     * A recursive method to push a wide box, and its potential neighbors, in a specified direction.
     *
     * @param Warehouse $warehouse The lanternfish warehouse and its contents.
     * @param int       $lX        The horizontal position of the left side of the box.
     * @param int       $y         The vertical position of the box.
     * @param int       $dX        The horizontal vector along which the box should be pushed.
     * @param int       $dY        The vertical vector along which the box should be pushed.
     *
     * @return bool Indicates whether the box was successfully pushed.
     */
    private function pushWideBox(Warehouse $warehouse, int $lX, int $y, int $dX, int $dY): bool
    {
        $rX = $lX + 1;

        $nlX = $lX + $dX;
        $nrX = $rX + $dX;
        $nY = $y + $dY;

        if ($warehouse->isWall($nlX, $nY) || $warehouse->isWall($nrX, $nY)) {
            return false;
        }

        if ($warehouse->isWideBox($nlX, $nY) && $nlX !== $rX) {
            [$leftSide, $rightSide] = $warehouse->getWideBox($nlX, $nY);
            [$bX, $bY] = array_map('intval', explode(',', $leftSide));

            $success = $this->pushWideBox($warehouse, $bX, $bY, $dX, $dY);

            if ($success === false) {
                return false;
            }
        }

        if ($warehouse->isWideBox($nrX, $nY) && $nrX !== $lX) {
            [$leftSide, $rightSide] = $warehouse->getWideBox($nrX, $nY);
            [$bX, $bY] = array_map('intval', explode(',', $leftSide));

            $success = $this->pushWideBox($warehouse, $bX, $bY, $dX, $dY);

            if ($success === false) {
                return false;
            }
        }

        # Move left.
        if ($dX === -1 && $warehouse->isEmptySpace($nlX, $nY)) {
            $warehouse->removeEmptySpace($nlX, $nY);
            $warehouse->removeWideBox($lX, $y);
            $warehouse->addWideBox($nlX, $nY);
            $warehouse->addEmptySpace($rX, $y);

            return true;
        }

        # Move right.
        if ($dX === 1 && $warehouse->isEmptySpace($nrX, $nY)) {
            $warehouse->removeEmptySpace($nrX, $nY);
            $warehouse->removeWideBox($lX, $y);
            $warehouse->addWideBox($nlX, $nY);
            $warehouse->addEmptySpace($lX, $y);

            return true;
        }

        # Move up or down.
        if ($dY !== 0 && $warehouse->isEmptySpace($nlX, $nY) && $warehouse->isEmptySpace($nrX, $nY)) {
            $warehouse->removeEmptySpace($nlX, $nY);
            $warehouse->removeEmptySpace($nrX, $nY);
            $warehouse->addWideBox($nlX, $nY);

            $warehouse->removeWideBox($lX, $y);
            $warehouse->addEmptySpace($lX, $y);
            $warehouse->addEmptySpace($rX, $y);

            return true;
        }

        return false;
    }

    /**
     * Follow the list of instructions to determine where all the boxes end up.
     *
     * @param int[][]   $instructions    A list of directional vectors telling the warehouse robot where to go.
     * @param Warehouse $warehouse       The lanternfish warehouse and its contents.
     * @param bool      $isWideWarehouse Whether the boxes are located in the wide variant of the warehouse.
     *
     * @return void All positions are stored in the warehouse object, which is passed by reference.
     */
    private function followInstructions(array $instructions, Warehouse &$warehouse, bool $isWideWarehouse = false): void
    {
        foreach ($instructions as [$dX, $dY]) {
            $x = $warehouse->robot->x;
            $y = $warehouse->robot->y;

            $nX = $x + $dX;
            $nY = $y + $dY;

            if ($warehouse->isWall($nX, $nY)) {
                continue;
            }

            if (!$isWideWarehouse && $warehouse->isBox($nX, $nY)) {
                $success = $this->pushBox($warehouse, $nX, $nY, $dX, $dY);

                if ($success === false) {
                    continue;
                }
            }
            if ($isWideWarehouse && $warehouse->isWideBox($nX, $nY)) {
                $warehouseCopy = clone $warehouse;

                [$leftSide, $rightSide] = $warehouse->getWideBox($nX, $nY);
                [$bX, $bY] = array_map('intval', explode(',', $leftSide));

                $success = $this->pushWideBox($warehouseCopy, $bX, $bY, $dX, $dY);

                if ($success === false) {
                    continue;
                }

                $warehouse = $warehouseCopy;
            }

            $warehouse->removeEmptySpace($nX, $nY);
            $warehouse->robot->x = $nX;
            $warehouse->robot->y = $nY;
            $warehouse->addEmptySpace($x, $y);
        }
    }

    /**
     * Calculates the sum of all boxes' GPS coordinates in the provided warehouse.
     *
     * @param Warehouse $warehouse       The lanternfish warehouse and its contents.
     * @param bool      $isWideWarehouse Whether the boxes are located in the wide variant of the warehouse.
     *
     * @return int The sum of all box GPS coordinates.
     */
    private function getGpsSum(Warehouse $warehouse, bool $isWideWarehouse = false): int
    {
        return array_reduce(
            $isWideWarehouse ? $warehouse->wideBoxes : $warehouse->boxes,
            function (int $sum, string | array $box) use ($isWideWarehouse) {
                $coordinates = $isWideWarehouse ? $box[0] : $box;
                [$x, $y] = explode(',', $coordinates);

                return $sum + (100 * (int) $y + (int) $x);
            },
            0,
        );
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

        return $this->getGpsSum($warehouse);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        $warehouse = $this->mapWarehouse(true);
        $instructions = $this->readInstructions();

        $this->followInstructions($instructions, $warehouse, true);

        return $this->getGpsSum($warehouse, true);
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