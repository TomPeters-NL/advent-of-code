## Legend
| Variable | Explanation                                         |
|----------|-----------------------------------------------------|
| $α$      | The amount of presses of button A.                  |
| $a_x$    | The horizontal (X) movement when pressing button A. |
| $a_y$    | The vertical (Y) movement when pressing button A.   |
| $ß$      | The amount of presses of button B.                  |
| $b_x$    | The horizontal (X) movement when pressing button B. |
| $b_y$    | The vertical (Y) movement when pressing button B.   |
| $p_x$    | The horizontal (X) coordinate of the prize.         |
| $p_y$    | The vertical (Y) coordinate of the prize.           |

## Mathematics
The basic formulas for calculating the claw machine movements towards the prize are:

$$
\large
\begin{align}
α \cdot a_x + ß \cdot b_x &= p_x \\
α \cdot a_y + ß \cdot b_y &= p_y
\end{align}
$$

The goal is to discover the values for $α$ and $ß$.
This requires two sets of formulas, each eliminating one of these variables in order to calculate the other.
This can be done by "equalizing" one side of the equation by cross-multiplying the $a_{xy}$ and $b_{xy}$ values.

$$
\large
\begin{align}
α \cdot a_x \cdot b_y + ß \cdot b_x \cdot b_y &= p_x \cdot b_y \\
α \cdot a_y \cdot b_x + ß \cdot b_y \cdot b_x &= p_y \cdot b_x \\\\
α \cdot a_x \cdot a_y + ß \cdot b_x \cdot a_y &= p_x \cdot a_y \\
α \cdot a_y \cdot a_x + ß \cdot b_y \cdot a_x &= p_y \cdot a_x
\end{align}
$$

Now the $ß$ and $α$ sides of each pair of equations are equal, respectively.
Each pair of equations can now be combined into a single formula by subtraction. 

$$
\large
\begin{align}
α \cdot a_x \cdot b_y - α \cdot a_y \cdot b_x + ß \cdot b_x \cdot b_y - ß \cdot b_y \cdot b_x &= p_x \cdot b_y - p_y \cdot b_x \\
α \cdot a_x \cdot a_y - α \cdot a_y \cdot a_x + ß \cdot b_x \cdot a_y - ß \cdot b_y \cdot a_x &= p_x \cdot a_y - p_y \cdot a_x
\end{align}
$$

Since the $ß$ and $α$ sides are equal, the equations can be simplified as follows.

$$
\large
\begin{align}
α \cdot a_x \cdot b_y - α \cdot a_y \cdot b_x &= p_x \cdot b_y - p_y \cdot b_x \\
ß \cdot b_x \cdot a_y - ß \cdot b_y \cdot a_x &= p_x \cdot a_y - p_y \cdot a_x
\end{align}
$$

All that remains is to solve for $α$ and $ß$, which can be achieved by these final manipulations.

$$
\large
\begin{align}
α \cdot (a_x \cdot b_y - \cdot a_y \cdot b_x) &= p_x \cdot b_y - p_y \cdot b_x \\
ß \cdot (b_x \cdot a_y - \cdot b_y \cdot a_x) &= p_x \cdot a_y - p_y \cdot a_x
\end{align}
$$

$$
\large
\begin{align}
α &= \frac{p_x \cdot b_y - p_y \cdot b_x}{a_x \cdot b_y - \cdot a_y \cdot b_x} \\
ß &= \frac{p_x \cdot a_y - p_y \cdot a_x}{b_x \cdot a_y - \cdot b_y \cdot a_x}
\end{align}
$$