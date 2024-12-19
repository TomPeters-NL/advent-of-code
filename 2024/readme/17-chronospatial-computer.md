## Introduction.

The Historians have somehow ended up with a 3-bit computer attempting to run a program.
The computer has 3 registers (i.e., A, B, C) and a program.

The program consists of pairs of opcode and operands.
Each opcode triggers one of eight instructions, which tend to use an operand to calculate a new value for one of the registers.

The operand can be used as either a literal or a combo operand.
A literal operand is the number as provided, whereas a combo operand will be either a literal value or the value of one of the registers.

_**The goal is to find the lowest positive value for register A which would cause the program to copy itself.**_

## Reference.
### Combo operands.

| Operand | Value      |
|---------|------------|
| 0       | 0          |
| 1       | 1          |
| 2       | 2          |
| 3       | 3          |
| 4       | Register A |
| 5       | Register B |
| 6       | Register C |
| 7       | None       |

### Operations.

| Opcode | Name | Action                                                     |
|--------|------|------------------------------------------------------------|
| 0      | adv  | `A = A >> combo`                                           |
| 1      | bxl  | `B = B ^ literal`                                          |
| 2      | bst  | `B = combo % 8`                                            |
| 3      | jnz  | `if A is not 0, jump pointer to literal` |
| 4      | bxc  | `B = B ^ C`                                                |
| 5      | out  | `Output: combo % 8`                                        |
| 6      | bdv  | `B = A >> combo`                                           |
| 7      | cdv  | `C = A >> combo`                                           |

### Example.

Assume the program contains these sixteen integers and eight instruction groups consisting of an opcode and operand.

$$$
\large
\begin{gather}
2,4,1,2,7,5,1,3,4,4,5,5,0,3,3,0 \\
(2,4)(1,2)(7,5)(1,3)(4,4)(5,5)(0,3)(3,0)
\end{gather}
$$$

Each of these groups can be converted to pseudocode showing what each instruction entails.

```
(2,4) B = A % 8
(1,2) B = B ^ 2
(7,5) C = A >> B
(1,3) B = B ^ 3
(4,4) B = B ^ C
(5,5) OUTPUT B % 8
(0,3) A = A >> 3
(3,0) if A != 0, jump back to start, else halt the program
```

Reverse engineering the program is most efficiently done from right to left, as the program can only halt when register A contains a zero value during the final instruction.

The value of register A is only modified once (i.e., `A = A >> 3`), establishing that during the final run of the program, register A had to contain a value between 0 and 7.
Any higher values would result in a value of 1 or higher.

$$$
\large
\begin{align}
7&: \quad 111 >> 3 = 0 = 0 \cdot 2^0 &= 0  \\
8&: \quad 1000 >> 3 = 1 = 1 \cdot 2^0 &= 1 \\
20&: \quad 10100 >> 3 = 10 = 1 \cdot 2^1 &= 2
\end{align}
$$$

By using a recursive function and processing each number in the program in reverse, it is possible to efficiently reverse engineer the lowest possible initial value for register A.

At the onset of each run, register A should be set to `A = (p << 3) + i`, where `p` is the result of the previous recursion and `i` is the current loop index.

After the initial program run, each run should start with register A being greater than 7, since any value lower than 8 would result in the program halting.

Once the output setup is reached, this value should be compared to the number being recreated:
  - If they match, start another recursive call using the new value of register A.
  - If they do not match or the recursive chain returns a non-match down the line, continue the loop.
