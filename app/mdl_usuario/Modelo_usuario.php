<?php

class Modelo_usuario {
    protected $conexion;

    /**
     * Función contructura de la clase Modelo_usuario.
     * @param string $dbname nombre de la base de datos a la que se va a 
     * conectar el modelo.
     * @param string $dbuser usuario con el que se va a conectar a la 
     * base de datos.
     * @param string $dbpass contraseña para poder acceder a la base de datos.
     * @param string $dbhost Host en donde se encuentra la base de datos.
     */     
    public function __construct($dbname,$dbuser,$dbpass,$dbhost) {
        
        $conn_string = 'pgsql:host='.$dbhost.';port=5432;dbname='.$dbname;
        
        try { 
            $bd_conexion = new PDO($conn_string, $dbuser, $dbpass); 
            $this->conexion = $bd_conexion;
            
        } catch (PDOException $e) {
            var_dump( $e->getMessage());
        }       
    }
    
    /**
     * Función que permite comprobar la existencia de un usuario 
     * con acceso al sistema por medio de su login y password.
     * @param string $l, Cadena que hace referencia al login del usuario a 
     * comprobar.
     * @param string $c, Cadena que hace referencia al login del usuario a 
     * comprobar.
     * @return boolean
     */
    function comprobarExistenciaDeUsuario($l) {
        $l = htmlspecialchars($l);

        $sql = "SELECT * FROM usuarios_autorizados_novedad"
                . " WHERE login = '".$l."';";

        $l_stmt = $this->conexion->prepare($sql);
        if (!$l_stmt) {
            $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;
            return false;
        } else {
            if(!$l_stmt->execute()) { 
                $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
                return false;
            }
            
            if($l_stmt->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            }
        }      
    }    
    
    /**
     * Función que retorna la contraseña se un usuario por medio de su login.
     * @param string $l, Login del usuario a consultar.
     * @return boolean
     */
    function retornarContrasena($l) {
        $l = htmlspecialchars($l);

        $sql = "SELECT password FROM usuarios_autorizados_novedad"
                . " WHERE login = '".$l."';";

        $l_stmt = $this->conexion->prepare($sql);
        if (!$l_stmt) {
            $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;
            return false;
        }
        else {
            if(!$l_stmt->execute()) { 
                $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
                return false;
            }
            
            if($l_stmt->rowCount() > 0)
            {
                $result = $l_stmt->fetch( PDO::FETCH_NUM );
            }
        }
        return $result[0];
    }    
    
    /**
     * funcion que encripta la constraseña del usuario usando el metodo md5
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    public function encriptarPassword($password) {       
        return md5($password);
    }

    /**
     * Función que verifica si la contraseña digitada por el usuario es correcta
     * o no.
     * @param string $linput, login que digito el usuario.
     * @param string $cinput, password que digito el usuario.
     * @return boolean
     */
    public function verificarContrasena($linput, $cinput) {
        $passwdBd = $this->retornarContrasena($linput);
        
        if (md5($cinput) == $passwdBd) {
            return true;
        } 
        else {
            return false;
        }        
    }

  
    /*-------------------------------Comprobar Acceso-----------------------------------------*/
    
    /**
     * Función que permite comprobar si un determinado usuario tiene acceso o 
     * no al sistema.
     * @param string $login, Cadena que hace referencia al login del usuario.
     * @param string $password, Cadena que hace referencia al login del usuario.
     */
    public function comprobarAcceso($login, $password) {
        $login = htmlspecialchars($login);
        
        if(!$this->verificarContrasena($login, $password)) {
            $GLOBALS['mensaje'] = MJ_ERROR_CONTRASENA_INCORRECTA;
            return;
        }
        
        $password = $this->retornarContrasena($login);
        
        $sql = "SELECT id,nombre_usuario,login,perfil,correo,telefono,extension FROM usuarios_autorizados_novedad WHERE login = '".$login."' AND 
                password = '".$password."' AND estado = 'ACTIVO';";

        $l_stmt = $this->conexion->prepare($sql);

        if (!$l_stmt) {
            $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;            
        } else {
            if(!$l_stmt->execute()) { 
                $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
            }
            
            if($l_stmt->rowCount() > 0) { 
                $result = $l_stmt->fetchAll(); 
            }             
        } 

        $GLOBALS['mensaje'] = MJ_CONSULTA_EXITOSA;

        return $result[0];        
    }
    
}

?>