<?php
//Cargar la configuracion
$config = require __DIR__ . '/config.php';
//Si no encuentra el required, se detiene la ejecucion del script y muestra un error

//Extraer las variable de entorno de config.php
//Asignado a una variable para poder usarla en la conexion

$host = $config['DB_HOST'];
$dbname = $config['DB_NAME'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$charset = $config['DB_CHARSET'];

//Opciones recomendadas para la conexion PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Lanzar excepciones en caso de error de MSQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Listar info y verla como arreglo asociativo (array)
    PDO::ATTR_EMULATE_PREPARES => false, //No prepararemos consultas
];
//Hoja completa sobre que es el try y que es el catch una hoja de cada una, completa
try{
    //DSN:Indica el motor + host + nombre de la base de datos + charset
    $dsn = "
    mysql:host=$host;
    dbname=$dbname;
    charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options); //Metodos a ocupar

}catch(PDOException $e){
    die("Error en la DB" . $e->getMessage());

}

?>
//No traje cocretas para la perra mamda que acabo de hacer