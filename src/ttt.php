<html>
<body>

<form action="ttt.php" method="POST">
Name: <input type="text" name="name"><br>
<input type="submit">
</form>

Hello <?php echo $_POST["name"]; ?>, <?php echo date("Y/m/d"); ?>

</body>
</html>