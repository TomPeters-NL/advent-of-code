from pathlib import Path
from time import time
from re import finditer


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

raw_input = Path(__file__).resolve().parent.joinpath('input/7.txt').read_text()
start_time_one = time()
solution_one = 0

# # # # # # #
# Part  One #
# # # # # # #

rows = raw_input.splitlines()
row_count = len(rows)

splitters = {row_number: [] for row_number in range(row_count)}
for s_y, row in splitters.items():
    splitters[s_y] = row + [match.start() for match in finditer(r'\^', rows[s_y])]

beams = [(rows[0].index('S'), 0)]
used_splitters = []

while len(beams) > 0:
    new_beams = []

    for (b_x, b_y) in beams:
        for s_y, s_columns in splitters.items():
            if s_y > b_y and b_x in s_columns:
                splitter_location = (b_x, s_y)
                left_beam = (b_x - 1, s_y)
                right_beam = (b_x + 1, s_y)

                if left_beam not in new_beams:
                    new_beams.append(left_beam)

                if right_beam not in new_beams:
                    new_beams.append(right_beam)

                if splitter_location not in used_splitters:
                    used_splitters.append(splitter_location)

                break

    beams = new_beams

solution_one = len(used_splitters)

# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #

rows = raw_input.splitlines()
row_count = len(rows)

splitters = {row_number: [] for row_number in range(row_count)}
for s_y, row in splitters.items():
    splitters[s_y] = row + [match.start() for match in finditer(r'\^', rows[s_y])]

timeline_map = {(rows[0].index('S'), 0): 1}

while len(timeline_map) > 0:
    new_timelines = {}

    for (b_x, b_y), timeline_count in timeline_map.items():
        has_split = False

        for s_y, s_columns in splitters.items():
            if s_y > b_y and b_x in s_columns:
                left_timeline = (b_x - 1, s_y)
                right_timeline = (b_x + 1, s_y)

                new_timelines[left_timeline] = new_timelines.get(left_timeline, 0) + timeline_count
                new_timelines[right_timeline] = new_timelines.get(right_timeline, 0) + timeline_count

                has_split = True
                break

        if not has_split:
            solution_two += timeline_count
        
    timeline_map = new_timelines

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
