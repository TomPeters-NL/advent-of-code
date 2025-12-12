from pathlib import Path
from time import time
from collections import deque

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

def generate_matrix(buttons: list, joltage_diagram: list) -> list:
    matrix = []
    for index, joltage in enumerate(joltage_diagram):
        matrix_row = []
        for button in buttons:
            matrix_row.append(0) if index not in button else matrix_row.append(1)
        matrix_row.append(joltage)
        matrix.append(matrix_row)

    return matrix


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

manual = [machine.split() for machine in raw_input.splitlines()]
machines = []

for entry in manual:
    indicator_light_diagram = entry[0]
    button_wiring = entry[1:-1]

    machines.append({
        'lights': [False] * len(indicator_light_diagram.strip('[]')),
        'light_diagram': [light == '#' for light in list(indicator_light_diagram.strip('[]'))],
        'buttons': [[int(wiring) for wiring in list(button.strip('()').split(','))] for button in button_wiring],
    })

initialization_log = []

for machine in machines:
    initial_lights = machine['lights'].copy()
    iterations = [initial_lights]
    queue = deque([(initial_lights, [])])

    while queue:
        current_lights, buttons_pressed = queue.popleft()

        if current_lights == machine['light_diagram']:
            initialization_log.append(len(buttons_pressed))
            break

        for next_button in machine['buttons']:
            new_lights = current_lights.copy()

            for light_index in next_button:
                new_lights[light_index] = not new_lights[light_index]

            if new_lights not in iterations:
                iterations.append(new_lights)
                queue.append((new_lights, buttons_pressed + [next_button]))

solution_one = sum(initialization_log)

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

manual = [machine.split() for machine in raw_input.splitlines()]
machines = []

for entry in manual:
    button_wiring = entry[1:-1]
    joltage_requirements = entry[-1]

    machines.append({
        'buttons': [[int(wiring) for wiring in list(button.strip('()').split(','))] for button in button_wiring],
        'joltage_diagram': [int(joltage) for joltage in joltage_requirements.strip('{}').split(',')],
    })

for machine in machines:
    button_presses = [0] * len(machine['buttons'])
    matrix = generate_matrix(machine['buttons'], machine['joltage_diagram'])
    solution_two = 'I tried so hard, and got so far. But in the end, I gave up.'

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
