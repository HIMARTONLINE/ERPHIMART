document.addEventListener('DOMContentLoaded', function() { 
    var tabla = function(url_nuevo) {
        $('#btnnuevo').on('click', function(e) {
            e.preventDefault();
            document.location.href=url_nuevo;
        });

        $('[href="editar"]').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                document.location.href='usuario/'+id+'/edit';
            });
        });

        $('[href="eliminar"]').each(function() {
            $(this).on('click', function(e) {
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
                            $('#formdelete').attr('action', 'usuario/'+id).submit();
                        }
                    }
                });
            });
        });
    };
});