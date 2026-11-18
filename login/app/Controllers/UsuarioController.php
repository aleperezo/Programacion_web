<?php
declare(strict_types=1);
namespace App\Controllers;
//Controlador de la seccion privada de las listas registradas
final class UsuarioController{
    //Funcion que recibe el modelo de ususarios para consultar datos sin incluir sql en la lista
    public function __construct(private Usuario $usuarios){
    }
    //Comprobar si hay una sesion y muesatra unicamente el ID, nombre, correo, fecha, etc
    public function index():void{
        if(!isset($_SESSION['usuario'])){
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Iniciar sesión'];
            redireccionar('login');
        }
        vista('usuarios/index',[
            'titulo' => 'Usuarios','usuarios' => $this->usuarios->listar()
        ]);
?>