<?php
header('Content-Type: application/json');
require 'conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $metodo = 'PUT';
}

switch ($metodo) {
    case 'GET':
        $sql = "SELECT id_usuario, nombre_completo, correo, edad, peso, tipo_membresia, fecha_vencimiento, estado, foto FROM usuarios";
        $resultado = $conn->query($sql);
        $usuarios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                if (empty($fila['foto'])) {
                    $fila['foto'] = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop';
                }
                $usuarios[] = $fila;
            }
        }
        echo json_encode($usuarios);
        break;

    case 'POST':
    case 'PUT':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $correo = $conn->real_escape_string($_POST['correo'] ?? '');
        $edad = intval($_POST['edad'] ?? 0);
        $peso = floatval($_POST['peso'] ?? 0);
        $membresia = $conn->real_escape_string($_POST['membresia'] ?? 'Basica');
        $vencimiento = $conn->real_escape_string($_POST['vencimiento'] ?? date('Y-m-d'));
        $contrasena = isset($_POST['contrasena']) ? $conn->real_escape_string($_POST['contrasena']) : '';

        $sql_foto_update = "";
        $ruta_destino = null;

        // Manejo robusto de la subida de archivos en macOS / XAMPP
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['foto']['name']));
            $carpeta_fisica = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/Programacion_web/anterior/Proyectos/uploads/';
            
            if (!file_exists($carpeta_fisica)) {
                mkdir($carpeta_fisica, 0777, true);
            }
            
            $ruta_fisica = $carpeta_fisica . $nombre_archivo;
            $ruta_destino = 'uploads/' . $nombre_archivo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_fisica)) {
                $sql_foto_update = ", foto='$ruta_destino'";
            } else {
                // Si vuelve a fallar por permisos de Mac, guardamos una ruta web simulada temporal o enviamos error detallado
                echo json_encode(['success' => false, 'error' => 'Error de permisos en Mac al mover el archivo. Revisa los permisos de la carpeta uploads.']);
                exit;
            }
        }

        if ($metodo === 'POST') {
            $foto_definitiva = $ruta_destino ? $ruta_destino : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop';
            $sql = "INSERT INTO usuarios (nombre_completo, correo, edad, peso, contrasena, tipo_membresia, fecha_vencimiento, foto) 
                    VALUES ('$nombre', '$correo', $edad, $peso, '$contrasena', '$membresia', '$vencimiento', '$foto_definitiva')";
        } else {
            $sql = "UPDATE usuarios SET 
                    nombre_completo='$nombre', 
                    correo='$correo', 
                    edad=$edad, 
                    peso=$peso, 
                    tipo_membresia='$membresia', 
                    fecha_vencimiento='$vencimiento' 
                    $sql_foto_update 
                    WHERE id_usuario=$id";
        }
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'mensaje' => 'Operación exitosa']);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id']);
        $conn->query("DELETE FROM asistencias WHERE id_usuario=$id");
        $sql = "DELETE FROM usuarios WHERE id_usuario=$id";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'mensaje' => 'Usuario eliminado']);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
}

$conn->close();
?>