<?php
declare(strict_types=1);
//Espacio de trabajo para la conexion a la base de datos
namespace App\Core;
//PDO es la clase incluida en PHP para la conexion a la base de datos
use PDO;
//encapsular POO
final class Conexion {
    private ?PDO $pdo = null;
    public function __construct(private array $config) {
    }
    public function obtener():PDO{
     if($this->pdo === null){
        $c = $this->configuracion;
        //dsn = data source name
        //motor, servidor, base de datos, utf8mb4, admite unicode y emojis
        $dsn= sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['dbname']);
        $this->pdo=new PDO($dsn, $c['username'], $c['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //modo de error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //modo de 
            PDO::ATTR_EMULATE_PREPARES => false, //desactivar emulacion de consultas preparadas
        
        ]);
        $this->pdo->exec('SET time_zone = '+00:00'); //zona horaria
        
     }   
    }
}