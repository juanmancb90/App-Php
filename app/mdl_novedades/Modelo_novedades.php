<?php
/**
 * clase Modelo del modulo
 */
class Modelo_novedades {
    protected $conexion;

    /**
     * Función contructur de la clase Modelo
     * @param string $dbname nombre de la base de datos a la que se va a 
     * conectar el modelo.
     * @param string $dbuser usuario con el que se va a conectar a la 
     * base de datos.
     * @param string $dbpass contraseña para poder acceder a la base de datos.
     * @param string $dbhost Host en donde se encuentra la base de datos.
     */    
    public function __construct($dbname,$dbuser,$dbpass,$dbhost) {
        
        $conn_string = 'pgsql:host='.$dbhost.';port=5432;dbname='.$dbname;
        
        try
        { 
            $bd_conexion = new PDO($conn_string, $dbuser, $dbpass); 
            $this->conexion = $bd_conexion;  
            
        }
        catch (PDOException $e)
        {
            var_dump( $e->getMessage());
        }       
    }

    /**
     * funcion que permite registrar una novedad en el sistema
     * @param  [string] $n [hace referencia al nombre de la novedad ingresada por el administrador]
     * @param  [string] $s [hace referencia al nombre del sistema seleccionado por el usuario]
     * @return [boolean]   
     */
    public function crearNovedad($n, $s){

        $n = htmlspecialchars($n);
        $s = htmlspecialchars($s);

        $tmp = $this->getNombreSistema($s);
        $nombre = $tmp[0];
    
        $sql = "INSERT INTO novedad_sistema (novedad,nombre_sistema,cod_sistema) VALUES ('".$n."','".$nombre."','".$s."');";
        $l_stmt = $this->conexion->prepare($sql);

        if(!$l_stmt)
        {
            $GLOBALS['mensaje'] = "Error: SQL";
            //$GLOBALS['mensaje'] = var_export($this->conexion->errorInfo(),true);;
            return false;
        }
        else{
            if(!$l_stmt->execute())
            {
                $GLOBALS['mensaje'] = "Error: La novedad ya se encuentra registrada en el sistema.";
                //$GLOBALS['mensaje'] = var_export($this->conexion->errorInfo(),true);;
                return false;
            }
        }

        $GLOBALS['mensaje'] = "Se registro de forma correcta la novedad en el sistema";
        return true;
    }

    /**
     * funcion que permite obtener el nombre del sistema
     * @param  [string] $s [Hace referencia al codigo del sistema ingresado por el usuario]
     * @return [array]    [Retorna el nombre del sistema]
     */
    public function getNombreSistema($s){
        $sql = "SELECT sistema FROM sistema where cod_sistema = '".$s."';";

        $l_stmt = $this->conexion->prepare($sql);

        if(!$l_stmt){
            $GLOBALS['mensaje'] = "Error: SQL";
            //$GLOBALS['mensaje'] = var_export($this->conexion->errorInfo(),true);;
        }
        else{
            if(!$l_stmt->execute()){
                $GLOBALS['mensaje'] = "Error: SQL";
                //$GLOBALS['mensaje'] =var_export($this->conexion->errorInfo(),true);;
            }
            if($l_stmt->rowCount() > 0){
                $result = $l_stmt->fetchAll();
            }
        }

        return $result[0];
    }

    /**
     * Función que permite buscar novedades en el sistema utilizando el nombre
     * de este.
     * @param strig $n, palabra clave.
     * @return array
     */
    public function buscarNovedadNombre($n)
    {
        $n = htmlspecialchars(trim($n));
        
        if($n == '' or $n == null){
            $sql = "SELECT * FROM novedad_sistema;";

            $l_stmt = $this->conexion->prepare($sql);

            if (!$l_stmt)
            {
                $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;            
            }
            else
            {
                if(!$l_stmt->execute())
                {
                    $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
                }
                
                if($l_stmt->rowCount() > 0)
                {
                    $result = $l_stmt->fetchAll();
                    $GLOBALS['mensaje'] = MJ_CONSULTA_EXITOSA;
                }            
            }

            return $result;

        }
        else
        {
            $sql = "SELECT * FROM novedad_sistema WHERE novedad = '".$n."';";

            $l_stmt = $this->conexion->prepare($sql);

            if (!$l_stmt)
            {
                $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;            
            }
            else
            {
                if(!$l_stmt->execute())
                {
                    $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
                }
                if($l_stmt->rowCount() > 0)
                {
                    $result = $l_stmt->fetchAll();
                    $GLOBALS['mensaje'] = MJ_CONSULTA_EXITOSA;
                }
                else
                {
                    $GLOBALS['mensaje'] = "No hay registro asociado a su consulta";
                }              
            }

            return $result;
        }
    } 
    
    /**
     * Función que permite consultar una novedad en el sistema por medio de su
     * id.
     * @param numerico $k, Entero que hace referencia al id de la novedad
     */
    public function buscarNovedadId($k)
    {
        $k = htmlspecialchars(trim($k));

        $sql = "SELECT * FROM novedad_sistema WHERE id = '".$k."';";

        $l_stmt = $this->conexion->prepare($sql);

        if (!$l_stmt)
        {
            $GLOBALS['mensaje'] = MJ_PREPARAR_CONSULTA_FALLIDA;            
        }
        else
        {
            if(!$l_stmt->execute())
            {
                $GLOBALS['mensaje'] = MJ_CONSULTA_FALLIDA;
            }
            if($l_stmt->rowCount() > 0)
            {
                $result = $l_stmt->fetchAll();
                $GLOBALS['mensaje'] = MJ_CONSULTA_EXITOSA;
            }
            else
            {
                $GLOBALS['mensaje'] = "No hay registro asociado a su consulta";
            }           
        } 
       
        return $result;
    }

    public function validarDatosNovedad($n, $s){
        $n = htmlspecialchars(trim($n));
        $s = htmlspecialchars(trim($s));

        if(is_string($n) & $s != 0 & $n != '')
        {
             return true;
        }
        else
        {
            $GLOBALS['mensaje'] = MJ_REVISE_FORMULARIO." ".MJ_NO_CAMPOS_VACIOS." ".", además selecciona una opción valida de la lista.";
            return false;
        }
    }

    public function modificarNovedad($c, $n, $s){
        $c = htmlspecialchars(trim($c));
        $n = htmlspecialchars(trim($n));
        $s = htmlspecialchars(trim($s));

        $tmp = $this->getNombreSistema($s);
        $nombre = $tmp[0];
    

        $sql = "UPDATE novedad_sistema SET "
                . "novedad = '".$n."', "
                . "nombre_sistema = '".$nombre."', "
                . "cod_sistema = '".$s."'"
                . " WHERE id = ".$c.";";

        $l_stmt = $this->conexion->prepare($sql);

        if (!$l_stmt)
        {
            $GLOBALS['mensaje'] = MJ_UPDATE_FALLIDA;
            return false;
        }
        else
        {
            if(!$l_stmt->execute())
            {
                $GLOBALS['mensaje'] = MJ_UPDATE_FALLIDA;
                return false;
            }
        }

        $GLOBALS['Mensaje'] = "La información de la novedad seleccionada se ha actualizado correctamente";
        return true;
    }

}
?>