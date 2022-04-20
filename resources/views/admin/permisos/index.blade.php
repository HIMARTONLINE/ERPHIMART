@extends('admin.layout.layout')

@section('stylesheet')
    <link href="{{ asset('fullcalendar-scheduler/main.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

<script src="{{asset('fullcalendar-scheduler/main.min.js')}}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/permisos.js').'?r='.time() }}"></script>

@section('title')
<h1 class="m-0 text-dark">Solicitar Permiso</h1>
@endsection

@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Permisos</li>
</ol>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        @isset($parametros['data']->crew_id)
                        {{ method_field('patch') }}
                        @endif
                        <h4 class="header-title">CREAR NUEVA SOLICITUD</h4>
                        <p class="text-muted font-14">Completa el siguiente formulario para envíar tu solicitud de permiso</p>
                    </div>

                    <ul class="nav nav-tabs nav-bordered mb-2">
                        <li class="nav-item">
                            <a id="solicitudDias" href="#dias" data-toggle="tab" class="nav-link active">
                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                <span class="d-none d-md-block">Solicitar días</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a id="solicitudHoras" href="#horas" data-toggle="tab" class="nav-link">
                                <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                                <span class="d-none d-md-block">Solicitar horas</span>
                            </a>
                        </li>
                    </ul>
                    <form style="display: block" id="nuevo" action="{{route('admin.permisos.getregistros')}}" method="post" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                                                     
                            <div id="calendario"></div>
                        
                        <div class="from-group">
                            <label for="motivo">Motivo</label>
                            <textarea name="motivo2" id="motivo2" cols="30" rows="4" class="form-control" required="true" placeholder="Redacte brevemente la rázon por la que se ausenta" data-tipo="txt" data-traducir="false"></textarea>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <!--Botón para mandar los datos del permiso-->
                                <button id="btnEnviar" type="button" data-guardar="true" class="btn btn-secondary"><i class="fas fa-save"></i> Guardar </button>
                                <input type="hidden" name="dias_solicitados" id="dias_solicitados" required="true" data-tipo="txt" />
                            </div>
                        </div>
                    </form>
                    <form style="display: none" id="nuevo2" action="{{route('admin.permisos.enviar')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha">Fecha</label>
                                    <input type="datetime-local" name="fecha" id="fecha" class="form-control" placeholder="dd/mm/YYYY" data-tipo="txt" data-traducir="false" />
                                    <span class="font-13 text-muted">ej. "DD/MM/YYYY"</span>
                                </div>  
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="horas">Horas</label>
                                    <input type="number" min="1" name="horas" id="horas" class="form-control" placeholder="Total de horas de ausencia" data-tipo="txt" data-traducir="false" />
                                </div>
                            </div>
                        </div>
                        <div class="from-group">
                            <label for="motivo">Motivo</label>
                            <textarea name="motivo" id="motivo" cols="30" rows="4" class="form-control" placeholder="Redacte brevemente la rázon por la que se ausenta" data-tipo="txt" data-traducir="false"></textarea>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <!--Botón para mandar los datos del permiso-->
                                <button id="btnEnviar2" type="button" data-guardar="true" class="btn btn-secondary"><i class="fas fa-save"></i> Guardar </button>
                            </div>   
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">SOLICITUDES ENVIADAS</h4>
                    <p class="text-muted font-14">Historial de solicitudes</p>
                    <table class="table table-centered mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center">Fecha de solicitud</th>  <!-- SOLICITUD -->
                                <th style="text-align: center">Fecha de salida</th>
                                <th style="text-align: center">Reingreso</th>  <!-- REINGRESO -->
                                <th style="text-align: center">Solicitados</th>  <!-- SOLICITADOS -->
                                
                                <th>Estatus</th>  <!-- ESTATUS -->
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($parametros['solicitudes']))
                                <tr>
                                    <td colspan="6" style="text-align: center">No se encontrarón solicitudes</td>
                                </tr>
                            @endif

                            @foreach($parametros['solicitudes'] as $key=>$value)
                            <tr>
                                <td style="text-align: center">{{ $value['created_at'] }}</td>
                                <!--hacer la comparacion en caso de ser días u horas-->
                                <td style="text-align: center">{{ $value['salida'] }}</td>
                                <td style="text-align: center">{{ $value['reingreso'] }}</td>
                                <!--Realizar otra comparacion en caso de ser horas tomadas o días-->
                                @if (empty($value['horas']) || $value['horas'] == 0)
                                    <td style="text-align: center">{{ $value['dias'] }} dia's</td>
                                @else
                                    <td style="text-align: center">{{ $value['horas'] }} hrs</td>
                                @endif
                                
                                <td style="text-align: center"><i class="bi bi-circle-fill {{$value['autorizacion'] }}"></i></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--<div id="solicitudModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('permisos.columna1') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('layout.cerrar') }}<">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="regreso">
                        <span id="regreso"></span>
                        {{ __('permisos.columna2') }}
                    </h3>
                    <div class="table-responsive mt-3">
                        <table id="diso" class="table table-sm table-centered mb-0 font-14">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('permisos.encabezado3') }}</th>
                                    <th>{{ __('permisos.encabezado1') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <form id="formdelete" action="" method="post">
                        @csrf
                        {{ method_field('delete') }}
                        <input type="hidden" name="registro_id" id="registro_id" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                    <button id="descartar" type="button" class="btn btn-warning">{{ __('permisos.descartar') }}</button>
                </div>
            </div>
        </div>
    </div>--}}

@stop

@push('scripts')

    
   {{-- <script src="{{ asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/locale-all.js') }}"></script>--}}
    
    <script>
        $("#solicitudDias").on('click', function() {
            $("#nuevo2").css('display', 'none');
            $("#nuevo").css('display', 'block');
        });
    
        $("#solicitudHoras").on('click', function() {
            $("#nuevo").css('display', 'none');
            $("#nuevo2").css('display', 'block');
        });
    </script>

    
@endpush