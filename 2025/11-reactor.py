from pathlib import Path
from time import time
from collections import deque
from functools import cache

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

def count_paths(devices: dict, start_device: str, target_device: str) -> int:
    @cache
    def recursive_count(current_device) -> int:
        if current_device == target_device:
            return 1
        
        if current_device not in devices:
            return 0
        
        total_paths = 0

        for next_device in devices[current_device]:
            total_paths += recursive_count(next_device)

        return total_paths

    return recursive_count(start_device)

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/11.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

devices = {input: output.strip().split() for input, output in [device.split(':') for device in raw_input.splitlines()]}

solution_one += count_paths(devices, 'you', 'out')

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

devices = {input: output.strip().split() for input, output in [device.split(':') for device in raw_input.splitlines()]}

solution_two += count_paths(devices, 'svr', 'fft') * count_paths(devices, 'fft', 'dac') * count_paths(devices, 'dac', 'out') 
solution_two += count_paths(devices, 'svr', 'dac') * count_paths(devices, 'dac', 'fft') * count_paths(devices, 'fft', 'out') 

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
