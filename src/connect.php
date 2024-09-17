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
        $boardJson = $_POST['board'];
        $cells = processJsonBoard($boardJson);

        if (checkConnectWinner($cells, 'X')) {
            displayConnectBoard($cells);
            echo "<h2>You won!</h2>";
            if (ob_get_length()) {
                ob_end_flush();
            }
            flush();
            //session_destroy();
            echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
            //exit;
        }

        $aiCol = aiSelectColumn($cells);
        if ($aiCol !== null) {
            dropPiece($cells, $aiCol, 'O');

            if (checkConnectWinner($cells, 'O')) {
                displayConnectBoard($cells);
                echo "<h2>I won!</h2>";
                if (ob_get_length()) {
                    ob_end_flush();
                }
                flush();
                //session_destroy();
                echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
                //exit;
            }
        } else {
            displayConnectBoard($cells);
            echo "<h2>Draw</h2>";
            //session_destroy();
            echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
            exit;
        }

        if (isBoardFull($cells)) {
            displayConnectBoard($cells);
            echo "<h2>Draw</h2>";
            //session_destroy();
            echo '<form action="connect.php" method="post"><button type="submit">Play again</button></form>';
            //exit;
        }
    } else {
        $cells = array_fill(0, 5, array_fill(0, 7, '.'));
    }

    displayConnectBoardWithButtons($cells);
}

function processJsonBoard($boardJson) {
    $cells = json_decode($boardJson, true); 
    if (is_null($cells)) {
        $cells = array_fill(0, 4, array_fill(0, 6, '.'));
    }
    return $cells;
}

function boardToJson($cells) {
    $json = json_encode($cells); 
    //echo '<pre>Board to JSON: ' . htmlspecialchars($json) . '</pre>'; 
    return $json;
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
        for ($col = 0; $col <= $cols - 4; $col++) {
            if ($cells[$row][$col] == $piece &&
                $cells[$row][$col+1] == $piece &&
                $cells[$row][$col+2] == $piece &&
                $cells[$row][$col+3] == $piece) {
                return true;
            }
        }
    }

    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row <= $rows - 4; $row++) {
            if ($cells[$row][$col] == $piece &&
                $cells[$row+1][$col] == $piece &&
                $cells[$row+2][$col] == $piece &&
                $cells[$row+3][$col] == $piece) {
                return true;
            }
        }
    }

    for ($row = 0; $row <= $rows - 4; $row++) {
        for ($col = 0; $col <= $cols - 4; $col++) {
            if ($cells[$row][$col] == $piece &&
                $cells[$row+1][$col+1] == $piece &&
                $cells[$row+2][$col+2] == $piece &&
                $cells[$row+3][$col+3] == $piece) {
                return true;
            }
        }
    }

    for ($row = 3; $row < $rows; $row++) {
        for ($col = 0; $col <= $cols - 4; $col++) {
            if ($cells[$row][$col] == $piece &&
                $cells[$row-1][$col+1] == $piece &&
                $cells[$row-2][$col+2] == $piece &&
                $cells[$row-3][$col+3] == $piece) {
                return true;
            }
        }
    }

    return false;
}

function isBoardFull($cells) {
    foreach ($cells[0] as $cell) {
        if ($cell == '.') {
            return false;
        }
    }
    return true;
}

function displayConnectBoardWithButtons($cells) {
    echo '<form action="connect.php" method="post">';
    echo '<table border="1">';
    echo '<tr>';

    for ($col = 0; $col < 7; $col++) {
        if ($cells[0][$col] == '.') {
            $newCells = $cells;
            dropPiece($newCells, $col, 'X');
            $boardJson = boardToJson($newCells); 

            echo '<td>';
            echo '<button type="submit" name="board" value="'.htmlspecialchars($boardJson).'">Drop</button>';
            echo '</td>';
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
