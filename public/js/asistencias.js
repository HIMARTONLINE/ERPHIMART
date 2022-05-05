
$('#entrada').daterangepicker({
    opens: 'left'
}, function(start, end, label) {
console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
});

$('#fechaComida').daterangepicker({
    opens: 'left'
}, function(start, end, label) {
console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
});

$('[data-consultarentrada="true"').on('click', function() {
    var continuar = true;
    $('.inputerror').removeClass('inputerror');
    var datos = $('#consulta').serializeArray();
    datos.forEach(function(value) {
        var elemento = $('#' + value.name);
        if (!main.validar(value.value, elemento.attr('data-tipo'))) {
            continuar = false;
            elemento.addClass('inputerror');
        }
    });

    if (continuar) {
        $('#consultaEntrada').submit();
    } else {
        main.alerta(etiquetas.alerta1, 'warning');
    }
});
$('[data-consultarcomida="true"').on('click', function() {
    var continuar = true;
    $('.inputerror').removeClass('inputerror');
    var datos = $('#consulta').serializeArray();
    datos.forEach(function(value) {
        var elemento = $('#' + value.name);
        if (!main.validar(value.value, elemento.attr('data-tipo'))) {
            continuar = false;
            elemento.addClass('inputerror');
        }
    });

    if (continuar) {
        $('#consulta').submit();
    } else {
        main.alerta(etiquetas.alerta1, 'warning');
    }
});