var autorizaciones = (function (window, undefined) {
    var etiquetas = null;

    var acciones = function() {
        $('[data-solicitud]').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                var datos = JSON.parse($(this).attr('data-solicitud'));
                $('#regreso').html(datos.reingreso);
                $('#diso tbody').html('');
                datos.dias_solicitados.forEach(function(value, key) {
                    moment.locale('es');
                    var fecha = moment(value.fecha).format('DD.MMMM.YYYY');
                    var numero = key+1;
                    $('#diso tbody').append('<tr><td>'+numero+'</td><td><span class="mayus '+value.clase+'">'+fecha+'</span></td></tr>');
                });
                $('#solicitudModal').modal({ backdrop : 'static', keyboard : false });
            });
        });

        /*$('[data-premisos]').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                console.log("Diste click");
                var datos = JSON.parse($(this).attr('data-premisos'));
                $('#regresoPermisos').html(datos.reingreso);
                $('#disoPermisos tbody').html('');
                
            });
        });*/

        $('[data-autoriza]').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                var datos = JSON.parse($(this).attr('data-autoriza'));

                $('#formautoriza').attr('action', '/autorizacion/'+datos.id);
                var personal = '<tr>\
                                    <td>'+datos.name+'</td>\
                                    <td>'+datos.solicitado+'</td>\
                                    <td>'+datos.reingreso+'</td>\
                                </tr>';
                $('#autorizaTable tbody').html('').append(personal);
                $('#autorizaVacacionesModal').modal({ backdrop : 'static', keyboard : false });
            });
        });

        $('#siautorizo').on('click', function(e) {
            e.preventDefault();
            $('#formautoriza #autorizacion').val(1);
            $('#formautoriza').submit();
        });

        $('#noautorizo').on('click', function(e) {
            e.preventDefault();
            $('#formautoriza #autorizacion').val(0);
            $('#formautoriza').submit();
        });

        $('[data-permiso]').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                let datos = JSON.parse($(this).attr('data-permiso'));

                $('#formpermisos').attr('action', '/autorizacion/permisos/'+datos.id);
                var personal = '<tr>\
                                    <td>'+datos.name+'</td>\
                                    <td>'+datos.inicio+'</td>\
                                    <td>'+datos.reingreso+'</td>\
                                </tr>';
                $('#autorizaTable2 tbody').html('').append(personal);
                $('#autorizaPermisos').modal({ backdrop : 'static', keyboard : false });
            });
        });

        $('#siautorizo2').on('click', function(e) {
            e.preventDefault();
            
            $('#formpermisos #autorizacionpermisos').val(1);
            $('#formpermisos').submit();
        });

        $('#noautorizo2').on('click', function(e) {
            e.preventDefault();
           
            $('#formpermisos #autorizacionpermisos').val(0);
            $('#formpermisos').submit();
        });
    };

    // Funciones para el reloj 
    /*function actual() {
        fecha = new Date(); //Actualizar fecha
        hora = fecha.getHours(); //Hora actual
        minuto = fecha.getMinutes(); //minuto actual
        segundo = fecha.getSeconds(); //segundos actual
        
        if(hora < 10) {     //asigna dos cifras para la hora 
            hora = "0" + hora;
        }

        if(minuto < 10) {   //asigna dos cifras para los minutos
            minuto = "0" + minuto;
        }

        if(segundo < 10) {  //asigna dos cifras para los segundos
            segundo = "0" + segundo;
        }

        //devolver los datos 
        relojPermisos = hora + " : " + minuto + " : " + segundo;
        return relojPermisos;
    }

    function actualizar() {
        miHora = actual();  //Recoge la hora actual
        miReloj = document.getElementById("reloj"); //Seleccionamos el div con el id reloj y le asignamos el valor
        miReloj.innerHTML=miHora;
    }
    
    setInterval(actualizar, 1000);*/

    return {
        init : function(etiquetas_traduccion) {
            etiquetas = etiquetas_traduccion;
            acciones();
        },
    };

})(window, undefined);