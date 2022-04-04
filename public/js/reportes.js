$(document).ready(function() {
    //-----------grafica----------------------//

    let fechas = [];
    let datos = [];
    let proId = [];
    let mejor = [];

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
                },
                /*{
                    label: 'Producto',
                    data: top10,
                    backgroundColor: [
                        'rgba(0, 80, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(0, 80, 255, 1)',
                    ],
                    borderWidth: 3
                }*/
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
/*$('#fecha').daterangepicker({
    timePicker: false,
    timePickerIncrement: 30,
    autoApply: true,
    locale: {
        format: 'DD/MM/YYYY'
    }
});*/


//---------------------------- DateRangePicker------------------------//

var start = moment().subtract(29, 'days');
var end = moment();

function cb(start, end) {
    $('#reportrange input').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
        'Hoy': [moment(), moment()],
        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Ultimos 7 días': [moment().subtract(6, 'days'), moment()],
        'Ultimos 30 días': [moment().subtract(29, 'days'), moment()],
        'Este mes': [moment().startOf('month'), moment().endOf('month')],
        'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
}, cb);

cb(start, end);