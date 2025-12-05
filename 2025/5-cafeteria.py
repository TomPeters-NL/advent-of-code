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


# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/5.txt').read_text()
start_time_one = time()
solution_one = 0

split_index = raw_input.splitlines().index('')
fresh_ranges = raw_input.splitlines()[:split_index]
ingredient_list = [int(ingredient) for ingredient in raw_input.splitlines()[split_index + 1:]]

# # # # # # #
# Part  One #
# # # # # # #

for ingredient in ingredient_list:
    for fresh_ingredients in fresh_ranges:
        first, last = [int(fresh_ingredient) for fresh_ingredient in fresh_ingredients.split('-')]

        if ingredient >= first and ingredient <= last:
            solution_one += 1
            break

# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #

sorted_fresh_ranges = sorted([[int(first), int(last)] for first, last in (fresh_range.split('-') for fresh_range in fresh_ranges)])
net_fresh_ranges = [sorted_fresh_ranges.pop(0)]

for first, last in sorted_fresh_ranges:
    net_first, net_last = net_fresh_ranges[-1]

    if first <= net_last:
        net_fresh_ranges[-1] = [net_first, max(last, net_last)]
    else:
        net_fresh_ranges.append([first, last])

for first, last in net_fresh_ranges:
    solution_two += last - first + 1

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
