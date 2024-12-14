<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;
use DivisionByZeroError;
use TypeError;

class Prize
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}

class Button
{
    public function __construct(
        public string $type,
        public int $cost,
        public int $dX,
        public int $dY,
        public int $presses = 0,
    ) {
    }
}

class ClawMachine
{
    public function __construct(
        public Prize $prize,
        public Button $buttonA,
        public Button $buttonB,
    ) {
    }

    public function getCost(): int
    {
        return $this->buttonA->presses * $this->buttonA->cost + $this->buttonB->presses * $this->buttonB->cost;
    }
}

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day13
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/13', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    private function exploreTheArcade(): array
    {
        $clawMachines = [];
        $observations = array_map('array_filter', array_chunk($this->input, 4));

        foreach ($observations as [$buttonDetailsA, $buttonDetailsB, $prizeDetails]) {
            preg_match_all('/\d+/', $prizeDetails, $coordinatesPrize);
            $prize = new Prize((int) $coordinatesPrize[0][0], (int) $coordinatesPrize[0][1]);

            preg_match_all('/\d+/', $buttonDetailsA, $vectorsA);
            $buttonA = new Button('A', 3, (int) $vectorsA[0][0], (int) $vectorsA[0][1]);

            preg_match_all('/\d+/', $buttonDetailsB, $vectorsB);
            $buttonB = new Button('B', 1, (int) $vectorsB[0][0], (int) $vectorsB[0][1]);

            $clawMachines[] = new ClawMachine($prize, $buttonA, $buttonB);
        }

        return $clawMachines;
    }

    private function determineButtonPressesForPrize(ClawMachine $clawMachine): ?ClawMachine
    {
        $buttonAX = $clawMachine->buttonA->dX;
        $buttonAY = $clawMachine->buttonA->dY;
        $buttonBX = $clawMachine->buttonB->dX;
        $buttonBY = $clawMachine->buttonB->dY;
        $prizeX = $clawMachine->prize->x;
        $prizeY = $clawMachine->prize->y;

        try {
            $clawMachine->buttonA->presses = ($prizeX * $buttonBY - $prizeY * $buttonBX) / ($buttonAX * $buttonBY - $buttonAY * $buttonBX);
            $clawMachine->buttonB->presses = ($prizeX * $buttonAY - $prizeY * $buttonAX) / ($buttonBX * $buttonAY - $buttonBY * $buttonAX);
        } catch (DivisionByZeroError | TypeError) {
            return null;
        }

        return $clawMachine;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     *
     * @param ClawMachine[] $clawMachines A list of all claw machines in the arcade.
     */
    private function partOne(array $clawMachines): int
    {
        foreach ($clawMachines as &$clawMachine) {
            $clawMachine = $this->determineButtonPressesForPrize($clawMachine);
        }

        return array_reduce(array_filter($clawMachines), fn ($totalCost, $clawMachine) => $totalCost + $clawMachine->getCost());
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param ClawMachine[] $clawMachines A list of all claw machines in the arcade.
     */
    private function partTwo(array $clawMachines): int
    {
        array_walk($clawMachines, function ($clawMachine) {
            $clawMachine->prize->x += 10000000000000;
            $clawMachine->prize->y += 10000000000000;
        });

        foreach ($clawMachines as &$clawMachine) {
            $clawMachine = $this->determineButtonPressesForPrize($clawMachine);
        }

        return array_reduce(array_filter($clawMachines), fn ($totalCost, $clawMachine) => $totalCost + $clawMachine->getCost());
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $clawMachines = $this->exploreTheArcade();

        $this->adventHelper->printSolutions(
            $this->partOne($clawMachines),
            $this->partTwo($clawMachines),
        );
    }
}

(new Day13())->printSolutions();