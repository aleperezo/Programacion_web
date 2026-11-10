<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Conexion;

//Modelo de MVC: para conectar con SQL y persistencia, sin generar HTML, no leer formularios
class Usuario {
    //Recibe el adimistrador de la base de datos y la configuracion de la base de datos
   public function __construct(private Conexion $conexion) {
   
   }
   public function buscarPorCorreo(string $correo): array|false {
    $consulta = $this->conexion->obtener()->prepare('SELECT id, nombre, correo, password_hash FROM usuarios WHERE correo = :correo');
    $consulta->execute(['correo' => $correo]);
    //fetch() devuelve un array asociativo con los datos del usuario o false si no se encuentra
    $usuario = $consulta->fetch();
   }
    //function para generar un registro de un usuario
    public function registrar(string $nombre, string $correo, string $hash):int{
        //hash es la contraseña encriptada
        $pdo = $this->conexion->obtener();
        $sql='INSERT INTO usuarios (nombre, correo, password_hash) VALUES (:nombre, :correo, :hash)';
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
        'nombre' => $nombre, 
        'correo' => $correo, 
        'password_hash' => $hash]);
        //devuelve el ID del usuario registrado
        return (int) $pdo->lastInsertId();
    }

    public function listar(): array {
        $sql = 'SELECT id, nombre, correo FROM usuarios ORDER BY nombre ASC';
        $consulta = $this->conexion->obtener()->query($sql)->fetchAll();
        return $consulta->fetchAll();
    }

    public function actualizarHash(int $id, string $hash): void {
        $sql = 'UPDATE usuarios SET password_hash = :hash WHERE id = :id';
        $consulta = $this->conexion->obtener()->prepare($sql);
        $consulta->execute([
            'hash' => $hash, 
            'id' => $id]);
        return $consulta->execute(['hash' => $hash, 'id' => $id]);
    }
}