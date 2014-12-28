/**
*funciones globales de la aplicacion 
*/
$(document).ready(function () {
    
    /**
     * Función que maneja los eventos de despliegue del menu.
     */
    $('#menu li').hover(
        function () {
            $('ul', this).fadeIn();
        }, 
        function () {
            $('ul', this).fadeOut();	 	
        }
    );


     /**
     * Función que maneja el despliegue del panel de modificacion
     */    
    $("#divDialogModificacion").dialog({ autoOpen: false,
        width: 400,
        maxWidth: 600,
        height: 500,
        maxHeight: 600
    });
    
});

/**
 * Función que muestra el texto como mensaje en el panel superior.
 * @param {string} texto, Cadena que representa el mensaje a mostrar.
 * @returns {undefined}
 */
function mostrarMensaje(texto) {
        $("#divMensaje").empty();
        $("#divMensaje").text(texto);
}  

