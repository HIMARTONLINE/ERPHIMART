var presencia = (function (window, undefined) {
    var workers = {};

    var eventos = function() {
        $('#checador').submit(function(e){
            e.preventDefault();
        });

        $('#serial').on('keyup', function(e) {
            if(e.which === 13) {
                e.preventDefault();

                var ahora = moment(moment().format('HH:mm'),'HH:mm');
                var diferencia = moment.duration(ahora - limite)._milliseconds/60000;
                var clase = 'neutral';
                var referencia = $(this).val();
                var mandar = false;

                if(referencia.length > 0) {
                    var resultado = { 'res'    : false,
                                      'id'     : 0,
                                      'nombre' : 'Usuario no encontrado',
                                      'avatar' : 'assets/images/users/avatar.jpg',
                                      'clase'  : 'fallo',
                                      'tiempo' : ahora.format('HH:mm') };

                    if(personal[referencia]) {
                        mandar = true;
                        if(personal[referencia].registros == 0) {
                            if(diferencia < 0) {
                                clase = 'atiempo';
                            } else {
                                clase = 'tarde';
                            }
                        }

                        resultado = { 'res'    : true,
                                      'id'     : personal[referencia].id,
                                      'nombre' : personal[referencia].name,
                                      'avatar' : personal[referencia].foto,
                                      'clase'  : clase,
                                      'tiempo' : ahora.format('HH:mm') };
                    } else if(personal[referencia] == undefined) {
                        for(key in personal) {
                            if(personal[key].clave == referencia) {
                                mandar = true;
                                if(personal[key].registros == 0) {
                                    if(diferencia < 0) {
                                        clase = 'atiempo';
                                    } else {
                                        clase = 'tarde';
                                    }
                                }
                                
                                resultado = { 'res'    : true,
                                              'id'     : personal[key].id,
                                              'nombre' : personal[key].name,
                                              'avatar' : personal[key].foto,
                                              'clase'  : clase,
                                              'tiempo' : ahora.format('HH:mm') };
                            }
                        }
                    }

                    if(mandar) {
                        crearWorker(referencia);
                    }

                    $(this).val('').focus();
                    mostrar(resultado);
                }
            }
        });
    };

    var crearWorker = function(referencia) {
        workers[referencia] = new Worker('/js/presencia.worker.js');
        workers[referencia].addEventListener('message', function(e) {
            var resultado = JSON.parse(e.data);
        });

        $.get('/tokenpresencia', function(data, status) {
            workers[referencia].postMessage({ clave  : referencia,
                                              _token : data._token });
        });
    };

    var mostrar = function(data) {
        var tarjeta = '<div id="t'+data.id+'" class="tarjeta oculto '+data.clase+'">\
                           <div class="imagen">\
                               <img src="images/usuarios/'+data.avatar+'" />\
                           </div>\
                           <div class="contenido">\
                               <h3>'+data.nombre+'</h3>\
                               <h2 class="mt-1">'+data.tiempo+'</h2>\
                           </div>\
                       </div>';

        $('.usuarios').append(tarjeta);
        $('#t'+data.id).addClass('visible');
        setTimeout(function() {
            $('#t'+data.id).removeClass('visible');
            setTimeout(function() {
                $('#t'+data.id).remove();
            }, 1200);
        }, 3000);
    };

    var checkPageFocus = function() {
        let body = document.querySelector('body');

        if (document.hasFocus()) {
            $('.logo').removeClass('sleeping').find('img').attr('src', '/assets/images/logo.png');
            $('#serial').focus();
        } else {
            //$('.logo').addClass('sleeping').find('img').attr('src', '/img/sleeping.png');
        }
    }

    var personal = {};
    var limite = null;
    var setPersonal = function(datos) {
        datos.forEach(function(value) {
            personal[value.serial] = value;
        });
    };

    return {
        form : function(datos, entrada) {
            limite = moment(entrada,'HH:mm');
            setPersonal(datos);
            eventos();
        },
        checkPageFocus : function() {
            checkPageFocus();
        },
        renewToken : function() {
            
        },
        actualizaPersonal : function() {
            $.get('/getpersonal', function(data, status) {
                setPersonal(data);
            });
        }
    };

})(window, undefined);

setInterval(presencia.checkPageFocus, 1000); //1 segundo
setInterval(presencia.actualizaPersonal, 600000); //10 minutos