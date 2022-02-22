const { values } = require("lodash");

// Crear el Formulario para el rango de fechas
var reportes = (function(window, undefined) {

    $('[data-consultarmes="true"]').on('click', function() {
        alert('Diste Click al boton');
    });
    /*
    var accione = function() {
        $('[data-consultarmes="true"]').on('click', function(e) {
            e.preventDefault();

            var continuar = true;

            $('.inputerror').removeClass('inputerror');
            var datos = $('#consulta').serializeArray();
            datos.forEach(function() {
                var elemento = $('#' + value.name);
                if (!main.validar(value.value, elemento.attr('data-tipo'))) {
                    continuar = false;
                    elemento.addClass('inputerror');
                }
            });

            if (continuar) {
                $('#consultames').submit();
            }
        });
    }*/
});