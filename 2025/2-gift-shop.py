from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/2.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

product_ranges = raw_input.strip().split(',')
product_ids = []

for product_range in product_ranges:
    lower, upper = map(int, product_range.split('-'))
    product_ids += range(lower, upper + 1)

for product_id in product_ids:
    string_id = str(product_id)
    id_length = len(string_id)

    if id_length % 2 != 0:
        continue

    middle_index = id_length // 2

    if string_id[middle_index:] == string_id[:middle_index]:
        solution_one += product_id

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

product_ranges = raw_input.strip().split(',')
product_ids = []

for product_range in product_ranges:
    lower, upper = map(int, product_range.split('-'))
    product_ids += range(lower, upper + 1)

for product_id in product_ids:
    string_id = str(product_id)

    if (string_id + string_id).find(string_id, 1, -1) != -1:
        solution_two += product_id

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
