from pathlib import Path
from time import time
from math import prod

# # # # # #
# Methods #
# # # # # #

def get_duration(diff: float) -> str:
    if diff < 1:
        milliseconds = diff * 1000

        return f"{milliseconds:.2f}" + ' ms'
    elif diff > 60:
        minutes = diff // 60
        seconds = diff % 60

        return '{minutes} m ' + f"{seconds:.2f}" + ' s'
    else:
        return f"{diff:.2f}" + ' s'


# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/6.txt').read_text()
start_time_one = time()
solution_one = 0

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

end_time_one = time()
start_time_two = time()
solution_two = 0

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

end_time_two = time()

# # # # # # #
# Solutions #
# # # # # # #

print('Solution #1: ' + str(solution_one))
print('Solution #2: ' + str(solution_two))
print()

# # # # # #
# Timing  #
# # # # # #

duration_total = end_time_two - start_time_one
duration_one = end_time_one - start_time_one
duration_two = end_time_two - start_time_two

print('Time: ' + get_duration(duration_total))
print('Time #1: ' + get_duration(duration_one))
print('Time #2: ' + get_duration(duration_two))
