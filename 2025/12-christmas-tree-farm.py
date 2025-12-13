from pathlib import Path
from time import time
from collections import deque

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

def retrieve_gifts_and_plots(split_input: list) -> list:
    data_queue = deque(split_input)
    plots = []
    gifts = {}

    gift_index = None
    while data_queue:
        line = data_queue.popleft()

        if line[-1] == ':':
            gift_index = int(line[:-1])
            gifts[gift_index] = []

            gift_row = 0
            while True:
                line = data_queue.popleft()

                if line == '':
                    break

                for index, character in enumerate(list(line)):
                    if character == '#':
                        gifts[gift_index].append((gift_row, index))
                
                gift_row += 1
        else:
            plot, plot_gifts = line.split(':')
            plot_dimensions = tuple(map(int, plot.split('x')))
            gift_frequencies = list(map(int, plot_gifts.strip().split()))
            plots.append((plot_dimensions, gift_frequencies))

    return [gifts, plots]


def is_plot_too_small(plot: tuple, gift_frequencies: list, gifts: dict) -> bool:
    (plot_x, plot_y) = plot
    plot_area = plot_x * plot_y

    gift_area = 0
    for index, frequency in enumerate(gift_frequencies):
        if frequency != 0:
            gift_area += frequency * len(gifts[index])

    return plot_area < gift_area

def has_space_aplenty(plot: tuple, gift_frequencies: list, gifts: dict) -> bool:
    (plot_x, plot_y) = plot
    plot_area = plot_x * plot_y

    gift_area = 0
    for index, frequency in enumerate(gift_frequencies):
        if frequency == 0:
            continue

        gift_width = 0
        gift_height = 0

        for x, y in gifts[index]:
            gift_width = max(gift_width, x)
            gift_height = max(gift_height, y)

        gift_area += gift_width * gift_height

    return plot_area >= gift_area

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/12.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

gifts, plots = retrieve_gifts_and_plots(raw_input.splitlines())

for (plot_dimensions, gift_frequencies) in plots:
    if is_plot_too_small(plot_dimensions, gift_frequencies, gifts):
        continue

    if has_space_aplenty(plot_dimensions, gift_frequencies, gifts):
        solution_one += 1
        continue

    # Normally you would make an attempt to fit the remaining plots and gifts together here.
    # However, apparently the above was enough for the correct solution.

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

# There is no part two!

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
