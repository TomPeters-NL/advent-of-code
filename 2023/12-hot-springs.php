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
    // Extract the groups currently present in the partial record.
    preg_match_all('/#+/', $record, $matches);
    $currentGroups = $matches[0];
    $groupCount = count($currentGroups);

    // Check whether the current record does not exceed the number of required groups.
    $validGroupCount = $groupCount <= count($groupSizes);

    // Check whether the existing groups adhere to the required length.
    $validGroupLength = $groupCount === 0;
    if ($validGroupCount === true && $groupCount > 0) {
        foreach ($currentGroups as $index => $group) {
            $validGroupLength = strlen($group) <= $groupSizes[$index];

            if ($validGroupLength === false) {
                break;
            }
        }
    }

    return $validGroupCount && $validGroupLength;
}

/**
 * @param string[] $arrangements
 * @param int[] $groupSizes
 * @param string[] $cache
 */
function iterateRecord(array &$arrangements, string $record, array $groupSizes, array &$cache): void
{
    $isInCache = array_key_exists($record, $cache);
    $isDamagedRecord = str_contains($record, '?');

    if ($isInCache === false && $isDamagedRecord === true) {
        // Cache the current arrangement.
        $cache[$record] = $record;

        // Validate current progress.
        $target = strpos($record, '?');
        $progress = substr($record, 0, $target + 1);
        $isValidRecord = validatePartialRecord($progress, $groupSizes);

        // Create new arrangements.
        $iteration1 = $iteration2 = $record;

        $iteration1[$target] = '.';
        $iteration2[$target] = '#';

        // Continue iteration process.
        if ($isValidRecord === true) {
            iterateRecord($arrangements, $iteration1, $groupSizes, $cache);
            iterateRecord($arrangements, $iteration2, $groupSizes, $cache);
        }
    }

    if ($isInCache === false && $isDamagedRecord === false) {
        // Cache the current arrangement.
        $cache[$record] = $record;

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