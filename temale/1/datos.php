<?php
$nombre = $_GET['nombre'];
$edad = $_GET['edad'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body {
        color: red;
        background-color: chartreuse;
        text-align: center;
    }
</style>
<body>

    <h1>Nombre: <?php echo $nombre; ?></h1>
    <h1>Edad: <?php echo $edad; ?></h1>

    <a href="index.html">Inicio</a>
</body>
</html>