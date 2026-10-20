<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="enviar.php" method="get">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" placeholder="Nombre" required maxlength="50" minlength="3">
        <br>
        <label for="edad">Edad:</label>
        <input type="number" name="edad" placeholder="Edad" required min="1" max="120">
        <button type="submit">Enviar</button>
    </form>
</body>
</html>