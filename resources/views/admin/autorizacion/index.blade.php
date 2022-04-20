@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Autorizaciones</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Autorizaciones</li>
</ol>
@stop
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">{{ __('autorizaciones.encabezado1') }}</h4>
                    <p class="text-muted font-14">
                        Lista de solicitudes de vacaciones por atender.
                    </p>
                    <table id="datatable-buttons" class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('autorizaciones.col-gra-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-2') }}</th>
                                <th>{{ __('autorizaciones.col-vac-3') }}</th>
                                <th>{{ __('autorizaciones.col-vac-4') }}</th>
                                <th>{{ __('autorizaciones.col-vac-5') }}</th>
                                <th>{{ __('autorizaciones.col-vac-6') }}</th>
                                <th>{{ __('layout.accion') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($parametros['vacaciones']))
                                <tr>
                                    <td colspan="8" align="center">{{ __('autorizaciones.vacio') }}</td>
                                </tr>
                            @endif

                            @foreach($parametros['vacaciones'] as $key=>$value)
                            <tr>
                                <td class="table-user">
                                    <img src="{{ asset($value['foto']) }}" alt="table-user" class="mr-2 rounded-circle" />
                                    {{ $value['name'] }}
                                </td>
                                <td>{{ $value['solicitado'] }}</td>
                                <td>{{ $value['inicio'] }}</td>
                                <td>{{ $value['reingreso'] }}</td>
                                <td align="center">{{ $value['tomados'] }}</td>
                                <td align="center">{{ $value['pendientes'] }}</td>
                                <td align="center"><i class="bi bi-circle-fill {{$value['estatus'] }}"></i></td>
                                <td align="center">
                                    
                                    <button class="btn btn-sm btn-success" data-autoriza='@json($value)'><i class="bi bi-person-check-fill"></i></button>
                                    
                                    <button class="btn btn-sm btn-secondary" data-solicitud='@json($value)'><i class="bi bi-search"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Caja de solicitid de permisos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">{{ __('permisos.encabezado1') }} </h4>
                    <p class="text-muted font-14">
                        Lista de solicitudes de permiso por atender.
                    </p>
                   <!-- <div class="col-md-12 clearfix">
                        <div class="float-right text-primary" id="reloj" name="reloj"></div>
                    </div>-->
                    <table id="datatable-buttons" class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th >{{ __('autorizaciones.col-gra-1') }}</th> <!-- usuario -->
                                <th style="text-align: center">{{ __('autorizaciones.col-vac-1') }}</th> <!-- fecha solicitud -->
                                <th style="text-align: center">{{ __('autorizaciones.col-vac-2') }}</th> <!-- inicio -->
                                <th style="text-align: center">{{ __('autorizaciones.col-vac-3') }}</th> <!-- reingreso -->
                                <th style="text-align: center">{{ __('autorizaciones.col-per-2') }}</th> <!-- días u horas solicitados, requiere de una condición para mostrar el campo -->
                                <th style="text-align: center">{{ __('autorizaciones.col-per-1') }}</th> <!-- pendientes, cambiar por motivo del permiso -->
                                <th style="text-align: center">{{ __('autorizaciones.col-vac-6') }}</th> <!-- Estatus -->
                                <th style="text-align: center">{{ __('layout.accion') }}</th>            <!--Accion -->
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($parametros['permisos']))
                                <tr>
                                    <td colspan="8" style="text-align: center">{{ __('autorizaciones.vacio') }}</td>
                                </tr>
                            @endif

                            @foreach($parametros['permisos'] as $key=>$value)
                            <tr>
                                <td class="table-user" >
                                    <img src="{{ asset($value['foto']) }}" alt="table-user" class="mr-2 rounded-circle" />
                                    {{ $value['name'] }}
                                </td>
                                <td style="text-align: center">{{ $value['created_at'] }}</td>
                                <td style="text-align: center">{{ $value['inicio'] }}</td>
                                <td style="text-align: center">{{ $value['reingreso'] }}</td>
                                @if($value['dias'] == 0 || $value['dias'] == null)
                                    <td style="text-align: center">{{ $value['horas'] }} hrs</td>
                                @else
                                    <td style="text-align: center">{{ $value['dias'] }} dia's</td>
                                @endif
                                <td style="text-align: center">{{ $value['motivo'] }}</td>
                                <td style="text-align: center"><i class="bi bi-circle-fill {{ $value['estatus'] }}"></i></td>
                                <td style="text-align: center">                                  
                                    <button class="btn btn-sm btn-success" data-permiso='@json($value)'><i class="bi bi-person-check-fill"></i></button>
                                    <!--<button class="btn btn-sm btn-secondary" data-premisos='@/json($value)'><i class="mdi mdi-magnify"></i></button>-->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de caja de permisos -->

    <!-- Formularios de vista de días pedidos y autorización de vacaciones -->
    <div id="solicitudModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SOLICITUD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('layout.cerrar') }}<">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="regreso">
                        <span id="regreso"></span>
                        REINGRESO
                    </h3>
                    <div class="table-responsive mt-3">
                        <table id="diso" class="table table-sm table-centered mb-0 font-14">
                            <thead class="thead-light">
                                <tr>
                                    <th>DÍA</th>
                                    <th>{{ __('vacaciones.encabezado1') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="autorizaVacacionesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('autorizaciones.col-gra-2') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('layout.cerrar') }}<">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        {!! __('autorizaciones.autoriza') !!}
                    </p>
                    <table id="autorizaTable" class="table table-centered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('autorizaciones.col-gra-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-3') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <form id="formautoriza" action="" method="post">
                        @csrf
                        {{ method_field('post') }}
                        <input type="hidden" name="autorizacion" id="autorizacion" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-left" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                    <button type="button" id="noautorizo" class="btn btn-danger">{{ __('autorizaciones.no') }}</button>
                    <button type="button" id="siautorizo" class="btn btn-success">{{ __('autorizaciones.si') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Formularios de vista de días solicitados y autorizaciones de Permisos -->
    <div id="solicitud" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('vacaciones.columna1') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('layout.cerrar') }}<">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="regreso">
                        <span id="regresoPermisos"></span>
                        {{ __('vacaciones.columna2') }}
                    </h3>
                    <div class="table-responsive mt-3">
                        <table id="disoPermisos" class="table table-sm table-centered mb-0 font-14">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('vacaciones.encabezado3') }}</th>
                                    <th>{{ __('vacaciones.encabezado1') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                </div>
            </div>
        </div>
    </div>

     <div id="autorizaPermisos" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('autorizaciones.col-gra-2') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('layout.cerrar') }}<">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        {!! __('autorizaciones.autorizar') !!}
                    </p>
                    <table id="autorizaTable2" class="table table-centered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('autorizaciones.col-gra-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-1') }}</th>
                                <th>{{ __('autorizaciones.col-vac-3') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <form id="formpermisos" action="" method="post">
                        @csrf
                        
                        <input type="hidden" name="autorizacionpermisos" id="autorizacionpermisos" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-left" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                    <button type="button" id="noautorizo2" class="btn btn-danger">{{ __('autorizaciones.no') }}</button>
                    <button type="button" id="siautorizo2" class="btn btn-success">{{ __('autorizaciones.si') }}</button>
                </div>
            </div>
        </div>
    </div>

    <form id="formdelete" action="" method="post">
        @csrf
        {{ method_field('delete') }}
        <input type="hidden" name="registro_id" id="registro_id" />
    </form>
@stop
@push('scripts')
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    {{--<script src="{{ asset('assets/js/vendor/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/locale-all.js') }}"></script>--}}
    <script src="{{asset('js/autorizaciones.js')}}"></script>
    <script>
        (function($) {
            var etiquetas = {apunto       : '{{ __('layout.apunto') }}',
                             si           : '{{ __('layout.si') }}',
                             no           : '{{ __('layout.no') }}',
                             agregar      : '{{ __('layout.agregar') }}',
                             buscar       : '{{ __('layout.buscar') }}',
                             mostrando    : '{{ __('layout.mostrando') }}',
                             totales      : '{{ __('layout.totales') }}',
                             paginas      : '{{ __('layout.paginas') }}',
                             vacio        : '{{ __('layout.vacio') }}',
                             noencontrado : '{{ __('layout.noencontrado') }}',};

            autorizaciones.init(etiquetas);

            @if ($message = Session::get('success'))
            main.alerta('{{ $message }}', 'success');
            @endif

            @if ($message = Session::get('error'))
            main.alerta('{{ $message }}', 'error');
            @endif

            @if ($message = Session::get('warning'))
            main.alerta('{{ $message }}', 'warning');
            @endif
        })(jQuery);
    </script>    
@endpush