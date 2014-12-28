<?php
/**
 * Descripcion del controlador del modulo
 *
 */
class Controlador_novedades
{
    
    /**
     * Funcion que permite desplegar el modulo de novedades
     */
    public function listar(){

        $GLOBALS['mensaje'] = "";
        
        $data = array(
            'mensaje' => 'Agregar/Modificar/Eliminar Novedades del Sistema',
        );  
        
        $v = new Controlador_vista();
        $v->retornar_vista($_SESSION["perfil"],NOVEDADES, OPERATION_LIST, $data);

    }
    /**
     * funcion que permite agregar una nueva novedad en el sistema 
     * @return [json] [Objeto con un mensaje de confirmacion o error de la ejecucion del modelo]
     */
    public function crearNovedadSistema(){
        $GLOBALS['mensaje'] = "";

        $m = new Modelo_novedades(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, 
                    Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $info = json_decode($_POST['jObject'], true);

            $rslt = $m->crearNovedad($info['novedad'], $info['sistema']);

            if($rslt){
                $result = array('value' => true,);
            }
            else{
                $result = array('value' => false,);
            }
        }
        $result['mensaje'] = $GLOBALS['mensaje'];

        echo json_encode($result);
    }

    /**
     * funcion que permite realizar las consultas de la novedades del sistema 
     * @return [json] [Archivo con los valores de la novedad]
     */
    public function buscarNovedadSistema(){
        $GLOBALS['mensaje'] = "";

        $m = new Modelo_novedades(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, 
                    Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(is_numeric($_POST['buscar'])) {
                $data = $m->buscarNovedadId($_POST['buscar']);
            }
            else if(is_string($_POST['buscar'])){
                $data = $m->buscarNovedadNombre($_POST['buscar']);
            }
            else{
                $data['mensaje'] = "Error el campo de busqueda no puede estar vacio";
            }  
        }  
        
        $data['mensaje'] = $GLOBALS['mensaje']; 
        
        echo json_encode($data);

    }

    public function modificarNovedadSistema(){
        $GLOBALS['mensaje'] = "";
        
        $m = new Modelo_novedades(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, 
                    Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $info = json_decode($_POST['jObject'], true);
            if($m->validarDatosNovedad($info['nombre'], $info['sistema'])){
                $rslt = $m->modificarNovedad($info['codigo'], $info['nombre'], $info['sistema']);
            }
            if($rslt) 
            {
                $result = array(
                    'value' => true,
                );
            }
            else
            {
                $result = array(
                    'value' => false,
                );                
            }
        }

        $result['mensaje'] = $GLOBALS['mensaje'];

        echo json_encode($result);
    }


}

?>