<?php

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = [];

#################
### Solutions ###
#################

/**
 * A piece of equipment for the main character, either a weapon, piece or armor, or ring.
 */
class Equipment
{
    public function __construct(
        /** @var int $cost The amount of money required to buy this piece of equipment. */
        public int $cost = 0,

        /** @var int $damage The amount of damage the equipment can do. */
        public int $damage = 0,

        /** @var int $armor The amount of armor the equipment provides to protect against attacks. */
        public int $armor = 0,
    ) {
    }
}

/**
 * A character in the RPG.
 * Either the main character or the boss in this scenario.
 */
class Character
{
    /**
     * @var Equipment[] $equipment The weapons, armor, and rings worn by the character.
     */
    public array $equipment;

    /**
     * @var int $netDamage The amount of damage that manages to penetrate the enemy's armor.
     */
    public int $netDamage;

    public function __construct(
        /** @var int $hitPoints The amount of hit points, or life, the character has. */
        public int $hitPoints = 100,

        /** @var int $damage The amount of damage the character can do. */
        public int $damage = 0,

        /** @var int $armor The amount of armor the character has to protect against attacks. */
        public int $armor = 0,
    ) {
    }

    /**
     * Equips a piece of equipment on the character, increasing their stats.
     *
     * @param Equipment $equipment The piece of equipment to be equipped by the character.
     *
     * @return $this The current character.
     */
    public function equip(Equipment $equipment): self
    {
        $this->equipment[] = $equipment;

        $this->damage += $equipment->damage;
        $this->armor += $equipment->armor;

        return $this;
    }

    /**
     * Calculates the net damage done by this character to the enemy character, based on their damage and armor values.
     *
     * @param Character $enemy The enemy character the current character is facing.
     *
     * @return $this The current character.
     */
    public function setNetDamage(Character $enemy): self
    {
        $netDamage = $this->damage - $enemy->armor;

        $this->netDamage = $netDamage > 0 ? $netDamage : 1;

        return $this;
    }
}

/**
 * A list of weapons for the character to equip
 *
 * @return Equipment[] A list of weapons.
 */
function getWeapons(): array
{
    return [
        new Equipment(8, 4), # Dagger.
        new Equipment(10, 5), # Shortsword.
        new Equipment(25, 6), # Warhammer.
        new Equipment(40, 7), # Longsword.
        new Equipment(74, 8), # Greataxe.
    ];
}

/**
 * A list of armor for the character to equip.
 *
 * @return Equipment[] A list of armor.
 */
function getArmor(): array
{
    return [
        new Equipment(13, armor: 1), # Leather.
        new Equipment(31, armor: 2), # Chainmail.
        new Equipment(53, armor: 3), # Splintmail.
        new Equipment(75, armor: 4), # Bandedmail.
        new Equipment(102, armor: 5), # Platemail.
    ];
}

/**
 * A list of rings for the character to equip.
 *
 * @return Equipment[] A list of rings.
 */
function getRings(): array
{
    return [
        new Equipment(25, 1), # Damage +1
        new Equipment(50, 2), # Damage +2
        new Equipment(100, 3), # Damage +3
        new Equipment(20, armor: 1), # Defense +1
        new Equipment(40, armor: 2), # Defense +2
        new Equipment(80, armor: 3), # Defense +3
    ];
}

/**
 * Generate a list of all possible player characters and equipment sets.
 *
 * @return Character[]
 */
function equipCharacters(): array
{
    $characters = [];

    # Create the base player character with 100 hit points.
    $baseCharacter = new Character(100);

    # Distribute weapons among the characters.
    $weapons = getWeapons();
    foreach ($weapons as $weapon) {
        $clone = clone $baseCharacter;

        $characters[] = $clone->equip($weapon);
    }

    # Distribute armor among the weaponized characters.
    $armor = getArmor();
    foreach ($characters as $character) {
        foreach ($armor as $pieceOfArmor) {
            $clone = clone $character;

            $characters[] = $clone->equip($pieceOfArmor);
        }
    }

    # Distribute 2 rings among each of the weaponized and armored characters.
    $rings = getRings();
    foreach ($characters as $character) {
        $ringCount = count($rings);

        for ($i = 0; $i < $ringCount; $i++) {
            for ($j = $i + 1; $j <= $ringCount; $j++) {
                $clone = clone $character;

                $clone = $clone->equip($rings[$i]);
                $clone = $j === $ringCount ? $clone->equip($rings[0]) : $clone->equip($rings[$j]);

                $characters[] = $clone;
            }
        }
    }

    return $characters;
}

/**
 * Find all heroes capable of beating the boss.
 *
 * @param Character[] $characters The list of characters facing the boss.
 *
 * @return Character[] The list of heroes.
 */
function fightBoss(array $characters): array
{
    $fallen = [];
    $heroes = [];

    $boss = new Character(103, 9, 2);

    foreach ($characters as $character) {
        $character->setNetDamage($boss);
        $boss->setNetDamage($character);

        $turnsUntilVictory = ceil($boss->hitPoints / $character->netDamage);
        $turnsUntilDefeat = ceil($character->hitPoints / $boss->netDamage);

        if ($turnsUntilVictory <= $turnsUntilDefeat) {
            $heroes[] = $character;
        } else {
            $fallen[] = $character;
        }
    }

    return [$fallen, $heroes];
}

/**
 * Find the cheapest equipment set required to beat the boss.
 *
 * @param Character[] $heroes The list of heroes that beat the boss.
 *
 * @return int The cost of the cheapest equipment set.
 */
function calculateCheapestEquipment(array $heroes): int
{
    $cost = INF;

    foreach ($heroes as $hero) {
        $equipmentCost = array_reduce($hero->equipment, fn($sum, $equipment) => $sum + $equipment->cost);

        if ($cost > $equipmentCost) {
            $cost = $equipmentCost;
        }
    }

    return $cost;
}

/**
 * Find the most expensive equipment set that fails to beat the boss.
 *
 * @param Character[] $fallen A list of fallen characters.
 *
 * @return int The most expensive equipment set of the fallen characters.
 */
function calculateMostExpensiveEquipment(array $fallen): int
{
    $cost = 0;

    foreach ($fallen as $character) {
        $equipmentCost = array_reduce($character->equipment, fn($sum, $equipment) => $sum + $equipment->cost);

        if ($cost < $equipmentCost) {
            $cost = $equipmentCost;
        }
    }

    return $cost;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $characters = equipCharacters();

    [$fallen, $heroes] = fightBoss($characters);

    return calculateCheapestEquipment($heroes);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $characters = equipCharacters();

    [$fallen, $heroes] = fightBoss($characters);

    return calculateMostExpensiveEquipment($fallen);
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));