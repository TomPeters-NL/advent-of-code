from functools import cache
from itertools import combinations
from pathlib import Path
from time import time
from collections import defaultdict

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

def create_machine(machine_manual: list[str]) -> dict:
    return {
        'buttons': [set(map(int, raw_button.strip('()').split(','))) for raw_button in machine_manual[1:-1]],
        'joltage_requirements': list(map(int, machine_manual[-1].strip('{}').split(','))),
        'light_requirements': {index for index, symbol in enumerate(list(machine_manual[0].strip('[]'))) if symbol == '#'},
    }

def map_light_patterns(buttons: list[set]) -> dict[list[int], list[tuple[int]]]:
    light_patterns = defaultdict(list)

    for button_count in range(len(buttons) + 1):
        for button_presses in combinations(buttons, button_count):
            lights = set()

            for button in button_presses:
                lights ^= button

            light_patterns[frozenset(lights)].append(button_presses)
    
    return light_patterns

def configure_lights(light_requirements: set[int], light_patterns: dict[list[int], list[tuple[int]]]) -> int:
    button_combinations = light_patterns[frozenset(light_requirements)]

    return min([len(buttons) for buttons in button_combinations], default=0)

#
# Thanks to /u/tenthmascot and Josian Winslow for finding and explaining this non-algebraic solution.
# https://reddit.com/comments/1pk87hl
# https://aoc.winslowjosiah.com/solutions/2025/day/10/
#
# In short: 
# An even amount of button presses returns a light to its initial state and an odd amount of button presses turns it on.
# This means every joltage configuration can be abstracted to a light configuration, which can be solved without complicated Gauss-Jordan eliminations.
# After turning on the lights for the odd joltages, the remaining joltage configures turns completely even, and can thus be halved.
# [8, 5, 7, 20] -> [.##.] -> [8, 4, 6, 20] -> [4, 2, 3, 10] -> [..#.] -> [4, 2, 2, 10] -> [2, 1, 1, 5] -> etc.
# Note: Remember to multiply the cumulative result of the divisions and subsequent button presses by the division factor (2 here).
#
def configure_joltages(joltage_requirements: list[int], light_patterns: dict[list[int], list[tuple[int]]]) -> int | None:
    @cache
    def get_minimum_button_presses(joltage_target: tuple[int]) -> int | None:
        if not any(joltage_target):
            return 0
        
        minimum_presses = None
        lights = frozenset(index for index, level in enumerate(joltage_target) if level % 2 != 0)

        for button_combination in light_patterns[lights]:
            remaining_joltage = list(joltage_target)

            for button in button_combination:
                for joltage_index in button:
                    remaining_joltage[joltage_index] -= 1

            if any(joltage < 0 for joltage in remaining_joltage):
                continue

            half_joltage_target = tuple(joltage / 2 for joltage in remaining_joltage)
            half_minimum_presses = get_minimum_button_presses(half_joltage_target)

            if half_minimum_presses == None:
                continue

            total_presses = len(button_combination) + 2 * half_minimum_presses

            if minimum_presses == None:
                minimum_presses = total_presses
            else:
                minimum_presses = min(minimum_presses, total_presses)

        return minimum_presses
            
    return get_minimum_button_presses(tuple(joltage_requirements))


# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/10.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

machines = [create_machine(machine_manual.split()) for machine_manual in raw_input.splitlines()]

for machine in machines:
    light_patterns = map_light_patterns(machine['buttons'])
    solution_one += configure_lights(machine['light_requirements'], light_patterns)

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

machines = [create_machine(machine_manual.split()) for machine_manual in raw_input.splitlines()]

for machine in machines:
    light_patterns = map_light_patterns(machine['buttons'])
    solution_two += configure_joltages(machine['joltage_requirements'], light_patterns)

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
