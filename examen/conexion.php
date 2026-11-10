<?php
$host = "localhost";
$usuario = "root";    
$password = "12345";         
$base = "escuela";

$conexión = new mysqli($host, $usuario, $password, $base);

if (!$conexión) {
    die("Error de conexión");
}
?>