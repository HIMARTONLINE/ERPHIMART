@extends('admin.layout.layout')

@section('title')
<h1 class="m-0 text-dark">Inicio</h1>
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Asistencias</li>
</ol>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    Recopilación de registros de entrada y salida de los empleados
                </div>
                <ul class="nav nav-tabs nav-bordered mb-2">
                    <li class="nav-item">
                        <a href="#entradas" data-toggle="tab"
                        aria-expanded="{{ $parametros['entrada'] == '' && $parametros['comida'] == '' ? 'true' : 'false' }}{{ $parametros['entrada'] != '' ? 'true' : ''}}"
                        class="nav-link {{ $parametros['entrada'] == '' && $parametros['comida'] == '' ? 'active' : ''}}{{ $parametros['entrada'] != '' ? 'active' : ''}}">
                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                            <span class="d-none d-md-block">Registro de entrada</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#comida" data-toggle="tab"
                        aria-expanded="{{ $parametros['comida'] == '' ? 'false' : 'true'}}" 
                        class="nav-link {{ $parametros['comida'] == '' ? '' : active }}">
                            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                            <span class="d-none d-md-block">Registro de comedor</span>
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane {{ $parametros['entrada'] == '' && $parametros['comida'] == '' ? 'show active' : '' }}{{ $parametros['entrada'] != '' ? 'show active' : ''}}"
                        id="entradas">
                        <form id="consultaEntrada" action="{{ route('admin.asistencias.index') }}" method="GET"
                            enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="entrada">{{ __('reportes.rango') }} de entrada</label>
                                        <input type="text" class="form-control date" name="entrada" id="entrada"
                                        required="true" data-tipo="txt" data-toggle="date-picker" 
                                        data-cancel-class="btn-warning" value="{{ $parametros['entrada'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" data-consultarentrada="true" class="btn btn-secondary margenbtnfloat"><i class="mdi mdi-database-search"></i> {{ __('layout.consultar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane {{ $parametros['comida'] == '' ? '' : 'show active' }}" id="comida">
                        <form id="consulta" action="{{ route('admin.asistencias.index') }}" method="GET"
                            enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fechaComida">{{ __('reportes.rango') }} de comida</label>
                                        <input type="text" class="form-control date" name="fechaComida" id="fechaComida" 
                                        required="true" data-tipo="txt" data-toggle="date-picker" data-cancel-class="btn-warning" value="{{ $parametros['comida'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" data-consultarcomida="true" class="btn btn-secondary margenbtnfloat"><i class="mdi mdi-database-search"></i>  {{ __('layout.consultar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table id="table_id" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <td>{{ __('reportes.nombre') }}</td>
                                @foreach ($parametros['encabezados'] as $key => $value)
                                <td align="center">{{ $value }}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parametros['registros'] as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                @foreach ($value as $k => $v)
                                <td align="center">
                                    @if($v['estatus'] != '0')
                                        @switch($v['estatus'])
                                            @case(1)
                                                <i class="fas fa-smile-beam atiempo" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i> 
                                            @break
                                            @case(-1)
                                                <i class="fas fa-sad-tear tarde" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i>
                                            @break
                                            @default
                                                 <i class="fas fa-dizzy falta"></i>
                                        @endswitch
                                    @else   
                                        @if($v['estatus'] == '0')
                                            @if($v['hora'] > '14:10')
                                                <i class="fas fa-sad-tear tarde" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i>
                                                <i class="fas fa-sad-tear tarde" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i>
                                            @else
                                                <i class="fas fa-smile-beam atiempo" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i>
                                                <i class="fas fa-smile-beam atiempo" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $v['hora'] }}"></i>
                                            @endif
                                        @endif

                                    @endif
                                </td>

                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
@push('styles')
{{-- Incluimos los links del diseño de la tabla de un solo archivo --}}
<link  rel="stylesheet" type="text/css" media="all" href="{{ asset('css/daterangepicker.css') }}">
@include('auxiliares.design-datatables')
@endpush
@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/asistencias.js') }}"></script>
@include('auxiliares.scripts-datatables')

@endpush