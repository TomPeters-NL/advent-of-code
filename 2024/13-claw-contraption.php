<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;
use DivisionByZeroError;
use TypeError;

/** The prize in a claw machine. */
class Prize
{
    /**
     * @param int $x The horizontal (X) coordinate of the prize.
     * @param int $y The vertical (Y) coordinate of the prize.
     */
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}

/** One of the buttons used to manipulate a claw machine's claw. */
class Button
{
    /**
     * @param string $type The type of button, "A" or "B".
     * @param int    $cost The cost associated with pressing the button.
     * @param int    $dX The amount of horizontal (X) movement of the claw when the button is pressed.
     * @param int    $dY The amount of vertical (Y) movement of the claw when the button is pressed.
     * @param int    $presses The amount of times the button was pressed.
     */
    public function __construct(
        public string $type,
        public int $cost,
        public int $dX,
        public int $dY,
        public int $presses = 0,
    ) {
    }
}

/** One of the claw machines in the arcade. */
class ClawMachine
{
    /**
     * @param Prize  $prize The claw machine's prize.
     * @param Button $buttonA The first, most expensive, button on the claw machine.
     * @param Button $buttonB The second, cheaper, button on the claw machine.
     */
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

    /**
     * Walk through the arcade and take stock of all claw machines present.
     *
     * @return ClawMachine[] A list of all claw machines in the arcade.
     */
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

    /**
     * Play the claw machine and count the required button presses to win the prize.
     *
     * @param ClawMachine $clawMachine The claw machine being played.
     *
     * @return ClawMachine|null The claw machine with all button presses counted, or nothing if winning the prize is impossible.
     */
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
            # Return null if attempting to divide by zero or if the required amount of button presses is not an integer.
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