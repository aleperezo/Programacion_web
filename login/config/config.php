<?php
declare(strict_types=1); //Transformar todos los tipos de datos en estrictos
//Lee una sola vez el archivo .env y lo carga en las variables de entorno
$archivo = dirname(__DIR__) . '/.env'; //Obtener la ruta del directorio padre
//Guardamos ubicacion del archivo env en una variable de entorno
if(!is_file($archivo)) {
    throw new RuntimeException("No se puede leer o falta el archivo: $archivo"); //validaremos que existe
}
$variables = parse_ini_file($archivo, false, INI_SCANNER_RAW); //Toma archivo y lee de manera bruta todos los elementos
//Transformarlo en un tipo de variable
if($variables === false) {
    throw new RuntimeException("No se puede leer el archivo de configuracion: $archivo"); //Valida que ningun elemento este vacio
}
$leer = static function(string $clave) use ($variables): string { //validadr que sea un valor "valido"
   $valor = getenv($clave); //Obtenemos el valor de la variable de entorno
   if($valor === false) {
    $valor = $variables[$clave]?? null;
}
if(!is_string($valor)) {
    throw new RuntimeException("Falta variable de entorno");
}
return $valor;
};
$base = $leer ('DB_DATEBASE');
$puerto = filter_var($leer('DB_PORT'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]); //leemos base de datos y solo en este rango
if(!preg_match('/^[a-zA-Z0-9_]{1,64}+$/', $base) || $puerto === false) {
    throw new RuntimeException("Puerto de base de datos inválido o revisar la base de datos");
}
return [
    'app_name' => $leer('APP_NAME'),
    'database' => [
        'host' => $leer('DB_HOST'),
        'port' => $puerto,
        'name' => $base,
        'user' => $leer('DB_USER'),
        'password' => $leer('DB_PASSWORD'),
    ],
];

?>