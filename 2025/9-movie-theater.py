import matplotlib
matplotlib.use('TkAgg')

from pathlib import Path
from time import time
from shapely.geometry import Polygon
from shapely.plotting import plot_polygon
from matplotlib.pyplot import show
from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/9.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

def calculate_rectangle_area(corner_a: tuple, corner_b: tuple) -> int:
    (a_x, a_y), (b_x, b_y) = corner_a, corner_b

    return (abs(a_x - b_x) + 1) *  (abs(a_y - b_y) + 1)

# # # # # # #
# Part  One #
# # # # # # #

red_tiles = [(int(x), int(y)) for [x, y] in [coordinates.split(',') for coordinates in raw_input.strip().splitlines()]]
rectangle_areas = []

for index, tile_a in enumerate(red_tiles):
    for tile_b in red_tiles[index + 1:]:
        (a_x, a_y), (b_x, b_y) = tile_a, tile_b
        
        if a_x == b_x or a_y == b_y:
            continue

        rectangle_areas.append(calculate_rectangle_area(tile_a, tile_b))

solution_one = max(rectangle_areas)

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

red_tiles = [(int(x), int(y)) for [x, y] in [coordinates.split(',') for coordinates in raw_input.strip().splitlines()]]
rectangle_areas = {}

polygon = Polygon(red_tiles)

for index, tile_a in enumerate(red_tiles):
    for tile_b in red_tiles[index + 1:]:
        (a_x, a_y), (b_x, b_y) = tile_a, tile_b
        
        if a_x == b_x or a_y == b_y:
            continue

        rectangle = Polygon((tile_a, (a_x, b_y), tile_b, (b_x, a_y)))
        if not polygon.contains(rectangle):
            continue

        rectangle_areas[(tile_a, tile_b)] = calculate_rectangle_area(tile_a, tile_b)

largest_rectangle = max(rectangle_areas, key=rectangle_areas.get)
solution_two = rectangle_areas[largest_rectangle]

((a_x, a_y), (b_x, b_y)) = largest_rectangle
plot_polygon(polygon, color='blue')
plot_polygon(Polygon([(a_x, a_y), (a_x, b_y), (b_x, b_y), (b_x, a_y)]), color='green')
show()

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
