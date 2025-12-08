from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/3.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

for bank in raw_input.splitlines():
    batteries = list(bank)

    joltage = ''
    battery_offset = 2

    while len(joltage) < 2:
        battery_offset -= 1

        digit = max(batteries[:-battery_offset]) if battery_offset > 0 else max(batteries)
        joltage += digit

        batteries = batteries[batteries.index(digit) + 1:]

    solution_one += int(joltage)

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

for bank in raw_input.splitlines():
    batteries = list(bank)

    joltage = ''
    battery_offset = 12

    while len(joltage) < 12:
        battery_offset -= 1

        digit = max(batteries[:-battery_offset]) if battery_offset > 0 else max(batteries)
        joltage += digit

        batteries = batteries[batteries.index(digit) + 1:]

    solution_two += int(joltage)

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
