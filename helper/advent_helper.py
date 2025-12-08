from time import time

def print_solutions(solution_one: int | str, solution_two: int | str, start_time_one: float, start_time_two: float) -> None:
    now = time()
    duration_total = now - start_time_one
    duration_one = start_time_two - start_time_one
    duration_two = now - start_time_two

    print(f"Solution #1: {str(solution_one)}")
    print(f"Solution #2: {str(solution_two)}")
    print()
    print(f"Time: {get_duration(duration_total)}")
    print(f"Time #1: {get_duration(duration_one)}")
    print(f"Time #2: {get_duration(duration_two)}")

def get_duration(diff: float) -> str:
    if diff < 1:
        return f"{diff * 1000:.2f}" + ' ms'
    elif diff > 60:
        return f"{diff // 60} m {diff % 60:.2f} s"
    else:
        return f"{diff:.2f}" + ' s'