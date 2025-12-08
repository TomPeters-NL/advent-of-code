from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/0.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #


# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #


# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
