document.addEventListener('DOMContentLoaded', function() {

    var dias_solicitados = [];

    var calendarEl = document.getElementById('calendario');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        events: {
            url: '/festivos/getbloqueados',
            type: 'POST',
            data: {
                custom_param1: 'something',
                custom_param2: 'somethingelse'
            },
            error: function() {
                alert("Error al trar los datos de la consulta");
            },
        },
        hiddenDays: [0, 6],

        dateClick: function(date) {

            if (moment().format('YYYY-MM-DD') == date.dateStr || date.jsEvent.isTrusted) {

                if (date.jsEvent.target.classList.contains('fc-daygrid-day-number') == false) {

                    if (date.jsEvent.target.classList.contains('fc-bgevent') == false) {
                        
                        let select = date.dayEl.getAttribute("dato-seleccionado");

                        if (select == null || select == 'false') {

                            dias_solicitados.push(date.dateStr);
                            date.dayEl.setAttribute("dato-seleccionado", true);
                            
                            date.dayEl.style.backgroundColor = "#81ecec";
                            
                        } 
                        else {

                            let posicion = $.inArray(date.dateStr, dias_solicitados);

                            if(posicion != -1) {

                                dias_solicitados.splice(posicion, 1);
                                date.dayEl.setAttribute("dato-seleccionado", false);
                                date.dayEl.style.backgroundColor = "";
                            }
                        }
                        
                    }
                }

            }

            //console.log(dias_solicitados);
        },
    });

    calendar.render();

    $('.fc-prev-button, .fc-next-button').on('click', function(e) {
        dias_solicitados.forEach(function(value) {
            $('.fc-day[data-date="'+value+'"]').attr('data-seleccionado', true).css('background-color', '#81ecec');
        });
    });

    $("#btnEnviar").on('click', function(e) {
        e.preventDefault();
        
        let continuar = true;

        if(dias_solicitados.length > 0) {

            $("#dias_solicitados").val(JSON.stringify(dias_solicitados));
        }else {

            $("#dias_solicitados").val('');
        }

        $('.inputerror').removeClass('inputerror');

        $('[required="true"').each(function() {

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
            $("#nuevo").submit();
        }else {
            alert("Error al guardar la solicitud de permiso");
        }
    });

    //Validar el formulario 
    $("#btnEnviar2").on('click', function () {   //evento click del boton con el id "btnEnviar"
        let input;
        $('#horas, #motivo').each(function (index, item) {  //recorremos los inputs y el textarea
            input = $(item);                

            input.closest('#horas, #motivo').removeClass('inputerror'); //si las etiquetas input y textarea son diferentes de vacio remueve la clase
            if (input.val() == "") {         //validamos si las etiquetas son vacias
                    $("#horas, #motivo").addClass('inputerror');       //si es vacia va agregar la clase inputerror junto con un mensaje de indicaciones al usuario 
                    main.alerta(etiquetas.alerta1, 'warning');
                    return false;
            }
           else {   
                   $("#fecha").removeClass('inputerror');       
                   $("textarea").removeClass('inputerror');    //si el campo textarea e imputs son diferentes de vacíos envia el formulario en objeto JSON
                    $("#nuevo2").submit();
                }
                
        });

    });
});