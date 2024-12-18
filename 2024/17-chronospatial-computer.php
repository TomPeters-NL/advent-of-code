<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

class ChronospatialComputer
{
    public int $registerA;
    public int $registerB;
    public int $registerC;
}

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day17
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/17', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    private function processProgram(): array
    {
        $computer = new ChronospatialComputer();
        $commands = '';

        foreach ($this->input as $line) {
            if ($line === '') {
                continue;
            }

            [$name, $value] = explode(': ', $line);

            match ($name) {
                'Register A' => $computer->registerA = (int) $value,
                'Register B' => $computer->registerB = (int) $value,
                'Register C' => $computer->registerC = (int) $value,
                'Program' => $commands = $value,
            };
        }

        return [$computer, $commands];
    }

    private function getComboOperand(int $operand, ChronospatialComputer $computer): int
    {
        return match ($operand) {
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => $computer->registerA,
            5 => $computer->registerB,
            6 => $computer->registerC,
            7 => 666,
        };
    }

    private function executeCommands(ChronospatialComputer $computer, string $commands): string
    {
        $commandList = array_chunk(array_map('intval', explode(',', $commands)), 2);
        $instructionPointer = 0;
        $output = '';

        while ($instructionPointer < count($commandList)) {
            $increasePointer = true;

            [$opcode, $operand] = $commandList[$instructionPointer];
            $comboOperand = match ($opcode) {
                0, 2, 5, 6, 7 => $this->getComboOperand($operand, $computer),
                default => null,
            };

            switch ($opcode) {
                case 0:
                    $computer->registerA = (int) ($computer->registerA / pow(2, $comboOperand));
                    break;
                case 1:
                    $computer->registerB = $computer->registerB ^ $operand;
                    break;
                case 2:
                    $computer->registerB = $comboOperand % 8;
                    break;
                case 3:
                    if ($computer->registerA !== 0) {
                        $instructionPointer = $operand;
                        $increasePointer = false;
                    }

                    break;
                case 4:
                    $computer->registerB = $computer->registerB ^ $computer->registerC;
                    break;
                case 5:
                    $output .= $output !== '' ? ',' . $comboOperand % 8 : $comboOperand % 8;
                    break;
                case 6:
                    $computer->registerB = (int) ($computer->registerA / pow(2, $comboOperand));
                    break;
                case 7:
                    $computer->registerC = (int) ($computer->registerA / pow(2, $comboOperand));
            }

            if ($increasePointer) $instructionPointer++;
        }

        return $output;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): string
    {
        [$computer, $commands] = $this->processProgram();

        return $this->executeCommands($computer, $commands);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        [$computer, $commands] = $this->processProgram();

        $registerA = 0;

        do {
            $computer->registerA = $registerA;

            $output = $this->executeCommands($computer, $commands);

            $registerA++;
        } while ($output !== $commands);

        return $registerA;
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

(new Day17())->printSolutions();