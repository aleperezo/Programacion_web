<?php
require_once __DIR__ . '/../config/db.php';
//Consultar los registros de la tabla de usuarios

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD ALUMNOS</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <h1>CRUD con PDO (Alumnos)</h1>
    <a href="create.php" class="btn">+Nuevo Alumno</a>
    <h2>Listado </h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Creado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
</body>
</html>