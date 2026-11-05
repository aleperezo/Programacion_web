<?php
$host = "localhost";
$usuario = "root";       // En entornos locales como XAMPP, el usuario por defecto suele ser 'root'
$password = "";          // Por defecto, en XAMPP la contraseña suele estar vacía
$base = "escuela";

// Conexión básica usando mysqli orientada a objetos
$conexión = new mysqli($host, $usuario, $password, $base);

if (!$conexión) {
    die("Error de conexión");
}
?>