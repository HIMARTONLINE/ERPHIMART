<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>Presencia | Hi-MART ONLINE</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Módulo checador de asistencias" name="description" />
        <meta content="JN" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">
        <link rel="apple-touch-startup-image" href="{{ asset('assets/images/logo.png') }}">
        <!-- App css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/app-modern.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
        <link href="{{ asset('css/overridepresencia.css').'?r='.time() }}" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="tabla">
            <div class="celda">
                <div class="usuarios">
                </div>
                <div class="logo">
                    <img src="{{ asset('assets/images/logo.png') }}" id="despierto" style="margin-left: -100%;"/>
                    {{--<img src="{{ asset('img/sleeping.png') }}" id="dormido" />--}}
                </div>

                <form id="checador" method="post" autocomplete="off">
                    @csrf
                    <input type="text" class="bigint" name="serial" id="serial" autofocus="true" />
                </form>
            </div>
        </div>
        
        <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
        <script src="{{ asset('assets/js/app.min.js') }}"></script>
        <script src="{{ asset('js/presencia.js').'?r='.time() }}"></script>
        <script>
            (function($) {
                presencia.form({!! json_encode($parametros['personal']) !!}, '{{ $parametros['entrada'] }}');
            })(jQuery);
        </script>
    </body>
</html>