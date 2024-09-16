<?php 
session_start();
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
    }
    
    $date = date('Y-m-d H:i:s');
    echo "<h1>Battleship</h1>";
    echo "<h2>Hello {$_SESSION['name']}! The date is $date.</h2><br>";

    echo "<p>Number of remaining moves: {$_SESSION['moves_left']}</p><br>";
    
    drawBoard();

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