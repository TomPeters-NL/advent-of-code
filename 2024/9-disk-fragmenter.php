<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/9', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

function generateBlockMap(array $diskMap): array
{
    $blockMap = [];

    $blockSizes = array_map('intval', str_split($diskMap[0]));
    $isFile = true;
    $fileId = 0;

    foreach ($blockSizes as $blockSize) {
        $blockId = $isFile ? $fileId++ : '.';

        $block = array_fill(0, $blockSize, $blockId);

        $blockMap = array_merge($blockMap, $block);

        $isFile = !$isFile;
    }

    return $blockMap;
}

function organizeDiskByBlock(array $blockMap): array
{
    $organizedBlockMap = [];

    foreach ($blockMap as &$blockId) {
        if ($blockId !== '.') {
            $organizedBlockMap[] = $blockId;

            continue;
        }

        do {
            $moveBlockId = array_pop($blockMap);
        } while ($moveBlockId === '.');

        $organizedBlockMap[] = $moveBlockId;
    }

    return $organizedBlockMap;
}

function getFileDetails(array $blockMap): array
{
    $files = [];

    foreach ($blockMap as $index => $blockId) {
        if ($blockId === '.') {
            continue;
        }

        if (array_key_exists($blockId, $files)) {
            $files[$blockId]['size']++;
            $files[$blockId]['indices'][] = $index;
        } else {
            $files[$blockId] = [
                'size' => 1,
                'indices' => [$index],
            ];
        }
    }

    return $files;
}

function findEmptySpace(array $blockMap, int $size): array
{
    $emptySpaces = [];
    $sequence = 0;

    foreach ($blockMap as $blockIndex => $blockId) {
        if ($blockId !== '.') {
            $sequence = 0;
            continue;
        }

        if (++$sequence === $size) {
            $emptySpaces = range($blockIndex - $size + 1, $blockIndex);

            break;
        }
    }


    return $emptySpaces;
}

function organizeDiskByFile(array $blockMap): array
{
    $files = array_reverse(getFileDetails($blockMap), true);

    foreach ($files as $fileId => ['size' => $fileSize, 'indices' => $fileIndices]) {
        $emptySpaces = findEmptySpace($blockMap, $fileSize);

        if (empty($emptySpaces) || max($fileIndices) < min($emptySpaces)) {
            continue;
        }

        foreach ($fileIndices as $fileIndex) {
            $blockMap[$fileIndex] = 'x';
        }

        foreach ($emptySpaces as $spaceIndex) {
            $blockMap[$spaceIndex] = $fileId;
        }
    }

    return array_map(fn ($blockId) => $blockId === 'x' ? '.' : $blockId, $blockMap);
}

function createChecksum(array $organizedBlockMap): int
{
    $checksum = 0;

    foreach ($organizedBlockMap as $index => $blockId) {
        if ($blockId === '.') {
            continue;
        }

        $checksum += $index * $blockId;
    }

    return $checksum;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $blockMap = generateBlockMap($input);

    $organizedBlockMap = organizeDiskByBlock($blockMap);

    return createChecksum($organizedBlockMap);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $blockMap = generateBlockMap($input);

    $organizedBlockMap = organizeDiskByFile($blockMap);

    return createChecksum($organizedBlockMap);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));