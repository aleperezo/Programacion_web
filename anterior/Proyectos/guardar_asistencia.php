<?php
header('Content-Type: application/json');
require 'conexion.php';

// Recibir los datos enviados por JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = intval($data['id_usuario']);

// Insertar el registro de asistencia en la base de datos vinculado al ID del socio
$sql = "INSERT INTO asistencias (id_usuario) VALUES ($id_usuario)";

if ($conn->query($sql)) {
    echo json_encode([
        'success' => true, 
        'mensaje' => 'Asistencia registrada correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'mensaje' => 'Error al registrar en la base de datos: ' . $conn->error
    ]);
}

$conn->close();
?>