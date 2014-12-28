<?php
//definicion de la ruta de la aplicacion
define('__ROOT__', dirname(dirname(__FILE__)));

//dependencias de la aplicacion 
require_once __ROOT__.'/app/Config.php'; 
require_once __ROOT__.'/app/Constantes.php';
require_once __ROOT__.'/app/Controlador_vista.php';
require_once __ROOT__.'/app/mdl_novedades/Modelo_novedades.php';
require_once __ROOT__.'/app/mdl_novedades/Controlador_novedades.php';
require_once __ROOT__.'/app/mdl_usuario/Controlador_usuario.php';
require_once __ROOT__.'/app/mdl_usuario/Modelo_usuario.php';

//variable global
global $mesaje;

//mapa de enrutamiento
$map = array(
    'listar_novedades' => array('controlador' =>'Controlador_novedades', 'action' =>'listar'),
    'buscar_novedad_sistema' =>array('controlador' =>'Controlador_novedades', 'action' =>'buscarNovedadSistema'),
    'crear_novedad_sistema' =>array('controlador' =>'Controlador_novedades', 'action'=>'crearNovedadSistema'),
    'modificar_novedad_sistema' =>array('controlador' =>'Controlador_novedades', 'action'=>'modificarNovedadSistema'),   
    'iniciar_sesion' => array('controlador' =>'Controlador_usuario', 'action' =>'iniciar_sesion'),
    'salir_sesion' => array('controlador' =>'Controlador_usuario', 'action' =>'salir_sesion')
);

// Parseo de la ruta
if (isset($_GET['action']))
{
    if (isset($map[$_GET['action']]))
    {
        $ruta = $_GET['action'];
    }
    else
    {
        header('Status: 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' .
                $_GET['action'] .
                '</p></body></html>';
        exit;
    }
}
else
{
    $ruta = 'iniciar_sesion';
}

// Checkear el acceso del usuario
if(!call_user_func(array(new Controlador_usuario, 'check')))
{
    $ruta = 'iniciar_sesion';
}

$controlador = $map[$ruta];

// Ejecución del controlador asociado a la ruta
if (method_exists($controlador['controlador'],$controlador['action']))
{   
    call_user_func(array(new $controlador['controlador'], $controlador['action']));
}
else
{
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' .
            $controlador['controlador'] .
            '->' .
            $controlador['action'] .
            '</i> no existe</h1></body></html>';
}
?>
