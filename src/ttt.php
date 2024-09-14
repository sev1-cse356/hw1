<?php


$name = $_GET["name"];
$winner = '';


if (empty($_GET["board"])) {
    $board = ['', '', '', '', '', '', '', '', ''];
} else {
    $board = explode(' ', $_GET["board"]);
}


$count = countNonEmptyStrings($board);

if ($count % 2 == 0) {
    $currentPlayer = 'X';
} else {
    $currentPlayer = 'O';
}

if ($currentPlayer == 'O' && $count < 9 && $winner == '') {
    // : make automatic O move
    foreach (array_values($board) as $i => $val) {
        if ($val == '') {
            $redirectURL = sprintf("ttt.php?name=%s&board=%s", urlencode($name), urlencode(implode(' ', updateBoard($board, $i, $currentPlayer))));
            header(sprintf('Location:%s', $redirectURL), true, 303, );
            break;
        }
    }
}


function countNonEmptyStrings($array)
{
    $count = 0;

    // Loop through each element in the array
    foreach ($array as $element) {
        // Check if the element is a string and not empty
        if (is_string($element) && trim($element) !== '') {
            $count++;
        }
    }

    return $count;
}

// Function to check for a winner
function checkWinner($board)
{
    $winningCombinations = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8], // Rows
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8], // Columns
        [0, 4, 8],
        [2, 4, 6]             // Diagonals
    ];

    foreach ($winningCombinations as $combination) {
        if (
            $board[$combination[0]] !== '' &&
            $board[$combination[0]] === $board[$combination[1]] &&
            $board[$combination[1]] === $board[$combination[2]]
        ) {
            return $board[$combination[0]];
        }
    }
    return null;
}

$winner = checkWinner($board);

function updateBoard($board, $index, $currentPlayer)
{
    // Update the board at the specified index with the player's move
    $board[$index] = $currentPlayer;

    // Return the updated board
    return $board;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="ttt.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe</title>
    <style>

    </style>
</head>

<body>
    <h1>Tic-Tac-Toe</h1>
    <?php if (isset($_GET['name'])) {
        $name = htmlspecialchars($_GET['name']);
        $date = date('Y-m-d H:i:s');
        echo "<h1>Hello $name, $date</h1>";
    } else {
        // Display form to enter the name
        echo '<form action="ttt.php" method="GET">
    <label for="name">Enter your name:</label>
    <input type="text" id="name" name="name" required>
    <input type="submit" value="Start Game">
</form>';
    }
    ?>
    <div class="board <?= empty($name) ? 'hidden' : '' ?>">
        <?php for ($i = 0; $i < 9; $i++): ?>
            <a class="<?= $board[$i] !== '' ? 'disabled' : '' ?>"
                href="ttt.php?name=<?= urlencode($name) ?>&board=<?= urlencode(implode(' ', updateBoard($board, $i, $currentPlayer))) ?>">
                <div class="cell">
                    <?php if ($board[$i] !== ''): ?>
                        <?= $board[$i] ?>
                    <?php else: ?>

                    <?php endif; ?>
                </div>
            </a>
        <?php endfor; ?>
        <?php
        $redirect = sprintf('<br><a href="ttt.php?name=%s">Play Again</a>', urlencode($name));
        if ($winner == "TIE") {
            echo 'WINNER: NONE. A STRANGE GAME. THE ONLY WINNING MOVE IS NOT TO PLAY.';
        } elseif ($winner == 'X') {
            echo 'You won!';
            echo $redirect;
        } elseif ($winner == 'O') {
            echo 'I won!';
            echo $redirect;
        }

        ?>
    </div>

</body>

</html>