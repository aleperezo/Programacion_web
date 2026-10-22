<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = intval($data['id_usuario']);
$codigo_ingresado = trim($data['codigo'] ?? '');

// Código diario requerido en recepción
$codigo_del_dia = "GYM2026"; 

if ($codigo_ingresado !== $codigo_del_dia) {
    echo json_encode(['success' => false, 'mensaje' => 'Código diario incorrecto o expirado.']);
    exit;
}

// Buscar al socio en la base de datos
$sql = "SELECT id_usuario, nombre_completo, tipo_membresia, foto FROM usuarios WHERE id_usuario = $id_usuario";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $socio = $resultado->fetch_assoc();
    
    $foto_url = !empty($socio['foto']) ? $socio['foto'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop';

    echo json_encode([
        'success' => true,
        'id' => $socio['id_usuario'],
        'nombre' => $socio['nombre_completo'],
        'membresia' => $socio['tipo_membresia'],
        'foto' => $foto_url
    ]);
} else {
    echo json_encode(['success' => false, 'mensaje' => 'El ID de socio no existe.']);
}

$conn->close();
?>