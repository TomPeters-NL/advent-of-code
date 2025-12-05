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

raw_input = Path(__file__).resolve().parent.joinpath('input/2.txt').read_text()
start_time_one = time()
solution_one = 0

# # # # # # #
# Part  One #
# # # # # # #

product_ranges = raw_input.strip().split(',')
product_ids = []

for product_range in product_ranges:
    lower, upper = map(int, product_range.split('-'))
    product_ids += range(lower, upper + 1)

for product_id in product_ids:
    string_id = str(product_id)
    id_length = len(string_id)

    if id_length % 2 != 0:
        continue

    middle_index = id_length // 2

    if string_id[middle_index:] == string_id[:middle_index]:
        solution_one += product_id

# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #

product_ranges = raw_input.strip().split(',')
product_ids = []

for product_range in product_ranges:
    lower, upper = map(int, product_range.split('-'))
    product_ids += range(lower, upper + 1)

for product_id in product_ids:
    string_id = str(product_id)

    if (string_id + string_id).find(string_id, 1, -1) != -1:
        solution_two += product_id

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
