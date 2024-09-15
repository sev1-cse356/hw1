<?php
header('X-CSE356: 66d0f3556424d34b6b77c48f'); 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connect-4</title>
</head>
<body>
<?php
if (!isset($_POST['name']) && !isset($_SESSION['name'])) {
    echo '<form action="connect.php" method="post">';
    echo 'Name: <input type="text" name="name">';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
    exit;
} else {
    if (isset($_POST['name'])) {
        $_SESSION['name'] = htmlspecialchars($_POST['name']);
    }
    $name = $_SESSION['name'];
    $date = date('Y-m-d H:i:s');
    echo "<p>Hello $name, $date</p>";

    if (isset($_POST['board'])) {
        $board = $_POST['board'];
        $cells = processBoard($board);
    } else {
        $cells = array_fill(0, 5, array_fill(0, 7, '.'));
    }

    if (isset($_POST['column'])) {
        $col = intval($_POST['column']);
        dropPiece($cells, $col, 'X');

        if (checkConnectWinner($cells, 'X')) {
            echo "<p>You won!</p>";
            session_destroy();
            echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
            displayConnectBoard($cells);
            exit;
        }

        $aiCol = aiSelectColumn($cells);
        if ($aiCol !== null) {
            dropPiece($cells, $aiCol, 'O');

            if (checkConnectWinner($cells, 'O')) {
                echo "<p>I won!</p>";
                session_destroy();
                echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
                displayConnectBoard($cells);
                exit;
            }
        } else {
            echo "<p>Draw</p>";
            session_destroy();
            echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
            displayConnectBoard($cells);
            exit;
        }
    }

    displayConnectBoardWithButtons($cells);
}

function processBoard($boardStr) {
    return json_decode($boardStr, true);
}

function boardToString($cells) {
    return json_encode($cells);
}

function dropPiece(&$cells, $col, $piece) {
    for ($row = count($cells) - 1; $row >= 0; $row--) {
        if ($cells[$row][$col] == '.') {
            $cells[$row][$col] = $piece;
            return true;
        }
    }
    return false;
}

function aiSelectColumn($cells) {
    for ($col = 0; $col < 7; $col++) {
        if ($cells[0][$col] == '.') {
            return $col;
        }
    }
    return null;
}

function checkConnectWinner($cells, $piece) {
    $rows = count($cells);
    $cols = count($cells[0]);

    for ($row = 0; $row < $rows; $row++) {
        for ($col = 0; $col < $cols - 3; $col++) {
            if ($cells[$row][$col] == $piece && $cells[$row][$col+1] == $piece &&
                $cells[$row][$col+2] == $piece && $cells[$row][$col+3] == $piece) {
                return true;
            }
        }
    }

    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row < $rows - 3; $row++) {
            if ($cells[$row][$col] == $piece && $cells[$row+1][$col] == $piece &&
                $cells[$row+2][$col] == $piece && $cells[$row+3][$col] == $piece) {
                return true;
            }
        }
    }

    for ($row = 0; $row < $rows - 3; $row++) {
        for ($col = 0; $col < $cols - 3; $col++) {
            if ($cells[$row][$col] == $piece && $cells[$row+1][$col+1] == $piece &&
                $cells[$row+2][$col+2] == $piece && $cells[$row+3][$col+3] == $piece) {
                return true;
            }
        }
    }
    for ($row = 3; $row < $rows; $row++) {
        for ($col = 0; $col < $cols - 3; $col++) {
            if ($cells[$row][$col] == $piece && $cells[$row-1][$col+1] == $piece &&
                $cells[$row-2][$col+2] == $piece && $cells[$row-3][$col+3] == $piece) {
                return true;
            }
        }
    }

    return false;
}

function displayConnectBoardWithButtons($cells) {
    $boardStr = boardToString($cells);
    echo '<form action="connect.php" method="post">';
    echo '<input type="hidden" name="board" value="'.htmlspecialchars($boardStr).'">';
    echo '<table border="1">';
    echo '<tr>';
    for ($col = 0; $col < 7; $col++) {
        if ($cells[0][$col] == '.') {
            echo '<td><button type="submit" name="column" value="'.$col.'">Drop</button></td>';
        } else {
            echo '<td></td>';
        }
    }
    echo '</tr>';
    foreach ($cells as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td style="width:30px; height:30px; text-align:center;">'.$cell.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</form>';
}

function displayConnectBoard($cells) {
    echo '<table border="1">';
    foreach ($cells as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td style="width:30px; height:30px; text-align:center;">'.$cell.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}
?>
</body>
</html>
