document.addEventListener('DOMContentLoaded', function() {
    
    let etiquetas = null;
    let dias_solicitados = [];

    let calendarioEl = document.getElementById('calendario');

    let calendario = new FullCalendar.Calendar(calendarioEl, { locale: 'es',
                                                        headerToolbar: { left:   "prev,next today",
                                                                         center: "title",
                                                                         right:  "dayGridMonth"
                                                                       },
                                                                events: { url: '/festivos/getbloqueados',
                                                                          type: 'POST',
                                                                          data: {custom_param1: 'something',
                                                                                 custom_param2: 'somethingelse'
                                                                                },
                                                                                error: function() {
                                                                                    main.alerta(etiquetas.alerta2, 'warning');
                                                                                },
                                                                        },
                                                                hiddenDays: [0],
                                                                dateClick: function(date) {
                                                                    
                                                                    if(moment().format('YYYY-MM-DD') == date.dateStr || date.jsEvent.isTrusted) {
                                                                        if(date.jsEvent.target.classList.contains('fc-daygrid-day-number') == false) {
                                                                            if(date.jsEvent.target.classList.contains('fc-bgevent') == false) {

                                                                                let select = date.dayEl.getAttribute("data-seleccionado");
                                                                                if(select == null || select == 'false') {
                                                                                    
                                                                                    let t = JSON.parse($('#vacaciones').attr('data-vacaciones'));
                                                                                    if(t > 0) {

                                                                                        dias_solicitados.push(date.dateStr);
                                                                                        t -= 1;
                                                                                        $("#vacaciones").attr('data-vacaciones', t).html(t);
                                                                                        date.dayEl.setAttribute("data-seleccionado", true);
                                                                                        date.dayEl.style.backgroundColor = "#81ecec";
                                                                                    }
                                                                                    
                                                                                }else {

                                                                                    let posicion = $.inArray(date.dateStr, dias_solicitados);
                                                                                    if(posicion != -1) {

                                                                                        dias_solicitados.splice(posicion, 1);
                                                                                        let t = JSON.parse($('#vacaciones').attr('data-vacaciones'));
                                                                                        t += 1;
                                                                                        $("#vacaciones").attr('data-vacaciones', t).html(t);
                                                                                        date.dayEl.setAttribute('data-seleccionado', false);
                                                                                        date.dayEl.style.backgroundColor = "";
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            });
    calendario.render();
    /*return {
        form: function(etiquetas_traduccion) {
            etiquetas = etiquetas_traduccion;
        }
    }*/
    $('.fc-prev-button, .fc-next-button').on('click', function(e) {
        dias_solicitados.forEach(function(value) {
            $('.fc-day[data-date="'+value+'"]').attr('data-seleccionado', true).css('background-color', '#81ecec');
        });
    });

    $('[data-guardar]').on('click', function(e) {
        e.preventDefault();
        
        var continuar = true;
        if(dias_solicitados.length > 0) {
            $('#dias_solicitados').val(JSON.stringify(dias_solicitados));
        } else {
            $('#dias_solicitados').val('');
        }
        
        $('.inputerror').removeClass('inputerror');

        $('[required="true"]').each(function() {
            if($(this).attr('required') != undefined && $(this).prop('disabled') == false) {
                if(!main.validar($(this).val(), $(this).attr('data-tipo'))) {
                    continuar = false;
                    $(this).addClass('inputerror');    
                    if($(this).hasClass('select2')) {
                        $(this).parent().find('.select2').find('.select2-selection--multiple').addClass('inputerror');
                    }
                }
            }
        });
        
        if(continuar) {
            $('#nuevo').submit();
        } else {
            alert("Error al enviar el formulario");
        }
    });

    $('[data-solicitud]').each(function() {
        $(this).on('click', function(e) {
            e.preventDefault();

            let datos = JSON.parse($(this).attr('data-solicitud'));
            $("#regreso").html(datos.fecha_ingreso);
            $("#diso tbody").html('');
            $("#descartar").attr('data-id', datos.id);
            datos.dias_solicitados.forEach(function(value, key) {
                moment.locale('es');
                let fecha = moment(value.fecha).format('DD/MM/YYYY');
                let numero = key + 1;
                $("#diso tbody").append('<tr><td>' + numero + '</td><td class="mayus ' + value.clase + '">' + fecha + '</span></td></tr>');
            });
            $('#solicitudModal').modal({backdrop : 'static', keyboard : false});
        });
    });

    $('#descartar').on('click', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        bootbox.confirm({
            message: etiquetas.apunto,
            buttons: {
                confirm: {
                    label: etiquetas.si,
                    className: 'btn-success'
                },
                cancel: {
                    label: etiquetas.no,
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result) {
                    $('#registro_id').val(id);
                    $('#formdelete').attr('action', '/vacaciones/'+id).submit();
                }
            }
        });
    });

});