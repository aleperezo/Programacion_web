<?php
//datos del servidor de la base de datos, MySQL
//$host = "127.0.0.1:3306";
//$user = "root";
//$password = "";
//$dbName = "crud_app";
$host = "sql303.infinityfree.com:3306";
$user = "if0_43105156";
$password = "6zFs3PkYiWUidEj";
$dbName = "if0_43105156_crud_app";

$conn = new mysqli($host, $user, $password, $dbName);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
} else {
    echo "Conexion exitosa a la base de datos";
}

?>