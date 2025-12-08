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


def get_neighbour_sum(grid: list, x: int, y: int) -> int:
    neighbour_sum = 0

    for d_x in [-1, 0, 1]:
        if x == 0 and d_x == -1 or x == len(grid[0]) - 1 and d_x == 1: continue

        for d_y in [-1, 0, 1]:
            if y == 0 and d_y == -1 or y == len(grid[0]) - 1 and d_y == 1 or d_x == 0 and d_y == 0: continue

            neighbour_sum += grid[y + d_y][x + d_x]

    return neighbour_sum


# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/4.txt').read_text()
start_time_one = time()
solution_one = 0

# # # # # # #
# Part  One #
# # # # # # #

paper_map = [[int(field) for field in list(row)] for row in raw_input.replace('.', '0').replace('@', '1').splitlines()]

for x, row in enumerate(paper_map):
    for y, paper_roll in enumerate(row):
        if paper_map[y][x] == 0: continue
        if get_neighbour_sum(paper_map, x, y) < 4: solution_one += 1

# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #

paper_map = [[int(field) for field in list(row)] for row in raw_input.replace('.', '0').replace('@', '1').splitlines()]
removable_rows = []

while True:
    while len(removable_rows) > 0:
        r_x, r_y = removable_rows.pop()
        paper_map[r_y][r_x] = 0

    for x, row in enumerate(paper_map):
        for y, paper_roll in enumerate(row):
            if paper_map[y][x] == 0: continue
            neighbours = get_neighbour_sum(paper_map, x, y)

            if neighbours < 4:
                removable_rows.append((x, y))
                solution_two += 1

    if len(removable_rows) == 0:
        break

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
