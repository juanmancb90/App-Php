<?php

class Controlador_usuario {

    
    /*-------------------------------Operaciones del Index-----------------------------------------*/
    /**
     * Función que permite cerrar una sesion de un usuario.
    */
    public function salir_sesion() {
        session_start();
        
        //eliminar informacion almacenada de la sesion
        session_unset();
        //finalizar sesion
        session_destroy ();
        
        $data = array(
            'mensaje' => 'Bienvenido  '. date('d-m-y  h:i A'),
        );
        
        $v = new Controlador_vista();
        $v->retornar_vista($_SESSION["perfil"],USUARIO, INICIAR_SESION, $data);
    }    
    
    /**
     * Función que permite chekear si hay una sesion iniciada.
    */    
    public function check() {
        session_start();
        if(isset($_SESSION['userid']) & $_SESSION["autorizado"]) {
            
            $fechaGuardada = $_SESSION["ultimoAcceso"]; 
            $ahora = time(); 
            $tiempo_transcurrido = $ahora-$fechaGuardada;
 
            if($tiempo_transcurrido >= T_SEGUNDOS_INACTIVIDAD_PERMITIDO) { 
                session_unset();
                session_destroy();
                return false;
            }else { 
                $_SESSION["ultimoAcceso"] = $ahora;
                return true;
            }             
        }   
        return false;
    }

    /**
     * Función que permite iniciar una sesion por un usuario, ademas esta
     * función se encarga de desplegar el panel de logeo o el mostrar la pagina
     * de inicio de la aplicación web.
     */    
    public function iniciar_sesion() {
        session_start();
        
        //instaciar el objeto de la clase modelo
        $m = new Modelo_usuario(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
                    Config::$mvc_bd_clave, Config::$mvc_bd_hostname);  
        
        $v = new Controlador_vista();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($infoResult = $m->comprobarAcceso($_POST['login'], $_POST['password']))
            {
                $_SESSION["autorizado"] = true;
                session_regenerate_id();   
                $_SESSION["userid"] = session_id();
                $_SESSION["perfil"] = $infoResult["perfil"];
                $_SESSION["login"] = $infoResult["login"];
                $_SESSION["nombre_usuario"] = $infoResult["nombre_usuario"];
                $_SESSION["id_db_user"] = $infoResult["id"];
                $_SESSION["ultimoAcceso"] = time();
                
                $data = array(
                    'mensaje' => 'Bienvenido/a al sistema '. $_SESSION["nombre_usuario"],
                );  

                $v->retornar_vista($_SESSION["perfil"],NOVEDADES, OPERATION_LIST, $data);
            } else {
                $data = array(
                    'mensaje' => 'Intentelo de nuevo. Puede que haya '
                    . 'escrito mal su usuario o contraseña '
                );

                $v->retornar_vista($_SESSION["perfil"],USUARIO, INICIAR_SESION, $data);                 
            }
        } else {
            if($_SESSION["autorizado"] & isset($_SESSION['userid']) 
                    & isset($_SESSION['perfil'])) {
                $data = array('mensaje' => 'Bienvenido/a al sistema '.$_SESSION["nombre_usuario"],);

                $v->retornar_vista($_SESSION["perfil"],NOVEDADES, OPERATION_LIST, $data);                
            } else {
                $this->salir_sesion();
            }
        }
    }    
}

?>