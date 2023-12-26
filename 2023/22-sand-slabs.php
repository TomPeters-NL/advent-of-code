<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/22', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * The X, Y, Z location of a falling brick.
 */
class Coordinate
{
    public function __construct(public int $x, public int $y, public int $z)
    {
    }
}

/**
 * A falling sand brick, its ID and dimensions.
 */
class Brick
{
    /**
     * @var Brick[] $supports A list of bricks supported by this brick.
     */
    public array $supports = [];

    /**
     * @var Brick[] $supports A list of bricks supporting this brick.
     */
    public array $supportedBy = [];

    public function __construct(public int|string $id, public Coordinate $start, public Coordinate $end)
    {
        #$this->id = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[$id]; # Enable for the example dataset to easily cross-reference with the puzzle description.
    }

    /**
     * Compares two bricks to determine whether their coordinates intersect.
     *
     * @param Brick $brick The falling brick to be compared to this one.
     *
     * @return bool Indicates whether the bricks intersect.
     */
    public function intersects(Brick $brick): bool
    {
        # Four scenarios per axis:
        #     1. Left-side overlap.  [ B ] A ]
        #     2. Right-side overlap. [ A [ B ]
        #     3. Inner overlap.      [ A [ B ] A ]
        #     4. Outer overlap.      [ B [ A ] B ]
        $intersectX = false;
        $intersectY = false;

        if (
            ($brick->start->x >= $this->start->x && $brick->start->x <= $this->end->x)
            || ($brick->end->x <= $this->end->x && $brick->end->x >= $this->start->x)
            || ($brick->start->x <= $this->start->x && $brick->end->x >= $this->end->x)
            || ($brick->start->x >= $this->start->x && $brick->end->x <= $this->end->x)
        ) {
            $intersectX = true;
        }

        if (
            ($brick->start->y >= $this->start->y && $brick->start->y <= $this->end->y)
            || ($brick->end->y <= $this->end->y && $brick->end->y >= $this->start->y)
            || ($brick->start->y <= $this->start->y && $brick->end->y >= $this->end->y)
            || ($brick->start->y >= $this->start->y && $brick->end->y <= $this->end->y)
        ) {
            $intersectY = true;
        }

        $intersect = $intersectX === true && $intersectY === true;
        if ($intersect === true) {
            $this->supports[] = $brick;
            $brick->supportedBy[] = $this;
        }

        return $intersect;
    }
}

/**
 * Sort the sand brick snapshot by the bricks' Z indices.
 *
 * @param string[] $input The puzzle input.
 *
 * @return string[] The sorted snapshot.
 */
function sortSnapshot(array $input): array
{
    usort($input, function ($alpha, $beta) {
        preg_match('/,(\d+)~/', $alpha, $matchAlpha);
        preg_match('/,(\d+)~/', $beta, $matchBeta);

        return (int)$matchAlpha[1] <=> (int)$matchBeta[1];
    });

    return $input;
}

/**
 * Convert the separate falling sand brick dimensions to brick objects.
 *
 * @param string[] $snapshot A snapshot of falling bricks' dimensions.
 *
 * @return Brick[] A list of falling sand bricks.
 */
function brickifySnapshot(array $snapshot): array
{
    $bricks = [];

    foreach ($snapshot as $index => $sand) {
        [$startDimensions, $endDimensions] = explode('~', $sand);

        $start = new Coordinate(...array_map('intval', explode(',', $startDimensions)));
        $end = new Coordinate(...array_map('intval', explode(',', $endDimensions)));

        $bricks[$index] = new Brick($index, $start, $end);
    }

    return $bricks;
}

/**
 * Calculates the resulting sand brick stack from the snapshot of falling bricks.
 *
 * @param Brick[] $bricks A snapshot of falling sand bricks.
 *
 * @return Brick[][] The ultimate stack of sand bricks.
 */
function stackBricks(array $bricks): array
{
    $firstBrick = array_shift($bricks);

    $stack = [1 => [$firstBrick]];

    while (empty($bricks) === false) {
        # Retrieve the next falling brick.
        $fallingBrick = array_shift($bricks);

        # Reverse the stack to iterate through it top to bottom to find the layer it will land on.
        $topToBottomStack = array_reverse($stack, true);
        foreach ($topToBottomStack as $z => $stackLayer) {
            # Preset the layer to current one, assuming it does not encounter another brick.
            $layer = $z;

            # Check each brick in the current layer for a potential intersect.
            foreach ($stackLayer as $brick) {
                $intersect = $brick->intersects($fallingBrick);

                # If the falling brick encounters another brick, set its layer to the one above it.
                if ($intersect === true) {
                    $layer = $z + 1;
                }
            }

            # Exit the loop if the falling brick encountered another brick.
            if ($layer !== $z) {
                break;
            }
        }

        # Place the brick on its respective layer(s).
        $brickHeight = $fallingBrick->end->z - $fallingBrick->start->z;
        for ($modifier = 0; $modifier <= $brickHeight; $modifier++) {
            $stack[$layer + $modifier][] = $fallingBrick;
        }
    }

    return $stack;
}

/**
 * Calculates the amount of sand bricks in the stack that could be safely disintegrated.
 *
 * @param Brick[][] $stack A stable stack of sand bricks.
 *
 * @return Brick[] The sand bricks that may be disintegrated without affecting structural integrity.
 */
function getDisintegrationTargets(array $stack): array
{
    $disintegrationTargets = [];

    foreach ($stack as $z => $layer) {
        # Determine which bricks are supported by which amount of bricks on this layer.
        $supports = [];
        foreach ($layer as $brick) {
            foreach ($brick->supports as $supportedBrick) {
                $supportId = $supportedBrick->id;

                $supports[$z][$supportId] = isset($supports[$z][$supportId]) === false ? 1 : $supports[$z][$supportId] + 1;
            }
        }

        # Check whether each brick could be safely removed.
        foreach ($layer as $brick) {
            $safe = true;

            foreach ($brick->supports as $supportedBrick) {
                $supportId = $supportedBrick->id;

                if ($supports[$z][$supportId] <= 1) {
                    $safe = false;
                    break;
                }
            }

            if ($safe === true) { # If safe, add the brick to the targets list once.
                $disintegrationTargets[$brick->id] = $brick;
            } elseif (array_key_exists($brick->id, $disintegrationTargets) === true) { # If deemed unsafe and in the target list, remove it.
                unset($disintegrationTargets[$brick->id]);
            }
        }
    }

    return $disintegrationTargets;
}

/**
 * Calculates the amount of sand blocks falling as part of the disintegration cascade.
 *
 * @param string[] $disintegrated A list of the disintegrated and fallen sand blocks so far.
 * @param Brick[] $supportedBricks A list of sand bricks supported by the most recently fallen sand brick.
 *
 * @return int The amount of sand bricks falling in this cascade.
 */
function calculateCascade(array &$disintegrated, array $supportedBricks): int
{
    $cascade = 0;

    foreach ($supportedBricks as $brick) {
        # Assume the support structure for this brick is failing.
        $supportFailure = true;

        # Check if all the bricks supporting this brick are also disintegrated.
        foreach ($brick->supportedBy as $supportingBrick) {
            # If even one supporting brick remains, the brick does not fall.
            if (in_array($supportingBrick->id, $disintegrated) === false) {
                $supportFailure = false;
                break;
            }
        }

        if ($supportFailure === true) {
            # Add the brick to the list of fallen bricks.
            $disintegrated[] = $brick->id;

            # Increase the cascade counter by one.
            $cascade++;

            # As this brick will fall, determine how many of the bricks it supports will fall.
            $cascade += calculateCascade($disintegrated, $brick->supports);
        }
    }

    return $cascade;
}

/**
 * Generates a list of the cascade size per sand brick.
 *
 * @param Brick[][] $stack The complete stack of sand bricks.
 * @param Brick[] $disintegrationTargets The list of safe-to-disintegrate sand bricks.
 *
 * @return int[] The cascade size per sand brick.
 */
function getDisintegrationCascades(array $stack, array $disintegrationTargets): array
{
    $cascades = [];

    foreach ($stack as $layer) {
        foreach ($layer as $brick) {
            # If the target can be safely disintegrated, ignore it.
            if (array_key_exists($brick->id, $disintegrationTargets) === true) {
                continue;
            }

            # Calculate the size of the cascade for this brick.
            $disintegrated = [$brick->id];
            $cascades[$brick->id] = calculateCascade($disintegrated, $brick->supports);
        }
    }

    return $cascades;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $snapshot = sortSnapshot($input);
    $bricks = brickifySnapshot($snapshot);
    $stack = stackBricks($bricks);

    return count(getDisintegrationTargets($stack));
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $snapshot = sortSnapshot($input);
    $bricks = brickifySnapshot($snapshot);
    $stack = stackBricks($bricks);
    $disintegrationTargets = getDisintegrationTargets($stack);

    return array_sum(getDisintegrationCascades($stack, $disintegrationTargets));
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));