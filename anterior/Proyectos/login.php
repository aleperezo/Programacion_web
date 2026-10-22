<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $conn->real_escape_string(trim($_POST['correo'] ?? ''));
    $contrasena = trim($_POST['contrasena'] ?? '');

    $sql = "SELECT * FROM usuarios WHERE correo = '$correo' AND contrasena = '$contrasena'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $user = $resultado->fetch_assoc();
        
        // Guardamos el ID del usuario en la sesión de PHP
        $_SESSION['id_usuario'] = $user['id_usuario'];
        
        // Redirigir al panel dinámico
        header("Location: panel.php");
        exit();
    } else {
        echo "<script>alert('Correo o contraseña incorrectos.'); window.location.href='sesiones.html';</script>";
        exit();
    }
} else {
    header("Location: sesiones.html");
    exit();
}
?>