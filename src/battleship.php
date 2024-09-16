<?php 
session_start();
session_destroy();
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
if (!isset($_GET['name']) && !isset($_SESSION['name'])) {
    echo '
    <form action="battleship.php" method="GET">
        <label for="name">Enter your name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <input type="submit" value="submit">
    </form>
    ';
} else {
    if (!isset($_SESSION['name'])) {
        // no name = game not started yet
        // initialize game state
        $_SESSION['name'] = htmlspecialchars($_GET['name']);
        $_SESSION['num_rows'] = 5;
        $_SESSION['num_cols'] = 7;
        $_SESSION['moves_left'] = ceil($_SESSION['num_rows'] * $_SESSION['num_cols'] * 0.6);
        $_SESSION['board'] = array_fill(0, $_SESSION['num_rows'], array_fill(0, $_SESSION['num_cols'], 0));
        
        $_SESSION['ship_coords'] = [];
    }
    
    $date = date('Y-m-d H:i:s');
    echo "<h1>Battleship</h1>";
    echo "<h2>Hello {$_SESSION['name']}! The date is $date.</h2><br>";

    echo "<p>Number of remaining moves: {$_SESSION['moves_left']}</p><br>";
    
    drawBoard();

    //set the ship locations randomly
    spawnShips();

    $shipCoordsFormatted = array_map(function($coord) {
        return "($coord[0], $coord[1])";
    }, $_SESSION['ship_coords']);
    
    echo "<p>Ship coords: " . implode(', ', $shipCoordsFormatted) . "</p><br>";
    
    //after setting ship locations, now the player moves max 21 times
    // while ($_SESSION['moves_left'] > 0) {

    // }

}

function spawnShips() {
    // Handle the 2x1, 3x1, 4x1 ships
    for ($i = 2; $i < 5; $i++) {
        $col = rand(0, 6);
        $row = rand(0, 4);
        $potential_ship_coords = [];
        
        if (in_array([$row, $col], $_SESSION['ship_coords'])) {
            //restart building current ship
            $i -= 1;
            continue;
        }

        $can_make_ship = false;
        if (rand(0, 1)) { 
            // Vertical building
            if ($row - $i + 1 >= 0) {
                //There is potential space to build *UP*
                $can_make_ship = true;
                //Now check if the spaces are taken
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row - $j, $col], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_make_ship = false;
                        break;
                    }
                }
                if ($can_make_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row - $j, $col]);
                    }
                }
            }
            if (!$can_make_ship && $row + $i - 1 <= 4) {
                //There is potential space to build *DOWN*
                $can_make_ship = true;
                //Now check if the spaces are taken
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row + $j, $col], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_make_ship = false;
                        break;
                    }
                }
                if ($can_make_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row + $j, $col]);
                    }
                }
            }
        } else { 
            // Horizontal building
            if ($col - $i + 1 >= 0) { 
                //There is potential space to build *LEFT*
                $can_make_ship = true;
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row, $col - $j], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_make_ship = false;
                        break;
                    }
                }
                if ($can_make_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row, $col - $j]);
                    }
                }
            }
            if (!$can_make_ship && $col + $i - 1 <= 6) { 
                //There is potential space to build *RIGHT*
                $can_make_ship = true;
                for ($j = 0; $j < $i; $j++) {
                    if (in_array([$row, $col + $j], $_SESSION['ship_coords'])) {
                        //Can't build here
                        $can_make_ship = false;
                        break;
                    }
                }
                if ($can_make_ship) {
                    for ($j = 0; $j < $i; $j++) {
                        array_push($_SESSION['ship_coords'], [$row, $col + $j]);
                    }
                }
            }
        }

        // If no valid position found, retry the ship placement
        if (!$can_make_ship) {
            $i -= 1;
        }
    }
}


function drawBoard() {
    echo '<table>';

    for ($row=0; $row < $_SESSION['num_rows']; $row++) {
        echo '<tr>';
        for ($col=0; $col < $_SESSION['num_cols']; $col++) {
            $cellValue = $_SESSION['board'][$row][$col];
            $displayValue = "?";    // cell value = 0 means not checked yet
            if ($cellValue == 1) { 
                // cell value = 1 means ship was hit
                $displayValue = "X";
            } else if ($cellValue == 2) {
                // cell value = 2 means no ship hit
                $displayValue = "O";
            }
            echo '
                <td>
                <button class="battleship_cell" type="submit" name="move" value="' . $row . ',' . $col . '" onclick="this.disabled=true;">
                    ' . htmlspecialchars($displayValue) . '
                </button>
                </td>
            ';
        }
    }
}

function checkGameOver() {

}

?>
</body>
</html>