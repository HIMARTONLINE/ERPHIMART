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
});