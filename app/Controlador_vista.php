<?php

/**
 * Clase que controla lo que va ser visto por el usuario. Controla la visualización
 * de la interfaz de usuario.
**/
class Controlador_vista
{
    //array que guarda el diccionario de rutas del menu y formularios
    var $diccionario = array();
    
    /**
     * Metodo constructora del Controlador_vista.
     * @global array $diccionario
    **/
    function __construct() {
        
        global $diccionario;
        
        $diccionario = array(           
            'links_menu'=>array(
                'SALIR'=>'index.php?action='.OPERATION_SALIR_SESION,
                'LIST_NOVEDADES'=>'index.php?action='.OPERATION_LIST.'_'.NOVEDADES
                ),
            'form_actions'=>array(
                'FORM_INICIAR_SESION'=>'index.php?action='.INICIAR_SESION
                )
        );        
    }
    
    /**
    *funcion que permite cargar de forma dinamica las librerias de la aplicacion web
    *@param $module. Hace referencia la modulo que se carga una vez ejecutada una ruta
    **/
    function crear_enlace_libreria($module)
    {
        $link = "<script type='text/javascript' src='js/".$module."_functions.js'>"
                . "</script>";

        return $link;
    }
    
    /**
     * Función que permite conseguir y alamacenar como un string el menu de
     * operaciones adicional dependiendo del perfil del usuario. 
     * @param string $perfil, Cadena que hace referencia la perfil de usuario 
     * con acceso al sistema de inventario.
     * @return string
    **/
    function conseguir_operaciones_add($perfil = 'normal')
    {
        if(strcmp($module, 'super') == 0)
        {
            $file = dirname(__FILE__).'/templates/vistas_menu_usuario/'.'m_operaciones_add_'.$perfil.'.html';
        }
        else
        {
            $file = dirname(__FILE__).'/templates/vistas_menu_usuario/'.'m_operaciones_add_'.$perfil.'.html';
        }
                
        $template = file_get_contents($file);
        
        return $template;               
    }

    /**
     * Función que permite conseguir la plantilla de una vista y guardarla en
     * un string.
     * @param string $module, Cadena que hace referencia al modulo al cual
     * pertenece la vista a visualizar.
     * @param string $operation, Cadena que hace referencia a la operación que
     * permite realizar la vista que se va a visualizar.
     * @return string.
    **/
    function conseguir_plantilla($module='novedades', $operation='')
    {        
        if(strcmp($module, 'layout1') == 0 || strcmp($module, 'layout2') == 0 )
        {
            $file = dirname(__FILE__).'/templates/'.$module.'.html';
        }
        else
        {
            $file = dirname(__FILE__).'/templates/vistas_mdl_'.$module.'/'
                    .$module.'_'.$operation.'.html';
        }
                
        $template = file_get_contents($file);

        return $template;
    }

    /**
     * Función que permite replazar dinamicamente informacion en cada página 
     * html.
     * @param string $html, Cadena que que contiene el html de la página a
     * visualizar.
     * @param array $data, Array que contiene la información a remplazar.
     * @return string
    **/
    function representar_datos_dinamica($html, $data)
    {
        foreach ($data as $clave=>$valor)
        {
            $html = str_replace('{'.$clave.'}', $valor, $html);
        }

        return $html;
    }
           
    /**
     * Función que retorna y permite visualizar la página requerida por el 
     * usuario.
     * @global array $diccionario
     * @param string $perfil, Cadena que hace referencia al perfil o privilegios
     * del usuario.
     * @param string $module, Cadena que hace referencia al modulo al cual
     * pertenece la vista a visualizar.
     * @param string $operation, Cadena que hace referencia a la operación que
     * permite realizar la vista que se va a visualizar.
     * @param array $data, Arreglo que contiene la información que se va a
     * reemplazar dinámicamente. 
    **/
    function retornar_vista($perfil, $module, $operation, $data=array()) {
        
        global $diccionario;
        
        if(strcmp($module, USUARIO) == 0 & 
                strcmp($operation, INICIAR_SESION) == 0)
        {
            $html = $this->conseguir_plantilla('layout1', '');
            $html = str_replace('{contenido}', 
                    $this->conseguir_plantilla($module, $operation), $html); 
            $html = str_replace('{librerias_adicionales}', '', $html);                   
            $html = $this->representar_datos_dinamica($html, 
            $diccionario['form_actions']);            
            $html = $this->representar_datos_dinamica($html, $data);
            
        }
        else
        {
            $html = $this->conseguir_plantilla('layout2', '');
            $html = str_replace('{operaciones_adicionales_adm_usuarios}', 
                    $this->conseguir_operaciones_add($perfil), $html);
            $html = str_replace('{librerias_adicionales}', 
                    $this->crear_enlace_libreria($module), 
                    $html);            
            $html = str_replace('{contenido}', 
                    $this->conseguir_plantilla($module, $operation), $html);
            $html = $this->representar_datos_dinamica($html, 
                    $diccionario['form_actions']);
            $html = $this->representar_datos_dinamica($html, 
                    $diccionario['links_menu']);
            $html = $this->representar_datos_dinamica($html, $data);            
        }
        
        print $html;
    }

}

?>

