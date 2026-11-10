<?php
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "escuela";

$conexion = new mysqli($host, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

//INSERT 
if (!empty($nombre) && !empty($correo)) {
    $sql_insert = "INSERT INTO alumnos (nombre, correo) VALUES ('$nombre', '$correo')";
    if ($conexion->query($sql_insert) === TRUE) {
        echo "<h3 style='color: green;'>¡Alumno guardado</h3>";
    } else {
        echo "<h3 style='color: red;'>Error al guardar: " . $conexion->error . "</h3>";
    }
}

//SELECT
echo "<h3>Lista de alumnos registrados:</h3>";
$sql_select = "SELECT * FROM alumnos";
$resultado = $conexion->query($sql_select);

if ($resultado && $resultado->num_rows > 0) {
    echo "<ul>";
    while($fila = $resultado->fetch_assoc()) {
        echo "<li>ID: " . $fila["id"] . " - " . $fila["nombre"] . " (" . $fila["correo"] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay alumnos registrados</p>";
}

//Volver al formulario
echo '<br><a href="index.html">Volver al registro</a>';

$conexion->close();
?>