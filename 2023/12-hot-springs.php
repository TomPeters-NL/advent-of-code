<?php

$input = file('./input/12-test.txt');

/**
 * @param string $record
 * @param int[] $groupSizes
 * @return array<int[], string>
 */
function unfold(string $record, array $groupSizes): array
{
    $record .= '?';
    $unfoldedRecord = str_repeat($record, 5);
    $unfoldedRecord = substr($unfoldedRecord, 0, strlen($unfoldedRecord) - 1);

    $groupSizeString = implode('', $groupSizes);
    $unfoldedGroupSizes = str_repeat($groupSizeString, 5);
    $unfoldedGroupSizes = array_map('intval', str_split($unfoldedGroupSizes));

    return [$unfoldedRecord, $unfoldedGroupSizes];
}

/**
 * @param int[] $groupSizes
 */
function validateCompleteRecord(string $record, array $groupSizes): bool
{
    preg_match_all('/#+/', $record, $matches);
    $groups = $matches[0];

    $isValidGroupAmount = count($groups) === count($groupSizes);

    if ($isValidGroupAmount === true) {
        $hasValidGroupSizes = false;

        foreach ($groups as $index => $group) {
            $hasValidGroupSizes = strlen($group) === $groupSizes[$index];

            if ($hasValidGroupSizes === false) {
                break;
            }
        }
    }

    return $isValidGroupAmount && $hasValidGroupSizes;
}

/**
 * @param int[] $groupSizes
 */
function validatePartialRecord(string $record, array $groupSizes): bool
{
    preg_match_all('/#+/', $record, $matches);
    $groups = $matches[0];

    $isValidGroupAmount = count($groups) <= count($groupSizes);
    var_dump(count($groups) . ' vs ' . count($groupSizes));

    if ($isValidGroupAmount === true) {
        $hasValidGroupSizes = false;

        foreach ($groups as $group) {
            $hasValidGroupSizes = strlen($group) <= max($groupSizes);

            if ($hasValidGroupSizes === false) {
                break;
            }
        }
    }

    return $isValidGroupAmount && $hasValidGroupSizes;
}

/**
 * @param string[] $cache
 */
function addToCache(array &$cache, string $record, int $index): void
{
    $record[$index] = 'x';

    if (array_key_exists($record, $cache) === false) {
        $cache[$record] = $record;
    }
}

/**
 * @param string[] $cache
 */
function validateCache(array &$cache, string $record, int $index): bool
{
    $record[$index] = 'x';

    return array_key_exists($record, $cache);
}

/**
 * @param string[] $arrangements
 * @param int[] $groupSizes
 * @param string[] $cache
 */
function iterateRecord(array &$arrangements, string $record, array $groupSizes, array &$cache, int $index = 0): void
{
    $isInCache = validateCache($cache, $record, $index);
    $isDamagedRecord = str_contains($record . $index, '?');

    if ($isInCache === false && $isDamagedRecord === true) {
        // Cache the current arrangement.
        addToCache($cache, $record, $index);

        // Create new arrangements.
        $iteration1 = $iteration2 = $record;
        $target = $record[$index];

        if ($target === '?') {
            $iteration1[$index] = '.';
            $iteration2[$index] = '#';
        }

        // Check validity of new arrangements.
//        $validIteration1 = validatePartialRecord($record, $groupSizes);
//        $validIteration2 = validatePartialRecord($record, $groupSizes);

        // Continue iteration process.
        $index++;

//        if ($validIteration1 === true) {
            iterateRecord($arrangements, $iteration1, $groupSizes, $cache, $index);
//        }

//        if ($validIteration2 === true) {
            iterateRecord($arrangements, $iteration2, $groupSizes, $cache, $index);
//        }
    }

    if ($isInCache === false && $isDamagedRecord === false) {
        // Cache the current arrangement.
        addToCache($cache, $record, $index);

        // Check if the arrangement matches conditions.
        $isValidRecord = validateCompleteRecord($record, $groupSizes);

        // Increase the sum if valid.
        if ($isValidRecord === true) {
            $arrangements[] = $record;
        }
    }
}

/**
 * @param int[] $groupSizes
 */
function calculateArrangementSum(string $record, array $groupSizes): int
{
    $arrangements = [];
    $cache = [];

    iterateRecord($arrangements, $record, $groupSizes, $cache);

    return count($arrangements);
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $arrangementSum = 0;

    foreach ($input as $springRecord) {
        list($record, $groupSizes) = explode(' ', $springRecord);
        $record = trim($record);
        $groupSizes = array_map('intval', explode(',', trim($groupSizes)));

        $arrangementSum += calculateArrangementSum($record, $groupSizes);
    }

    return $arrangementSum;
}

/**
 * @param string[] $input
 */
function partTwo(array $input): int
{
    $arrangementSum = 0;

    foreach ($input as $springRecord) {
        list($record, $groupSizes) = explode(' ', $springRecord);
        $record = trim($record);
        $groupSizes = array_map('intval', explode(',', trim($groupSizes)));

        list($record, $groupSizes) = unfold($record, $groupSizes);

        $arrangementSum += calculateArrangementSum($record, $groupSizes);
    }

    return $arrangementSum;
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