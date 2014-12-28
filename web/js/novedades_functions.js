
$(document).ready(function() {
    

    /*
     * Tabla que muestra todos las novedades que hay en el sistema
     */    
    $("#tablaNovedades").jqGrid({
        colNames:['Código','Novedad', 'Nombre Sistema', 'Código Sistema', 'Fecha'],
        colModel:[ 
            {name:'id',index:'id', width:40, align:"center"}, 
            {name:'novedad',index:'novedad', width:55, align:"center"},
            {name: 'nombre_sistema',index:"nombre_sistema", width:40, align:"center"},  
            {name:'cod_sistema',index:'cod_sistema', width:30, align:"center"},
            {name:'fecha',index:'fecha', width:30, align:"center"}
            ],
    multiselect: true, 
        caption: "Novedades del Sistema"  ,
        width: 760,
        height: 400
    });

/*************************Funciones de agregar/modificar/eliminar novedad*****************************/

    (function () {
        actualizarTablaNovedades();     
    })();   

    /**
     * funcion quer permite actualizar la tabla con las novedades
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    function actualizarTablaNovedades(data){

        if(data == null){
            data = buscarNovedad("");
        } 

        $("#tablaNovedades").clearGridData();
        $.each(data, function(index, record) {            
            if($.isNumeric(index)) {
                $("#tablaNovedades").jqGrid('addRowData',index,record);
            }
        });
    }

    /**
     * funcion quer permite ingresar una nueva novedad al sistema
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    function crearNovedad(data){
        try
        {
            var saveData = {};

            saveData['novedad'] = data[0];
            saveData['sistema'] = data[1];

            var jObject = JSON.stringify(saveData);

            $.ajax({
                type: "POST",
                url: "index.php?action=crear_novedad_sistema", 
                data: {jObject:  jObject},
                dataType: "json",
                error: function(error){
                    alert("Error petición ajax");
                    console.log(error.toString());
                },
                success: function(result){
                    if(result.value == true){
                        mostrarMensaje(result.mensaje);
                    }
                    else{
                        mostrarMensaje(result.mensaje);
                    }
                }
            });
        }
        catch(ex){
            alert("Error: Ocurrio un error " + ex);
        }

    }
    /**
     * funcion que permite guardar los cambios sobre una novedad que ingrese el usuario
     * @return {[type]} [description]
     */
    function guardarModNovedad(){
        try
        {
            var idElementSelect = $("#tablaNovedades").jqGrid('getGridParam','selarrrow'); 
            var elementSelect = $("#tablaNovedades").jqGrid('getRowData',idElementSelect);
            var codigo = elementSelect.id;
            var nombre = $.trim($("#nombre").val());
            var sistema = $.trim($("#selectSistemaM").find(':selected').val());
            var saveData = {};

            console.log();
            if(sistema == "Seleccionar" && nombre == ""){
                alert("Error: Los campos obligatorios (*) no pueden quedar vacios y sin seleccionar una opción valida");

            }
            saveData['codigo'] = codigo;
            saveData['nombre'] = nombre;
            saveData['sistema'] = sistema;

            
            var jObject = JSON.stringify(saveData);

            $.ajax({
                type: 'POST',
                url: 'index.php?action=modificar_novedad_sistema',
                data: {jObject:  jObject},
                dataType: "json",
                error: function(error){
                    alert("Error petición ajax");
                    console.log(error.toString());
                },
                success: function(result){
                    if(result.value == true){
                        $("#divDialogModificacion").dialog("close");
                        alert(result.mensaje);
                        var data = buscarNovedad(elementSelect.id);
                        actualizarTablaOrdenes(data);
                    }
                    else{
                        alert(result.mensaje);
                    }
                }
            });
        }
        catch(ex){
            alert("Error: Ocurrio un error" + ex);
        }

    }

    /**
     * funcion que permite recuperar la informacion de las novedades del sistema
     * @param  {string} data [nombre de la novedad]
     * @return {objetc}      [objeto json]
     */
    function buscarNovedad(consulta){
        try{
            var dataResult;

            $.ajax({
                type: "POST",
                url: "index.php?action=buscar_novedad_sistema",
                data: "buscar=" + consulta,
                dataType: "json",
                async: false,
                error: function (error) {
                    alert("Error petición ajax" + error.toString());
                },
                success: function (data){
                    dataResult = data;
                    mostrarMensaje(dataResult.mensaje);
                }
            });
            return dataResult;
        }
        catch(ex){
            alert("Error: Ocurrio un error" + ex);
        }
    }

    /**
     * Evento que permite agregar nuevas novedades usando ajax
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#crearNovedad").click(function (e) {
        var novedad = $.trim($("#novedad").val());
        var sistema = $.trim($("#selectSistema").find(':selected').val());
        var data;

        if(novedad != "" && sistema != 0){
            data = [novedad,sistema];
            crearNovedad(data);
            $("#novedad").val("");
            $("#selectSistema").prop("selectedIndex",0);
        }
        else{
            alert("Error: Ingrese un valor en el campo de texto y seleccione un valor de la lista");
        }
    });

    /**
     * Evento que permite seleccionar una novedad de la tabla y modificar sus datos en el ventana modal
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#modificarNovedad").click(function (e) {
        try {      
            var idElementSelect = $("#tablaNovedades").jqGrid('getGridParam','selarrrow'); 
            
            if(idElementSelect.length > 1)
            {
                alert("Sólo puede ser modificado un elemento a la vez.");
            }
            else if(idElementSelect.length == 0)
            {
                alert("Selecciona un elemento de la tabla por favor.");
            }
            else
            {
                var elementSelect = $("#tablaNovedades").jqGrid('getRowData',idElementSelect);

                console.log(elementSelect.id);
                
                var data = buscarNovedad(elementSelect.id);

                console.log(data);
                
                $.each(data, function(index, record)
                {
                    if($.isNumeric(index)) 
                    {
                        $("#divDialogModificacion").dialog("open");
                        $( "#divDialogModificacion" ).dialog("option", "height", 450);
                        $("#campNumeroNovedad").html(elementSelect.id);
                        $("#nombre").val(record.novedad);
                        $('#sistema').val(record.nombre_sistema);          
                    }
                });                                 
            }
        } 
        catch(ex) {
            alert("ERROR: Ocurrio un error " + ex);
        }         
    });
    
    /**
     * evento que nos permite buscar una novedad de acuerdo a 2 parametros su nombre o id
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#buscarNovedad").click(function (e) {
        var vlr = $.trim($("#novedad").val());
        console.log(vlr);
        if(vlr == "")
        {
           mostrarMensaje("Error: Los campos obligatorios no pueden ser vacios.");
           $("#novedad").val("");
           $("#tablaNovedades").clearGridData();
        }
        else{
            var data = buscarNovedad(vlr);
            $("#novedad").val("");
            actualizarTablaNovedades(data);
        }   
    });

    $("#btGuardarModNovedad").click(function (e) {
        if(confirm("Esta seguro(a) que desea guardar" + " los cambios realizados a la novedad","Confirmación"))
        {
            guardarModNovedad();
        }
    });
    
});


