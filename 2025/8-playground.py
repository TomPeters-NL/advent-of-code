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

def get_distance(junction_box_a: tuple, junction_box_b: tuple) -> float:
    (a_x, a_y, a_z) = junction_box_a
    (b_x, b_y, b_z) = junction_box_b

    return ((a_x - b_x) ** 2 + (a_y - b_y) ** 2 + (a_z - b_z) ** 2) ** 0.5

# # # # # # # # #
# Introduction  #
# # # # # # # # #

raw_input = Path(__file__).resolve().parent.joinpath('input/8.txt').read_text()
start_time_one = time()
solution_one = 1

# # # # # # #
# Part  One #
# # # # # # #

# 10 for the sample, 1000 for the puzzle.
connection_target = 1000

junction_boxes = [(int(x), int(y), int(z)) for [x, y, z] in [junction_box.split(',') for junction_box in raw_input.splitlines()]]
distances = {}
connected_junction_boxes = []

circuits = {}
circuit_id = 1

# Determine the distances between all pairs of junction boxes.
for jb_a in junction_boxes:
    for jb_b in junction_boxes:
        # Don't calculate distance between a junction box and itself, and ignore the reverse of existing combinations.
        if jb_a == jb_b or (jb_b, jb_a) in distances:
            continue

        distances[(jb_a, jb_b)] = get_distance(jb_a, jb_b)

# For efficiency, reverse (for popping items) sort the distances.
distances = dict(sorted(distances.items(), key=lambda distance: distance[1], reverse=True))

for connection in range(connection_target):
    ((jb_a, jb_b), distance) = distances.popitem()

    # The junction boxes are already on the same circuit.
    if jb_a in connected_junction_boxes and jb_b in connected_junction_boxes and circuits[jb_a] == circuits[jb_b]:
        continue

    # The junction boxes are powered, but on separate circuits. Consolidate the circuits.
    if jb_a in connected_junction_boxes and jb_b in connected_junction_boxes:
        new_circuit = circuits[jb_a]
        old_circuit = circuits[jb_b]

        for jb_x, current_circuit in circuits.items():
            if current_circuit == old_circuit:
                circuits.update({jb_x: new_circuit})

        continue

    # Connect the second junction box to the first's circuit.
    if jb_a in connected_junction_boxes:
        circuits[jb_b] = circuits[jb_a]
        connected_junction_boxes.append(jb_b)
        continue

    # Connect the first junction box to the second's circuit.
    if jb_b in connected_junction_boxes:
        circuits[jb_a] = circuits[jb_b]
        connected_junction_boxes.append(jb_a)
        continue

    # Connect both junction boxes to create a new circuit.
    circuits[jb_a] = circuit_id
    circuits[jb_b] = circuit_id
    connected_junction_boxes.append(jb_b)
    connected_junction_boxes.append(jb_a)
    circuit_id += 1

# Determine circuit sizes.
circuit_sizes = {}
for junction_box, circuit in circuits.items():
    circuit_sizes[circuit] = circuit_sizes.get(circuit, 0) + 1

# Multiply the sizes of the three largest circuits.
for i in range(3):
    size = max(circuit_sizes, key=circuit_sizes.get)
    solution_one *= circuit_sizes.pop(size)

# # # # # # #
# Interlude #
# # # # # # #

end_time_one = time()
start_time_two = time()
solution_two = 0

# # # # # # #
# Part  Two #
# # # # # # #

junction_boxes = [(int(x), int(y), int(z)) for [x, y, z] in [junction_box.split(',') for junction_box in raw_input.splitlines()]]
connected_junction_boxes = []
current_junction_boxes = ()

circuits = {}
circuit_id = 1
circuit_count = 0

# Determine the distances between all pairs of junction boxes.
distances = {}
for jb_a in junction_boxes:
    for jb_b in junction_boxes:
        # Don't calculate distance between a junction box and itself, and ignore the reverse of existing combinations.
        if jb_a == jb_b or (jb_b, jb_a) in distances:
            continue

        distances[(jb_a, jb_b)] = get_distance(jb_a, jb_b)

# For efficiency, reverse (for popping items) sort the distances.
distances = dict(sorted(distances.items(), key=lambda distance: distance[1], reverse=True))

for connection in range(len(distances)):
    # Stop the loop when all junction boxes are connected and part of a single circuit.
    if len(connected_junction_boxes) == len(junction_boxes) and circuit_count == 1:
        ((a_x, a_y, a_z), (b_x, b_y, b_z)) = current_junction_boxes
        solution_two = a_x * b_x
        break

    ((jb_a, jb_b), distance) = distances.popitem()
    current_junction_boxes = (jb_a, jb_b)

    # The junction boxes are already on the same circuit.
    if jb_a in connected_junction_boxes and jb_b in connected_junction_boxes and circuits[jb_a] == circuits[jb_b]:
        continue

    # The junction boxes are powered, but on separate circuits. Consolidate the circuits.
    if jb_a in connected_junction_boxes and jb_b in connected_junction_boxes:
        circuit_count -= 1
        new_circuit = circuits[jb_a]
        old_circuit = circuits[jb_b]
        for jb_x, current_circuit in circuits.items():
            if current_circuit == old_circuit:
                circuits.update({jb_x: new_circuit})
        continue

    # Connect the second junction box to the first's circuit.
    if jb_a in connected_junction_boxes:
        circuits[jb_b] = circuits[jb_a]
        connected_junction_boxes.append(jb_b)
        continue

    # Connect the first junction box to the second's circuit.
    if jb_b in connected_junction_boxes:
        circuits[jb_a] = circuits[jb_b]
        connected_junction_boxes.append(jb_a)
        continue

    # Connect both junction boxes to create a new circuit.
    if jb_a not in connected_junction_boxes and jb_b not in connected_junction_boxes:
        circuit_count += 1
        circuits[jb_a] = circuit_id
        circuits[jb_b] = circuit_id
        connected_junction_boxes.append(jb_b)
        connected_junction_boxes.append(jb_a)
        circuit_id += 1
        continue

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
