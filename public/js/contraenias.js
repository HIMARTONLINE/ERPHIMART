$(document).ready(function () {
    
    $('#btn_add').on('click', function() { 

        $('.modal-title').text("Agregar nueva contraseña");
        $("#action_button").val("Agregar");
        $('#password_form')[0].reset();
        $("#action").val("Agregar");
        $("#password_form").attr("action", "passwords/create");
    });
    
    $(document).on('click', '.edit', function() {
        
        let id = $(this).attr('id');

        $("#password_form").attr("action", "passwords/update/" + id);
        $('.modal-title').text("Editar registro");
        $("#action").val("Editar");
        $("#action_button").val("Editar");

        $.ajax({
            url: "/admin/passwords/" + id + "/edit",
            dataType: "json",
            cache:false,
            success: function (html) {

                $('#empresa').val(html.data.empresa);
                $('#servicio').val(html.data.servicio);
                $('#enlace').val(html.data.enlace);
                $('#usuario').val(html.data.usuario);
                $('#estado').val(html.data.estado);
                $('#clave').val(html.data.clave);
                $('#hidden_id').val(html.data.id);
            }
        });
    });
    
    /*new ClipboardJS('.btn');
        $('[data-clipboard-target="#miclave"]').on('click', function(e) {
            $('#mostrarClave').modal('hide');
            toastr['success']('Contraseña copiada');
        });*/

    $(document).on('click', '.clave', function() {

        let id = $(this).attr('id');

        $.ajax({
            url: "/admin/passwords/" + id + "/edit",
            dataType: "json",
            cache:false,
            success: function (html) {
                $('#miclave').val(html.data.clave);
            }
        });
    });

    $('[data-clipboard-target="#miclave"]').on('click', function(e) {

        copyClipboard();
     });

    function copyClipboard(element) {
        var $bridge = $("#miclave").val();
        $("body").append($bridge);
        $bridge.val($(element).text()).select();
        document.execCommand("copy");
        $bridge.remove();
      }
    
});