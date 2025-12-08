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

        return f"{minutes} m {seconds:.2f} s"
    else:
        return f"{diff:.2f}" + ' s'


# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/0.txt').read_text()
start_time_one = time()
solution_one = 0

# # # # # # #
# Part  One #
# # # # # # #


# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #


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
