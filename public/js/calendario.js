document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        events: {
            url: '/home/vacacionando',
            type: 'POST',
            data: {
                custom_param1: 'something',
                custom_param2: 'somethingelse'
            },
            error: function() {
                alert("Error al trar los datos de la consulta");
            },
        },
        eventRender: function(event, element) {
            console.log(event);
           /* if (event.icon) {
                element.find('.fc-sticky').prepend('<i class="' + event.icon + ' mr-1"></i>');
            }*/
        }
    });

    calendar.render();
});