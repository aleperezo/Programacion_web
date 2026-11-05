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
        
    }
}