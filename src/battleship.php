<?php 
session_start();

function initalizeStates() {
    $_SESSION['game_over'] = false;
    $_SESSION['user_win'] = false;
    $_SESSION['num_rows'] = 5;
    $_SESSION['num_cols'] = 7;
    $_SESSION['moves_left'] = ceil($_SESSION['num_rows'] * $_SESSION['num_cols'] * 0.6);
    $_SESSION['board'] = array_fill(0, $_SESSION['num_rows'], array_fill(0, $_SESSION['num_cols'], 0));
    $_SESSION['ship_coords'] = [];

    //set the ship locations randomly
    spawnShips();
}

function drawBoard() {
    echo "<p>Number of remaining moves: {$_SESSION['moves_left']}</p><br>";

    echo '<form action="battleship.php" method="POST">';
    echo '<table>';

    for ($row=0; $row < $_SESSION['num_rows']; $row++) {
        echo '<tr>';
        for ($col=0; $col < $_SESSION['num_cols']; $col++) {
            $cell_value = $_SESSION['board'][$row][$col];
            $display_value = "?";    // cell value = 0 & 1 means not checked yet
            if ($cell_value == 2) { 
                // cell value = 2 means ship was hit
                $display_value = "X";
            } else if ($cell_value == 3) {
                // cell value = 3 means no ship hit
                $display_value = "O";
            }
            $color = ($display_value === 'X') ? 'red' : 'blue';
            if ($display_value == "?") {
                echo '
                    <td>
                    <button class="battleship_cell" type="submit" name="move" value="' . $row . ',' . $col . '"
                    ' . ($_SESSION['game_over'] ? 'disabled' : '') . '>
                        <strong>?</strong>
                    </button>
                    </td>
                ';
            } else {
                echo '
                    <td>
                    <button class="battleship_cell" type="submit" name="move" value="' . $row . ',' . $col . '" 
                    style="color:' . $color . ';" disabled>
                        ' . htmlspecialchars($display_value) . '
                    </button>
                    </td>
                ';
            }
        }
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</form>';

    if ($_SESSION['game_over']) {
        if ($_SESSION['user_win']) {
            echo "<h3>You Win!</h3>";
        } else {
            echo "<h3>You Lose!</h3>";
        }
        echo '
            <p>Do you want to play again?</p>
            <form action="battleship.php" method="POST"> 
                <button type="submit" name="play_again">Play Again</button>
            </form>
        ';
    }
}

function spawnShips() {
    // Handle the 2x1, 3x1, 4x1 ships
    for ($i = 2; $i < 5; $i++) {
        $col = rand(0, 6);
        $row = rand(0, 4);
        
        if (in_array([$row, $col], $_SESSION['ship_coords'])) {
            //restart building current ship
            $i -= 1;
            continue;
        }

        $can_build_ship = false;
        if (rand(0, 1)) { 
            // Vertical building
            if ($row - $i + 1 >= 0) {
                //There is potential space to build *UP*
                $can_build_ship = true;
                //Now check if the spaces are taken
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row - $j, $col], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_build_ship = false;
                        break;
                    }
                }
                if ($can_build_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row - $j, $col]);
                    }
                }
            }
            if (!$can_build_ship && $row + $i - 1 <= 4) {
                //There is potential space to build *DOWN*
                $can_build_ship = true;
                //Now check if the spaces are taken
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row + $j, $col], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_build_ship = false;
                        break;
                    }
                }
                if ($can_build_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row + $j, $col]);
                    }
                }
            }
        } else { 
            // Horizontal building
            if ($col - $i + 1 >= 0) { 
                //There is potential space to build *LEFT*
                $can_build_ship = true;
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row, $col - $j], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_build_ship = false;
                        break;
                    }
                }
                if ($can_build_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row, $col - $j]);
                    }
                }
            }
            if (!$can_build_ship && $col + $i - 1 <= 6) { 
                //There is potential space to build *RIGHT*
                $can_build_ship = true;
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row, $col + $j], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_build_ship = false;
                        break;
                    }
                }
                if ($can_build_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row, $col + $j]);
                    }
                }
            }
        }

        // If no valid position found, retry the ship placement
        if (!$can_build_ship) {
            $i -= 1;
        }
    }

    foreach ($_SESSION['ship_coords'] as $coord) {
        $_SESSION['board'][$coord[0]][$coord[1]] = 1;
    }
}

function checkGameOver() {
    if (empty($_SESSION['ship_coords'])) {
        // hit all ships
        $_SESSION['game_over'] = true;
        $_SESSION['user_win'] = true;
    }

    else if ($_SESSION['moves_left'] == 0) {
        // no moves left and still have ship coordinates not hit
        $_SESSION['game_over'] = true;
        $_SESSION['user_win'] = false;
    }
}

if (isset($_POST['move'])) {
    $_SESSION['moves_left']--;
    $coordinate = $_POST['move'];
    $x_coord = $coordinate[0];
    $y_coord = $coordinate[2];
    $cell_value = $_SESSION['board'][$x_coord][$y_coord];
    if ($cell_value == 1) {
        // hit ship
        $_SESSION['board'][$x_coord][$y_coord] = 2;

        // remove coordinate from ship coordinates
        foreach ($_SESSION['ship_coords'] as $idx => $coord) {
            if (($coord[0] == $x_coord) && ($coord[1] == $y_coord)) {
                unset($_SESSION['ship_coords'][$idx]);
                break;
            }
        }
    } else if ($cell_value == 0){
        // missed ship
        $_SESSION['board'][$x_coord][$y_coord] = 3;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="battleship.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Battleship </title>
</head>

<body>
<?php 
if (!isset($_POST['name']) && !isset($_SESSION['name'])) {
    echo '
    <form action="battleship.php" method="POST">
        <label for="name">Enter your name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <input type="submit" value="submit">
    </form>
    ';
} else {
    if (!isset($_SESSION['name'])) {
        // no name = first game not started yet
        $_SESSION['name'] = htmlspecialchars($_POST['name']);
        initalizeStates();
    }

    if (isset($_POST['play_again'])) {
        initalizeStates();
    }

    $date = date('Y-m-d H:i:s');
    echo "<h1>Battleship</h1>";
    echo "<h2>Hello {$_SESSION['name']}! The date is $date.</h2><br>";

    checkGameOver();

    drawBoard();
}

?>
</body>
</html>