from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/5.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

split_index = raw_input.splitlines().index('')
fresh_ranges = raw_input.splitlines()[:split_index]
ingredient_list = [int(ingredient) for ingredient in raw_input.splitlines()[split_index + 1:]]

# # # # # # #
# Part  One #
# # # # # # #

for ingredient in ingredient_list:
    for fresh_ingredients in fresh_ranges:
        first, last = [int(fresh_ingredient) for fresh_ingredient in fresh_ingredients.split('-')]

        if ingredient >= first and ingredient <= last:
            solution_one += 1
            break

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

sorted_fresh_ranges = sorted([[int(first), int(last)] for first, last in (fresh_range.split('-') for fresh_range in fresh_ranges)])
net_fresh_ranges = [sorted_fresh_ranges.pop(0)]

for first, last in sorted_fresh_ranges:
    net_first, net_last = net_fresh_ranges[-1]

    if first <= net_last:
        net_fresh_ranges[-1] = [net_first, max(last, net_last)]
    else:
        net_fresh_ranges.append([first, last])

for first, last in net_fresh_ranges:
    solution_two += last - first + 1

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
