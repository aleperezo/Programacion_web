<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$qr_data = $data['qr_data']; // Ejemplo que manda el QR: "GYMICO-USER-1"

// Extraer solo el número de ID (el "1")
$partes = explode('-', $qr_data);
$id_usuario = end($partes);

// Verificar si el socio existe
$check = $conn->query("SELECT nombre_completo FROM usuarios WHERE id_usuario = '$id_usuario'");

if ($check->num_rows > 0) {
    $socio = $check->fetch_assoc();
    
    // Insertar la asistencia
    $sql_insert = "INSERT INTO asistencias (id_usuario) VALUES ('$id_usuario')";
    if($conn->query($sql_insert)){
        echo json_encode(['success' => true, 'nombre' => $socio['nombre_completo']]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al guardar en BD']);
    }
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Socio no encontrado o QR inválido']);
}

$conn->close();
?>