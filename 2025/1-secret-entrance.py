from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/1.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

turns = [int(line.strip().replace('L', '-').replace('R', '')) for line in raw_input.splitlines()]

dial = 50

for turn in turns:
    dial = (dial + turn) % 100
    if dial == 0: solution_one += 1

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

input_two = raw_input
turns = [int(line.strip().replace('L', '-').replace('R', '')) for line in raw_input.splitlines()]

dial = 50

for turn in turns:
    complete_rotations, remaining_turns = divmod(abs(turn), 100)

    solution_two += complete_rotations

    if turn < 0 and dial != 0 and dial - remaining_turns <= 0:
        solution_two += 1

    if turn >= 0 and dial + remaining_turns >= 100:
        solution_two += 1

    dial = (dial + turn) % 100

end_time_two = time()

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
