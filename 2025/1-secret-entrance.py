from time import time
from pathlib import Path

# # # # # #
# Methods #
# # # # # #

def get_duration (diff: float) -> str:
    if diff < 1:
        milliseconds = diff * 1000
        
        return f"{milliseconds:.2f}" + ' ms'
    elif diff > 60:
        minutes = diff // 60
        seconds = diff % 60
        
        return '{minutes} m ' + f"{seconds:.2f}" + ' s'
    else:
        return f"{diff:.2f}" + ' s'

# # # # # # # # # #
# Initialization  #
# # # # # # # # # #

input_path = Path(__file__).resolve().parent.joinpath('input/1.txt')
input = input_path.read_text()
start_time = time()

# # # # # # #
# Part  One #
# # # # # # #

solution_one = 0
safe_value = 50

input_one = input.replace('L', '-').replace('R', '+')
operations = input_one.splitlines()

for operation in operations:
    safe_value = eval(str(safe_value) + operation) % 100
    if safe_value == 0: solution_one += 1

end_time_one = time()

# # # # # # #
# Part  Two #
# # # # # # #

start_time_two = time()

solution_two = 0
safe_value = 50

input_two = input.replace('L', '-').replace('R', '+')
operations = input_two.splitlines()

for operation in operations:
    raw_value = eval(str(safe_value) + operation)
    solution_two += abs(raw_value // 100)
    safe_value = raw_value % 100

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

duration_total = end_time_two - start_time
duration_one = end_time_one - start_time
duration_two = end_time_two - start_time_two

print('Time: ' + get_duration(duration_total))
print('Time #1: ' + get_duration(duration_one))
print('Time #2: ' + get_duration(duration_two))