<?php

declare(strict_types=1);

######################
### Initialization ###
######################

require_once(__DIR__ . '/../helper/AdventHelper.php');

use AdventOfCode\Helper\AdventHelper;

$adventHelper = new AdventHelper();

$input = file('./input/15', FILE_IGNORE_NEW_LINES);

#################
### Solutions ###
#################

/**
 * The total amount of teaspoons of all ingredients combined.
 */
const INGREDIENT_MAXIMUM = 100;

/**
 * The amount of calories required in a cookie.
 */
const CALORIE_GOAL = 500;

/**
 * Extracts all ingredients and their properties from the puzzle input.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] The properties of each ingredient, indexed by the ingredient name.
 */
function extractIngredients(array $input): array
{
    $ingredients = [];

    foreach ($input as $line) {
        preg_match('/([A-Z][a-z]+)[^-0-9]+(-?\d+)[^-0-9]+(-?\d+)[^-0-9]+(-?\d+)[^-0-9]+(-?\d+)[^-0-9]+(-?\d+)/', $line, $matches);

        [$ignore, $name, $capacity, $durability, $flavor, $texture, $calories] = $matches;

        $ingredients[$name] = [(int) $capacity, (int) $durability, (int) $flavor, (int) $texture, (int) $calories];
    }

    return $ingredients;
}

/**
 * Generates all possible recipes with the ingredients provided.
 *
 * @param string[] $names       The names of the ingredients.
 * @param int      $count       The amount of ingredients.
 * @param int[][]  $recipes     A list of recipes, each containing the amount of teaspoons used per ingredient.
 * @param bool     $calorieGoal Determines whether recipes not exactly matching the calorie goal are disqualified.
 *
 * @return int[][] A list of recipes, each containing the amount of teaspoons used per ingredient.
 */
function generateRecipes(array $ingredients, array $names, int $count, bool $calorieGoal = false, array $recipes = []): array
{
    $ingredient = array_shift($names);

    # The first ingredient.
    if (empty($recipes) === true) {
        # Calculate the initial maximum.
        # For 4 ingredients: 100 - 4 + 1 = 97. If the 3 other ingredients are all 1 teaspoon, the sum is 100.
        $maximum = INGREDIENT_MAXIMUM - $count + 1;

        for ($i = 1; $i <= $maximum; $i++) {
            $recipes[][$ingredient] = $i;
        }
    }

    # All other ingredients.
    if (empty($names) === false) {
        foreach ($recipes as $index => $recipe) {
            $sumTeaspoons = array_sum($recipe);

            # Calculate the maximum using the current sum and the assumption that next ingredients will be 1 teaspoon.
            $maximum = INGREDIENT_MAXIMUM - $sumTeaspoons - count($names);
            for ($i = 1; $i <= $maximum; $i++) {
                $newRecipe = $recipe;
                $newRecipe[$ingredient] = $i;

                $recipes[] = $newRecipe;
            }

            unset($recipes[$index]);
        }
    }

    # The final ingredient.
    if (empty($names) === true) {
        # No need to calculate a maximum, simply add each recipe up to the maximum.
        foreach ($recipes as &$recipe) {
            $sumTeaspoons = array_sum($recipe);

            $recipe[$ingredient] = INGREDIENT_MAXIMUM - $sumTeaspoons;

            # Immediately calculate the cookie recipe's score.
            $recipe['score'] = calculateCookieScore($ingredients, $recipe, $calorieGoal);
        }
    }

    # Keep expanding the recipe as long as there are ingredients.
    if (empty($names) === false) {
        $recipes = generateRecipes($ingredients, $names, $count, $calorieGoal, $recipes);
    }

    return $recipes;
}

/**
 * Calculates the score of a cookie recipe, based on each ingredient's properties.
 *
 * @param int[][] $ingredients The properties of each available ingredient.
 * @param int[]   $recipe      The amount of teaspoons per ingredient in this recipe.
 * @param bool    $calorieGoal Determines whether recipes not exactly matching the calorie goal are disqualified.
 *
 * @return int The total score for the provided recipe.
 */
function calculateCookieScore(array $ingredients, array $recipe, bool $calorieGoal = false): int
{
    $totalCalories = 0;
    $score = [
        'capacity' => 0,
        'durability' => 0,
        'flavor' => 0,
        'texture' => 0,
    ];

    foreach ($recipe as $ingredient => $teaspoons) {
        [$capacity, $durability, $flavor, $texture, $calories] = $ingredients[$ingredient];

        $score['capacity'] += $teaspoons * $capacity;
        $score['durability'] += $teaspoons * $durability;
        $score['flavor'] += $teaspoons * $flavor;
        $score['texture'] += $teaspoons * $texture;
        $totalCalories += $teaspoons * $calories;
    }

    $recipeScore = array_product($score);
    if ($calorieGoal === true && $totalCalories !== CALORIE_GOAL) {
        $recipeScore = 0;
    }

    foreach ($score as $property) {
        if ($property < 0) {
            $recipeScore = 0;
            break;
        }
    }

    return $recipeScore;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $ingredients = extractIngredients($input);

    $names = array_keys($ingredients);
    $count = count($ingredients);

    $recipes = generateRecipes($ingredients, $names, $count);

    return array_reduce($recipes, fn($max, $recipe) => max($max, $recipe['score']), 0);
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $ingredients = extractIngredients($input);

    $names = array_keys($ingredients);
    $count = count($ingredients);

    $recipes = generateRecipes($ingredients, $names, $count, true);

    return array_reduce($recipes, fn($max, $recipe) => max($max, $recipe['score']));
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));