<?php
//Cargar la configuracion
$config = require __DIR__ . '/config.php';

//Extraer las variable de entorno de config.php

$host = $config['DB_HOST'];
$dbname = $config['DB_NAME'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$charset = $config['DB_CHARSET'];

?>