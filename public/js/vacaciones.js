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

});