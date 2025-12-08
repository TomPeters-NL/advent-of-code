from math import prod
from pathlib import Path
from time import time

from helper.advent_helper import print_solutions

# # # # # #
# Methods #
# # # # # #

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/6.txt').read_text()
start_time_one = time()
solution_one = 0
solution_two = 0

# # # # # # #
# Part  One #
# # # # # # #

worksheet = raw_input.splitlines()
operators = worksheet.pop().split()
problems = [[int(value) for value in values] for values in [row.split() for row in worksheet]]
problem_count = len(problems[0])

for index in range(problem_count):
    operator = operators[index]

    result = 1 if operator == '*' else 0

    for problem in problems:
        if operator == '*':
            result *= problem[index]
        else:
            result += problem[index]

    solution_one += result

# # # # # # #
# Interlude #
# # # # # # #

start_time_two = time()

# # # # # # #
# Part  Two #
# # # # # # #

worksheet = raw_input.splitlines()
operators = worksheet.pop().split()

rows = range(len(worksheet))
columns = range(len(worksheet[0]))

problem_index = 0
problems = {}

for column in columns:
    if not problem_index in problems:
        problems[problem_index] = []

    value = ''

    for row in rows:
        value += worksheet[row][column]

    value = value.strip()
    if value == '':
        problem_index += 1
    else:
        problems[problem_index].append(value)

for index, problem in problems.items():
    operator = operators[index]

    if operator == '*':
        solution_two += prod(map(int, problem))
    else:
        solution_two += sum(map(int, problem))

# # # # # # #
# Epilogue  #
# # # # # # #

print_solutions(solution_one, solution_two, start_time_one, start_time_two)
