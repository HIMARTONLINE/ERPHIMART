@extends('admin.layout.layout')

@section('stylesheet')
    <link href="{{ asset('fullcalendar-scheduler/main.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

<script src="{{asset('fullcalendar-scheduler/main.min.js')}}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/vacaciones.js').'?r='.time() }}"></script>

@section('title')
<h1 class="m-0 text-dark">Solicitar Vaciones</h1>
@endsection

@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Vacaciones</li>
</ol>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form id="nuevo" action="{{ url($parametros['url']) }}" method="patch" enctype="multipart/form-data" autocomplete="off">
                    @csrf

                    @isset($parametros['data']->crew_id)
                    {{ method_field('PATCH') }}
                    @endif
                    <h4 class="header-title">{{ __('vacaciones.subtitulo1') }}</h4>
                    <p class="text-muted font-14">{{ __('vacaciones.descripcion1') }}</p>
                    <hr />
                    <h2 class="dias-pendientes"><span id="vacaciones" data-vacaciones="{{ $parametros['vacaciones'] }}">{{ $parametros['vacaciones'] }}</span> {{ __('vacaciones.diasxtomar') }}</h2>
                    <div id="calendario"></div>
                    
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <input type="hidden" name="dias_solicitados" id="dias_solicitados" required="true" data-tipo="txt" />
                            <button type="button" data-guardar="true" class="btn btn-secondary"><i class="fas fa-save"></i> {{ __('layout.guardar') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">{{ __('vacaciones.subtitulo2') }}</h4>
                <p class="text-muted font-14">{{ __('vacaciones.descripcion2') }}</p>
                <table class="table table-centered mb-0">
                    <thead class="thead-dark">

                        <tr>
                            <th>{{ __('vacaciones.columna1') }}</th>
                            <th>{{ __('vacaciones.columna2') }}</th>
                            <th>{{ __('vacaciones.columna3') }}</th>
                            <th>{{ __('vacaciones.columna4') }}</th>
                            <th>{{ __('vacaciones.columna5') }}</th>
                            {{--<th>{{ __('vacaciones.columna6') }}</th>--}}
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($parametros['solicitudes']))
                            <tr>
                                <td colspan="6" align="center">{{ __('vacaciones.vacio') }}</td>
                            </tr>
                        @endif

                        @foreach($parametros['solicitudes'] as $key=>$value)
                        <tr>
                            <td>{{ $value['created_at'] }}</td>
                            <td>{{ $value['fecha_ingreso'] }}</td>
                            <td align="center">{{ $value['tomados'] }}</td>
                            <td align="center">{{ $value['pendientes'] }}</td>
                            <td align="center"><i class="bi bi-circle-fill {{ $value['autorizacion'] }}"></i></td>
                            {{--<td align="center">
                                @if($value['autorizacion'] == 'text-warning')
                                <button class="btn btn-sm btn-secondary" data-solicitud='@json($value)'><i class="bi bi-trash3"></i></button>
                                @else
                                <button class="btn btn-sm btn-secondary disabled"><i class="bi bi-trash3"></i></button>
                                @endif
                            </td>--}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="solicitudModal" class="modal fade" tabindex="-1" role="dialog">
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
                    <span id="regreso"></span>
                    {{ __('vacaciones.columna2') }}
                </h3>
                <div class="table-responsive mt-3">
                    <table id="diso" class="table table-sm table-centered mb-0 font-14">
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
                <form id="formdelete" action="" method="post">
                    @csrf
                    {{ method_field('delete') }}
                    <input type="hidden" name="registro_id" id="registro_id" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('layout.cerrar') }}</button>
                <button id="descartar" type="button" class="btn btn-warning">{{ __('vacaciones.descartar') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')

    
   {{-- <script src="{{ asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/locale-all.js') }}"></script>--}}

    
@endpush