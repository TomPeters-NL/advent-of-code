<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A 3-bit spacetime computer, whatever that may be. */
class ChronospatialComputer
{
    public function __construct(
        public int $registerA = 0,
        public int $registerB = 0,
        public int $registerC = 0,
    ) {
    }

    /**
     * Finds the value of the combo operand based on a literal operand.
     *
     * @param int $operand A literal operand.
     *
     * @return int The value of the combo operand.
     */
    public function getComboOperand(int $operand): int
    {
        return match ($operand) {
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => $this->registerA,
            5 => $this->registerB,
            6 => $this->registerC,
        };
    }
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

    /**
     * Processes the initial register values and the program instructions.
     *
     * @return array<ChronospatialComputer, int[]>
     */
    private function processProgram(): array
    {
        $computer = new ChronospatialComputer();
        $program = '';

        foreach ($this->input as $line) {
            if ($line === '') {
                continue;
            }

            [$name, $value] = explode(': ', $line);

            match ($name) {
                'Register A' => $computer->registerA = (int) $value,
                'Register B' => $computer->registerB = (int) $value,
                'Register C' => $computer->registerC = (int) $value,
                'Program' => $program = $value,
            };
        }

        return [
            $computer,
            array_map('intval', explode(',', $program)),
        ];
    }

    /**
     * Executes the provided program's instructions on the chronospatial computer.
     *
     * @param ChronospatialComputer $computer The computer tracking registers A, B, and C.
     * @param int[]                 $program The program's instructions, consisting of opcodes and operands.
     *
     * @return int[] The output value of the program.
     */
    private function executeProgram(ChronospatialComputer $computer, array $program): array
    {
        $output = [];

        $commandList = array_chunk($program, 2);

        $commandCount = count($commandList);
        $instructionPointer = 0;

        while ($instructionPointer < $commandCount) {
            [$opcode, $operand] = $commandList[$instructionPointer];

            switch ($opcode) {
                case 0:
                    $computer->registerA = $computer->registerA >> $computer->getComboOperand($operand);
                    break;
                case 1:
                    $computer->registerB = $computer->registerB ^ $operand;
                    break;
                case 2:
                    $computer->registerB = $computer->getComboOperand($operand) % 8;
                    break;
                case 3:
                    $instructionPointer = $computer->registerA === 0 ? $instructionPointer : $operand - 1;
                    break;
                case 4:
                    $computer->registerB = $computer->registerB ^ $computer->registerC;
                    break;
                case 5:
                    $output[] = $computer->getComboOperand($operand) % 8;
                    break;
                case 6:
                    $computer->registerB = $computer->registerA >> $computer->getComboOperand($operand);
                    break;
                case 7:
                    $computer->registerC = $computer->registerA >> $computer->getComboOperand($operand);
            }

            $instructionPointer++;
        }

        return $output;
    }

    /**
     * Finds the lowest positive value for register A that causes the program to self-replicate.
     *
     * @param int[] $program The program's instructions, consisting of opcodes and operands.
     * @param int   $a The current value of register A.
     *
     * @return int|false Returns a potential value of register A or false if no valid value can be found.
     */
    private function findSelfReplicatingRegister(array $program, int $a = 0): int | false
    {
        if (empty($program)) {
            return $a;
        }

        $target = $program[array_key_last($program)];

        foreach (range(0, 7) as $index) {
            $nA = ($a << 3) + $index;

            $b = $nA % 8;
            $b = $b ^ 2;
            $c = $nA >> $b;
            $b = $b ^ 3;
            $b = $b ^ $c;

            if ($b % 8 !== $target) {
                continue;
            }

            $remainingProgram = array_slice($program, 0, -1);
            $replicationNumber = $this->findSelfReplicatingRegister($remainingProgram, $nA);

            if ($replicationNumber !== false) {
                return $replicationNumber;
            }
        }

        return false;
    }

    /**
     * An alternative way to find the lowest positive value for register A that causes the program to self-replicate.
     *
     * @param int[] $program The program's instructions, consisting of opcodes and operands.
     * @param int   $a The current value of register A.
     * @param int   $n The current length of the target output.
     *
     * @return int|false Returns a potential value of register A or false if no valid value can be found.
     */
    private function findSelfReplicatingRegisterAlternative(array $program, int $a = 0, int $n = 1): int | false
    {
        if ($n > count($program)) {
            return $a;
        }

        $target = array_slice($program, -$n);

        foreach (range(0, 7) as $index) {
            $nA = ($a << 3) + $index;

            $output = $this->executeProgram(new ChronospatialComputer($nA), $program);

            if ($output === $target) {
                $result = $this->findSelfReplicatingRegisterAlternative($program, $nA, $n + 1);

                if ($result !== false) {
                    return $result;
                }
            }
        }

        return false;
    }

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     */
    private function partOne(): string
    {
        [$computer, $program] = $this->processProgram();

        return implode(',', $this->executeProgram($computer, $program));
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     */
    private function partTwo(): int
    {
        [$computer, $program] = $this->processProgram();

        return $this->findSelfReplicatingRegister($program);
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