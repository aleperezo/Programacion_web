<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;
$codigo_ingresado = isset($data['codigo']) ? trim(strtoupper($data['codigo'])) : '';

// Código del día generado automáticamente por PHP (Ej: GYM20261023)
// Cambiará solo cada día sin que tengas que moverle nada.
$codigo_del_dia = "GYM" . date('Ymd'); 

if ($codigo_ingresado !== $codigo_del_dia) {
    echo json_encode(['success' => false, 'mensaje' => 'Código diario incorrecto o expirado.']);
    exit;
}

if ($id_usuario <= 0) {
    echo json_encode(['success' => false, 'mensaje' => 'Por favor, ingresa un ID de socio válido.']);
    exit;
}

// Consultar datos y fecha de vencimiento del usuario
$sql = "SELECT id_usuario, nombre_completo, tipo_membresia, foto, fecha_vencimiento FROM usuarios WHERE id_usuario = $id_usuario";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $socio = $resultado->fetch_assoc();
    
    $foto_url = !empty($socio['foto']) ? $socio['foto'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop';

    // Validar si la membresía está activa (fecha de vencimiento mayor o igual a hoy)
    $hoy = date('Y-m-d');
    $vencimiento = $socio['fecha_vencimiento'] ? $socio['fecha_vencimiento'] : $hoy;
    $activa = ($vencimiento >= $hoy);

    echo json_encode([
        'success' => true,
        'id' => intval($socio['id_usuario']),
        'nombre' => $socio['nombre_completo'],
        'membresia' => $socio['tipo_membresia'],
        'foto' => $foto_url,
        'activa' => $activa,
        'vencimiento' => $vencimiento
    ]);
} else {
    echo json_encode(['success' => false, 'mensaje' => 'El ID de socio no existe en el sistema.']);
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>