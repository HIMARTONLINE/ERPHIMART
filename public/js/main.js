var main = (function (window, undefined) {
    var validar = function(valor, tipo) {
        var res = true;

        switch(tipo) {
            case 'crc':
                var re = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
                
                if(!re.exec(valor)){
                    res = false;
                } 
            break;

            case 'txt':
                if(valor.length == 0) {
                    res = false;
                }
            break;

            case 'mcero':
                if(valor.length == 0 || parseInt(valor) <= 0) {
                    res = false;
                }
            break;
        }

        return res;
    };

    var crear_form_traducciones = function(idiomas, traducciones) {
        idiomas.forEach(function(value) {
            var nav = '<li class="nav-item">\
                           <a href="#'+value.prefijo+'" data-toggle="tab" aria-expanded="false" class="nav-link">\
                               <i class="mdi mdi-home-variant d-md-none d-block"></i>\
                               <span class="d-none d-md-block"><img src="/'+value.icono+'" /></span>\
                           </a>\
                       </li>';

            $('#nav_tabs_idiomas').append(nav);

            var nav_content = $('<div>').addClass('tab-pane').attr('id', value.prefijo);
            $('[data-traducir="true"]').each(function() {
                var elemento = null;

                switch($(this).prop('nodeName')) {
                    case 'INPUT':
                        elemento = $('<input>');
                    break;

                    case 'TEXTAREA':
                        elemento = $('<textarea>');
                    break;
                }

                var identidad = value.prefijo+'_'+$(this).attr('id');
                var etiqueta = $(this).parent().find('label').text().length>0?$(this).parent().find('label').text():$(this).attr('id');
                var label = $('<label>').attr('for', identidad).html(etiqueta);
                elemento.attr({type        : $(this).attr('type'),
                               name        : identidad,
                               id          : identidad,
                               placeholder : $(this).attr('placeholder') }).addClass('form-control');

                var grupo = $('<div>').addClass('form-group');
                var columna = $('<div>').addClass('col-md-3');
                var row = $('<div>').addClass('row');

                nav_content.append(row.append(columna.append(grupo.append(label).append(elemento))));
            });

            $('#tab_content_idiomas').append(nav_content);
        });

        for(key in traducciones) {
            var elemento = '#'+key;
            $(elemento).val(traducciones[key]);
        }
    };

    return {
        alerta : function(msj, tipo) {
            switch(tipo) {
                case 'success':
                    $('#success-alert-modal').find('p.mt-3').html(msj.replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
                    $('#success-alert-modal').modal({ backdrop : 'static', keyboard : false });
                break;
                
                case 'error':
                    $('#danger-alert-modal').find('p.mt-3').html(msj.replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
                    $('#danger-alert-modal').modal({ backdrop : 'static', keyboard : false });
                break;

                case 'warning':
                    $('#warning-alert-modal').find('p.mt-3').html(msj.replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
                    $('#warning-alert-modal').modal({ backdrop : 'static', keyboard : false });
                break;
            }
        },
        crear_form_traducciones : function(idiomas, traducciones) {
            crear_form_traducciones(idiomas, traducciones);
        },
        validar : function(valor, tipo) {
            return validar(valor, tipo);
        },
        patch : function() {
            $.App.activateCondensedSidebar();

            $('.button-menu-mobile').on('click', function(e) {
                $.App.deactivateCondensedSidebar();
            });
        }
    };

})(window, undefined);

(function() {
    // main.patch();
})();