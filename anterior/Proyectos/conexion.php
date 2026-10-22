<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // Deja vacío si no le has puesto contraseña a tu XAMPP
$base_de_datos = "gymico_db";

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Establecer el conjunto de caracteres a utf8
$conn->set_charset("utf8mb4");
?>