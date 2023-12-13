<?php

$input = file('./input/12.txt');

/**
 * @param string $record
 * @param int[] $groupSizes
 * @return array
 */
function unfold(string $record, array $groups): array
{
    $records = array_fill(0, 5, $record);
    $unfoldedRecord = implode('?', $records);

    $groups = array_fill(0, 5, $groups);
    $unfoldedGroups = array_merge(...$groups);

    return [$unfoldedRecord, $unfoldedGroups];
}

function calculateArrangements(string $record, array $groups, array &$cache = []): int
{
    // Check the cache for reoccuring combinations.
    $cacheRecord = $record . '-' . implode(',', $groups);
    if (array_key_exists($cacheRecord, $cache) === true) {
        return $cache[$cacheRecord];
    }

    // Once the last of the record has been checked, check if all groups were handled.
    if ($record === '') {
        return (int) empty($groups);
    }

    // Once all groups have been handled, check whether there are no more springs left in the record.
    // Returns "1" if all springs were handled, "0" if there are any springs left.
    if (empty($groups) === true) {
        return (int) !str_contains($record, '#');
    }

    // Initialize the arrangement sum.
    $arrangements = 0;

    // Check if the string starts with an empty or damaged space (potential empty space).
    // If so, yeet it into the next iteration without that first character.
    $startsWithoutSpring = str_starts_with($record, '.') || str_starts_with($record, '?');
    if ($startsWithoutSpring === true) {
        $nextRecord = substr($record, 1);

        $arrangements += calculateArrangements($nextRecord, $groups, $cache);
    }

    // Check if the string starts with a spring or damaged space (potential spring).
    $startsWithSpring = str_starts_with($record, '#') || str_starts_with($record, '?');

    if ($startsWithSpring === true) {
        // Check whether there is enough left of the record for the next group length.
        $groupLength = $groups[0];
        $enoughRecordLeft = strlen($record) >= $groupLength;

        if ($enoughRecordLeft === true) {
            // Check whether the group would contain any empty spaces, if grouped as-is.
            $group = substr($record, 0, $groupLength);
            $emptySpacesInGroup = str_contains($group, '.');

            // Check whether the group would be followed by a spring if grouped as-is.
            $isDiscreteGroup = ($record[$groupLength] ?? '') !== '#';

            if ($emptySpacesInGroup === false && $isDiscreteGroup === true) {
                // With a succesful grouping completed, generate the remaining record and groups.
                // The "substr()" offset is increased by "1" to include the empty space required after the group.
                $remainingRecord = substr($record, $groupLength + 1);
                $remainingGroups = array_slice($groups, 1);

                $arrangements += calculateArrangements($remainingRecord, $remainingGroups, $cache);
            }
        }
    }

    // Add the current record to the cache.
    $cache[$cacheRecord] = $arrangements;

    return $arrangements;
}

/**
 * @param string[] $input
 */
function partOne(array $input): int
{
    $arrangementSum = 0;

    foreach ($input as $springRecord) {
        list($record, $groups) = explode(' ', $springRecord);
        $record = trim($record);
        $groups = array_map('intval', explode(',', trim($groups)));

        $arrangementSum += calculateArrangements($record, $groups);
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
        list($record, $groups) = explode(' ', $springRecord);
        $record = trim($record);
        $groups = array_map('intval', explode(',', trim($groups)));

        list($unfoldedRecord, $unfoldedGroups) = unfold($record, $groups);

        $arrangementSum += calculateArrangements($unfoldedRecord, $unfoldedGroups);
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