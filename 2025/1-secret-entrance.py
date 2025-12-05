from pathlib import Path
from time import time


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


# # # # # # # # # #
# Initialization  #
# # # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/1.txt').read_text()
start_time = time()

# # # # # # #
# Part  One #
# # # # # # #

turns = [int(line.strip().replace('L', '-').replace('R', '')) for line in raw_input.splitlines()]

solution_one = 0
dial = 50

for turn in turns:
    dial = (dial + turn) % 100
    if dial == 0: solution_one += 1

end_time_one = time()

# # # # # # #
# Part  Two #
# # # # # # #

start_time_two = time()

input_two = raw_input
turns = [int(line.strip().replace('L', '-').replace('R', '')) for line in raw_input.splitlines()]

solution_two = 0
dial = 50

for turn in turns:  # 6689?
    complete_rotations, remaining_turns = divmod(abs(turn), 100)

    solution_two += complete_rotations

    if turn < 0 and dial != 0 and dial - remaining_turns <= 0:
        solution_two += 1

    if turn >= 0 and dial + remaining_turns >= 100:
        solution_two += 1

    dial = (dial + turn) % 100

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
