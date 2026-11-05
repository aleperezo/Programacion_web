<?php
// Recibimos los datos enviados mediante POST desde el formulario
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];

// Mostramos la información en pantalla
echo "<h1>Datos Recibidos</h1>";
echo "Nombre: " . $nombre . "<br>";
echo "Correo: " . $correo;
?>