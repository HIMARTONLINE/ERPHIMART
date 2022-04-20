$(document).ready(function() {
    //-----------grafica----------------------//

    let fechas = [];
    let datos = [];

    let val = $("#datosGrafica").val();
    val = JSON.parse(val);

    $.each(val, function(i, item) {
        fechas.push(i);
        datos.push(item);

    });


    const ctx = document.getElementById('graficaVentas').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: 'Ordenes',
                data: datos,
                backgroundColor: [
                    'rgba(255, 82, 0, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 82, 0, 1)',
                ],
                borderWidth: 3
            }, ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    /*var calendario = function() {
        calendario = $('#calendario').fullCalendar({
            locale: 'es',
            header: {
                left: "prev,next today",
                center: "title",
                right: "month"
            },
            events: {
                url: '/home/vacacionando',
                type: 'POST',
                data: {
                    custom_param1: 'something',
                    custom_param2: 'somethingelse'
                },
                error: function() {
                    main.alerta(etiquetas.alerta2, 'warning');
                },
            },
            /*hiddenDays : [0],
            eventRender: function(event, element) {
                if (event.icon) {
                    element.find('.fc-title').prepend('<i class="' + event.icon + ' mr-1"></i>');
                }
            }
        });
    };*/
});