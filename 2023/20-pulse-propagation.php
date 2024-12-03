<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/20', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * General module specification.
 * Contains the generic properties and modules required by all derivatives.
 */
abstract class Module
{
    public string $name;

    /**
     * @var int[] $inputs A list of sources with their last-known output.
     */
    public array $inputs = [];

    /**
     * @var int[] $inputs A list of targets with their last-known input.
     */
    public array $outputs = [];

    abstract public function processPulse(string $source, int $input): array;
}

/**
 * A central Broadcaster module that sends initial low pulses to its outputs.
 */
class Broadcaster extends Module
{
    public function __construct(string $name, array $outputs)
    {
        $this->name = $name;
        $this->outputs = $outputs;
    }

    /**
     * Only able to send low pulses to its outputs.
     */
    public function processPulse(string $source, int $input): array
    {
        return $this->outputs;
    }
}

/**
 * A Flip-Flop module that turns on/off and outputs depending on the input and its state, respectively.
 */
class FlipFlop extends Module
{
    public bool $powered = false;

    public function __construct(string $name, array $outputs)
    {
        $this->name = $name;
        $this->outputs = $outputs;
    }

    /**
     * When receiving a low pulse:
     *      1. Switch the powered state.
     *      2. Send out a low pulse if unpowered or a high pulse if powered.
     */
    public function processPulse(string $source, int $input): array
    {
        $output = [];

        if ($input === 0) {
            $this->powered = !$this->powered;

            foreach ($this->outputs as $target => $outputData) {
                $this->outputs[$target]['pulse'] = (int) $this->powered;
            }

            $output = $this->outputs;
        }

        return $output;
    }
}

/**
 * A single- or multiple-input Conjunction module that outputs depending on the state of its inputs.
 */
class Conjunction extends Module
{
    public function __construct(string $name, array $outputs)
    {
        $this->name = $name;
        $this->outputs = $outputs;
    }

    /**
     * When receiving a pulse:
     *      1. Update the input source state.
     *      2. Send out a low pulse if all inputs are high pulses, a high pulse if not.
     */
    public function processPulse(string $source, int $input): array
    {
        $this->inputs[$source] = ['source' => $source, 'target' => $this->name, 'pulse' => $input];

        $allInputsHigh = count($this->inputs) === array_reduce($this->inputs, fn($x, $y) => $x + $y['pulse']);
        foreach ($this->outputs as $target => $outputData) {
            $this->outputs[$target]['pulse'] = (int) !$allInputsHigh;
        }

        return $this->outputs;
    }
}

/**
 * Generates a list of all modules in the puzzle input, alongside their types, names, outputs, and inputs.
 *
 * @param string[] $input The puzzle input.
 *
 * @return Module[] A list of all modules in the network.
 */
function configureNetwork(array $input): array
{
    $network = [];
    foreach ($input as $moduleSetup) {
        [$typeName, $outputList] = explode(' -> ', $moduleSetup);

        $type = substr($typeName, 0, 1);
        $name = $type !== 'b' ? substr($typeName, 1) : $typeName;

        $outputs = [];
        $outputKeys = explode(', ', $outputList);
        foreach ($outputKeys as $target) {
            $outputs[$target] = ['source' => $name, 'target' => $target, 'pulse' => 0];
        }

        $network[$name] = match ($type) {
            'b' => new Broadcaster($name, $outputs),
            '%' => new FlipFlop($name, $outputs),
            '&' => new Conjunction($name, $outputs),
        };
    }

    # Generate each module's inputs based on other module's outputs.
    foreach ($network as $module) {
        foreach ($module->outputs as ['source' => $source, 'target' => $target, 'pulse' => $pulse]) {
            if (array_key_exists($target, $network) === true) {
                $network[$target]->inputs[$source] = ['source' => $source, 'target' => $target, 'pulse' => 0];
            }
        }
    }

    return $network;
}

/**
 * Make an exact copy of a network, without the exact references.
 *
 * @param Module[] $network The original network to be cloned.
 *
 * @return Module[] The cloned network, which can be manipulated without affecting the original.
 */
function cloneNetwork(array $network): array
{
    $cloneNetwork = [];

    foreach ($network as $index => $module) {
        $cloneNetwork[$index] = clone $module;
    }

    return $cloneNetwork;
}

/**
 * Broadcast across the network once.
 *
 * @param Module[] $network A list of modules in the broadcast network.
 *
 * @return array The state of the network after the broadcast and a log of all pulses sent over the network during the broadcast.
 */
function broadcast(array $network): array
{
    $broadcastNetwork = cloneNetwork($network);
    $broadcastLog = [];
    $broadcastQueue = [];

    # Add the initial broadcast signal to the log and queue.
    $broadcastLog = array_merge($broadcastLog, array_values($broadcastNetwork['broadcaster']->outputs));
    $broadcastQueue = array_merge($broadcastQueue, array_values($broadcastNetwork['broadcaster']->outputs));

    while (empty($broadcastQueue) === false) {
        # Retrieve the next broadcast from the queue.
        ['source' => $source, 'target' => $target, 'pulse' => $pulse] = array_shift($broadcastQueue);

        if (array_key_exists($target, $broadcastNetwork) === true) {
            # Allow the broadcast target to process the incoming pulse.
            $newOutputs = $broadcastNetwork[$target]->processPulse($source, $pulse);

            # Add the new output broadcasts to the end of the log and queue.
            $broadcastLog = array_merge($broadcastLog, array_values($newOutputs));
            $broadcastQueue = array_merge($broadcastQueue, array_values($newOutputs));
        }
    }

    return [$broadcastNetwork, $broadcastLog];
}

/**
 * @param Module[] $networkAlpha The first pulse broadcast network.
 * @param Module[] $networkBeta  The second pulse broadcast network.
 *
 * @return bool Indicates whether the networks are identical (for the purposes of pattern analysis).
 */
function compareNetworks(array $networkAlpha, array $networkBeta): bool
{
    $sameState = true;

    foreach ($networkAlpha as $index => $module) {
        $isDifferentFlipFlop = $module instanceof FlipFlop && $module != $networkBeta[$index];
        $isDifferentConjunction = $module instanceof Conjunction && $module->inputs != $networkBeta[$index]->inputs;

        if ($isDifferentFlipFlop === true || $isDifferentConjunction === true) {
            $sameState = false;
            break;
        }
    }

    return $sameState;
}

/**
 * Calculate the amount of high and low pulses in the specified pattern range.
 *
 * @param array[] $broadcastLogs   The logged pulses per broadcast for the entire broadcast pattern.
 * @param int     $patternProgress The progress into the pattern.
 *
 * @return int[] The amount of high and low pulses in the specified broadcast pattern range.
 */
function calculatePatternPulses(array $broadcastLogs, int $patternProgress): array
{
    # Reduce the dataset to the target amount of broadcasts.
    $broadcasts = array_slice($broadcastLogs, 0, $patternProgress);

    # Calculate the high pulses by taking the sum of all broadcast pulses.
    # This is possible due to low and high pulses being designated as 0 and 1.
    $high = array_reduce($broadcasts, function ($highPulses, $broadcast) {
        return $highPulses + array_reduce($broadcast, fn($sum, $output) => $sum + $output['pulse']);
    }, 0);

    # Calculate the low pulses by counting the total amount of pulses broadcast and subtracting the amount of high pulses.
    # The broadcast amount is added to compensate for the first signal by the broadcaster.
    $broadcastAmount = count($broadcasts);
    $low = array_reduce($broadcasts, fn($pulses, $broadcast) => $pulses + count($broadcast)) + $broadcastAmount - $high;

    return [$high, $low];
}

/**
 * Provides a list of nested dependencies for the right conditions to trigger a low pulse to RX.
 *
 * @param Module[] $network A list of modules in the broadcast network.
 *
 * @return int[] A list of dependencies.
 */
function gatherDependencies(array $network): array
{
    $dependencies = [];

    # Module RS should receive all high pulses (1s).
    foreach (array_keys($network['rs']->inputs) as $primaryInput) {
        # To provide high pulses, these inputs should themselves receive a low pulse.
        foreach (array_keys($network[$primaryInput]->inputs) as $secondaryInput) {
            $dependencies[$secondaryInput] = 0;
        }
    }

    return $dependencies;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    # The button presses for this part.
    $buttonPresses = 1000;

    # Assemble the initial network.
    $network = configureNetwork($input);

    # Find the broadcast pattern.
    $patternDetected = false;
    $patternLength = 0;

    $broadcastLogs = [];
    $nextNetwork = $network;

    while ($patternDetected === false) {
        [$postBroadcastNetwork, $broadcastLog] = broadcast($nextNetwork);
        $patternLength++;

        # Assert whether the post-broadcast network and the initial network are the same.
        $patternDetected = compareNetworks($network, $postBroadcastNetwork);

        # Assign the next network state and log the broadcast output.
        $nextNetwork = $postBroadcastNetwork;
        $broadcastLogs[] = $broadcastLog;

        # Have an early exit if the pattern is longer than the amount of button presses.
        if ($patternLength === $buttonPresses) {
            break;
        }
    }

    # Determine how often the pattern fits in the required 1000 button presses.
    $patternRepetitions = floor($buttonPresses / $patternLength);
    $patternRemainder = $buttonPresses % $patternLength;

    # Calculate the amount of low and high pulses in the pattern.
    [$repetitionHigh, $repetitionLow] = calculatePatternPulses($broadcastLogs, $patternLength);
    [$remainderHigh, $remainderLow] = calculatePatternPulses($broadcastLogs, $patternRemainder);

    return ($patternRepetitions * $repetitionHigh + $remainderHigh) * ($patternRepetitions * $repetitionLow + $remainderLow);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    # Assemble the initial network.
    $network = configureNetwork($input);

    # Find the broadcast pattern.
    $allDependenciesFound = false;
    $dependencies = gatherDependencies($network);
    $dependencyNames = array_keys($dependencies);
    $buttonPresses = 0;


    # The RX module receives a single low pulse when the RS module receives high pulses from all its inputs.
    $nextNetwork = $network;
    while ($allDependenciesFound === false) {
        [$postBroadcastNetwork, $broadcastLog] = broadcast($nextNetwork);
        $buttonPresses++;

        foreach ($broadcastLog as ['source' => $source, 'target' => $target, 'pulse' => $pulse]) {
            if (in_array($source, $dependencyNames) === true && $dependencies[$source] === 0 && $pulse === 0) {
                $dependencies[$source] = $buttonPresses;
            }
        }

        # Assign the next network state.
        $nextNetwork = $postBroadcastNetwork;

        # Determine whether all the necessary modules have been logged.
        $allDependenciesFound = in_array(0, $dependencies) === false;
    }

    # Calculate the first button press at which the RX module receives a low pulse.
    return (int) array_product($dependencies);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));