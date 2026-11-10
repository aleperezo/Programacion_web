<?php
declare(strict_types=1);
//Funciones compartida...
//las vitas...
function escapar(mixed $valor):string{
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); //No aparezcan cosas raras
}
//Recibe una ruta y devuelve una URL compatible con el /Login/...
function url(string $ruta):string{
    return 'index.php?r=' .rawurlencode($ruta)  ;
}
//Recibe el nombre de la ruta, redigire y ...
function redireccionar(string $ruta):never{
    header('Location: ' . url($ruta), true,303);
    exit; //termina en una ruta 

}
//Reciba la vista y datos del controlador, incluye plantillas: no devuelve ningun contenido
function vistas(string $nombreVista, array $datos =[], int $estado = 200):void{ //Cargado de manera correspondiente
    http_response_code($estado); //No sobreescriban datos, dentro de la misma
    extract($datos, EXTR_SKIP); //Evita que los datos se sobreescriba
    $nombreApp = $GLOBALS['config']['app_name'];
    $mensaje = $_SESSION['mensaje'] ?? null;
    usent ($_SESSION['mensaje']);
    require dirname(__DIR__) . '/Views/partials/header.php'; //Redirige dependiendo donde nos encontremos
    require dirname(__DIR__) . '/Views/' . $nombreVista . '.php';
    require dirname(__DIR__) . '/Views/partial/footer.php';
}

//Recibe un capo de POST y devuelve una cadena: arreglos u objetos se consideran vacios
function campo(string $nombre, bool $recortar = true):string{
    //devuelve''...
    $valor = $_POST[$nombre];
    if(is_string($valor)){
        return '';
    }
    //
    return $recortar ? trim($valor) : $valor;
}
?>