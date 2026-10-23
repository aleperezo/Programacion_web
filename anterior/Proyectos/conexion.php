<?php
// $host = "localhost";
// $usuario = "root";
// $contrasena = "";
// $base_de_datos = "gymico_db";
$host = "sql303.infinityfree.com:3306";
$usuario = "if0_43105156";
$contrasena = "6zFs3PkYiWUidEj";
$base_de_datos = "if0_43105156_gymico_db";

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Establecer el conjunto de caracteres a utf8
$conn->set_charset("utf8mb4");
?>