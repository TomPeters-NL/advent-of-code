<?php

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

/**
 * Generates a list of cities and the travel distances between them.
 *
 * @param string[] $input The puzzle input.
 *
 * @return int[][] A list of distances between cities.
 */
function createDistanceMap(array $input): array
{
    $map = [];

    foreach ($input as $line) {
        [$destinations, $distance] = explode(' = ', $line);
        [$departure, $arrival] = explode(' to ', $destinations);

        $map[$departure][$arrival] = (int)$distance;
        $map[$arrival][$departure] = (int)$distance;
    }

    return $map;
}

/**
 * Generates all possible routes between cities and calculates their total distances.
 *
 * @param int[][] $map A list of distances between cities.
 * @param int[] $routes A list of distances per route.
 *
 * @return int[] A list of potential routes and their total distances.
 */
function generateRoutes(array $map, array $routes = []): array
{
    $newRoutes = 0;
    $cities = array_keys($map);

    if (empty($routes) === true) { # Initialize each route during the first loop.
        foreach ($cities as $city) {
            $routes[$city] = 0;
            $newRoutes++;
        }
    } else { # For each route, add a departure to each other unvisited city.
        foreach ($routes as $route => $distance) {
            foreach ($cities as $city) {
                # If the city has already been visited, skip this city.
                if (str_contains($route, $city) === true) {
                    continue;
                }

                $visited = explode('->', $route);
                $lastCity = $visited[array_key_last($visited)];

                $newRoute = $route . '->' . $city;
                $newDistance = $distance + $map[$lastCity][$city];

                $routes[$newRoute] = $newDistance;
                $newRoutes++;
            }

            # Only remove the current route if new routes have been generated.
            if ($newRoutes > 0) {
                unset($routes[$route]);
            }
        }
    }

    # Repeat this process while new routes are still being generated.
    if ($newRoutes > 0) {
        $routes = generateRoutes($map, $routes);
    }

    return $routes;
}

/**
 * Returns the solution for the first part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partOne(array $input): int
{
    $map = createDistanceMap($input);

    $routes = generateRoutes($map);

    asort($routes);

    return $routes[array_key_first($routes)];
}

/**
 * Returns the solution for the second part of this day's puzzle.
 *
 * @param string[] $input The puzzle input.
 */
function partTwo(array $input): int
{
    $map = createDistanceMap($input);

    $routes = generateRoutes($map);

    asort($routes);

    return $routes[array_key_last($routes)];
}

###############
### Results ###
###############

$adventHelper->printSolutions(partOne($input), partTwo($input));