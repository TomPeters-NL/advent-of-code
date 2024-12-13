<?php

declare(strict_types=1);

namespace AdventOfCode\Year2024;

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

/** A day in the Advent of Code, containing solutions to a two-part puzzle. */
class Day9
{
    private AdventHelper $adventHelper;
    private array $input;

    public function __construct()
    {
        $this->adventHelper = new AdventHelper();
        $this->input = file('./input/9', FILE_IGNORE_NEW_LINES);
    }

    #############
    ### Logic ###
    #############

    /**
     * Generates a multidimensional block map, which:
     * - Contains all block IDs as a list.
     * - Contains a list of file indices, indexed by their file ID.
     * - Contains a list of available empty spaces, indexed by their starting index.
     *
     * @param array $diskMap The puzzle input.
     *
     * @return array{complete: array<int, string>, files: int[][], empty: int[]} The resulting block map.
     */
    function generateBlockMap(array $diskMap): array
    {
        $blockMap = [
            'complete' => [],
            'files' => [],
            'empty' => [],
        ];

        $blockSizes = array_map('intval', str_split($diskMap[0]));
        $isFile = true;
        $fileId = 0;

        foreach ($blockSizes as $blockSize) {
            $blockId = $isFile ? $fileId++ : '.';

            # Generate the file or empty blocks.
            $block = array_fill(0, $blockSize, $blockId);

            # Add the blocks to the list of block IDs.
            $blockMap['complete'] = array_merge($blockMap['complete'], $block);

            $blockIndices = array_keys($blockMap['complete']);
            if ($isFile) {
                # If the block is a file, list the indices it spans by its file ID.
                $blockMap['files'][$blockId] = array_slice($blockIndices, -$blockSize, $blockSize);
            } elseif ($blockSize > 0) {
                # If the block is empty, list the amount of empty spaces by its starting index.
                $index = array_slice($blockIndices, -$blockSize, 1)[0];
                $blockMap['empty'][$index] = $blockSize;
            }

            $isFile = !$isFile;
        }

        return $blockMap;
    }

    /**
     * Reorganizes the disk's contents by individual block, rather than complete files.
     * This just further fragments the files, but hey, what do you expect from an amphipod?
     *
     * @param array<int, string> $blockMap The complete block map.
     *
     * @return array<int, string> The (re)organized block map.
     */
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

    /**
     * Find available empty spaces which would fit a target file.
     *
     * @param int[] $emptySpaces A list of available empty spaces, indexed by their starting index in the block map.
     * @param int[] $fileIndices A list of file indices in the block map.
     *
     * @return int[] A list of empty spaces that can hold the indicated file.
     */
    function findEmptySpace(array &$emptySpaces, array $fileIndices): array
    {
        $validEmptySpaces = [];

        $fileSize = count($fileIndices);
        $lastFileIndex = max($fileIndices);

        foreach ($emptySpaces as $spaceIndex => $spaceSize) {
            # Skip the current space if it isn't large enough or if it would cause a file to be moved forward.
            if ($lastFileIndex < $spaceIndex || $spaceSize < $fileSize) {
                continue;
            }

            $lastEmptySpaceIndex = $spaceIndex + $fileSize;
            $validEmptySpaces = range($spaceIndex, $lastEmptySpaceIndex - 1);

            $remainingSpace = $spaceSize - $fileSize;

            if ($remainingSpace !== 0) {
                $emptySpaces[$lastEmptySpaceIndex] = $remainingSpace;
            }

            unset($emptySpaces[$spaceIndex]);

            ksort($emptySpaces);

            break;
        }

        return $validEmptySpaces;
    }

    /**
     * Reorganizes the disk's contents by file, leaving more empty spaces, but preventing file fragmentation.
     *
     * @param array<int, string> $blockMap The complete block map.
     * @param int[][]            $files A list of file indices, indexed by their file ID.
     * @param int[]              $emptySpaces A list of available empty spaces, indexed by their starting index in the block map.
     *
     * @return array<int, string> The (re)organized block map.
     */
    function organizeDiskByFile(array $blockMap, array $files, array $emptySpaces): array
    {
        $files = array_reverse($files, true);

        foreach ($files as $fileId => $fileIndices) {
            $validEmptySpaces = $this->findEmptySpace($emptySpaces, $fileIndices);

            if (empty($validEmptySpaces)) {
                continue;
            }

            foreach ($fileIndices as $fileIndex) {
                $blockMap[$fileIndex] = '.';
            }

            foreach ($validEmptySpaces as $spaceIndex) {
                $blockMap[$spaceIndex] = $fileId;
            }
        }

        return $blockMap;
    }

    /**
     * Calculates a new checksum for a block map.
     *
     * @param array<int, string> $organizedBlockMap The organized, or defragmented, block map.
     *
     * @return int The new checksum.
     */
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

    #################
    ### Solutions ###
    #################

    /**
     * Returns the solution for the first part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partOne(array $input): int
    {
        $blockMap = $this->generateBlockMap($input);

        $organizedBlockMap = $this->organizeDiskByBlock($blockMap['complete']);

        return $this->createChecksum($organizedBlockMap);
    }

    /**
     * Returns the solution for the second part of this day's puzzle.
     *
     * @param string[] $input The puzzle input.
     */
    private function partTwo(array $input): int
    {
        $blockMap = $this->generateBlockMap($input);

        $organizedBlockMap = $this->organizeDiskByFile($blockMap['complete'], $blockMap['files'], $blockMap['empty']);

        return $this->createChecksum($organizedBlockMap);
    }

    ###############
    ### Results ###
    ###############

    public function printSolutions(): void
    {
        $this->adventHelper->printSolutions(
            $this->partOne($this->input),
            $this->partTwo($this->input),
        );
    }
}

(new Day9())->printSolutions();