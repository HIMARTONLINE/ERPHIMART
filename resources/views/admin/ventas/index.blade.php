@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Ventas</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Ventas</li>
</ol>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        Obten el reporte de ventas de acuerdo al mes o al rango de fecha seleccionada
                    </div>
                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#meses" data-toggle="tab"
                                aria-expanded="{{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'true' : 'false' }}{{ $parametros['mes'] != '' ? 'true' : '' }}"
                                class="nav-link {{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'active' : '' }}{{ $parametros['mes'] != '' ? 'active' : '' }}">
                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                <span class="d-none d-md-block">Meses</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#rango" data-toggle="tab"
                                aria-expanded="{{ $parametros['rango'] == '' ? 'false' : 'true' }}"
                                class="nav-link {{ $parametros['rango'] == '' ? '' : 'active' }}">
                                <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                                <span class="d-none d-md-block">Rango</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane {{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'show active' : '' }}{{ $parametros['mes'] != '' ? 'show active' : '' }}"
                            id="meses">
                            <form id="consultaMes" action="{{ route('buscar.mes')}}" method="post"
                                 autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mes">Mes</label>
                                            <select name="mes" id="mes" class="form-control select2" data-toggle="select2"
                                                required="true" data-tipo="txt">
                                                <option value="00" >Selecciona mes</option>
                                                @foreach($parametros['meses'] as $key => $value)
                                                        @if($key + 1 < 10)
                                                            <option value="0{{ $key + 1 }}">{{ $value }}</option>
                                                        @else
                                                            <option value="{{ $key + 1 }}">{{ $value }}</option>
                                                        @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" data-consultarmes="true" id="validacion"
                                            class="btn btn-secondary ">Consultar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane {{ $parametros['rango'] == '' ? '' : 'show active' }}" id="rango">
                            <form id="consulta" action="{{ route('buscar.mes') }}" method="post"
                                enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha">Rango</label>
                                            <input id="reportrange" type="text" class="form-control" name="rango" id="rango" required="true" value="{{ $parametros['rango'] }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" data-consultar="true" class="btn btn-secondary margenbtnfloat"><i class="mdi mdi-database-search"></i>Consultar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3>Venta Total</h3>
                        {{-- aqui puede ir otra etiqueta que se mostrara en la esquina superior derecha--}}
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <h3 class="mt-3 mb-3">${{ number_format($parametros['totalVentaOrden'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header border-0">
                    <h3>Valor total compra de productos</h3>
                </div>
                <div class="card-body">
                    <h3 class="mt-3 mb-3">${{ number_format($parametros['totalCompra'], 2) }}</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-header border-0">
                    <h3>Valor total Venta de productos</h3>
                </div>
                <div class="card-body">
                    <h3 class="mt-3 mb-3">${{ number_format($parametros['totalVentaProdu'], 2) }}</h3>
                </div>
            </div>
            {{--<div class="card">
                <div class="card-header border-0">
                    <h3>Cantidad total de Piezas</h3>
                </div>
                <div class="card-body">
                    <h3 class="mt-3 mb-3">{{ number_format($parametros['totalStock']) }} Piezas</h3>
                </div>
            </div>--}}
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex">

                    </div>
                    <div class="position-relative mb-4">
                        <div class="chartjs-size-monitor">
                            <input id="datosGrafica" type="hidden" value='@json($parametros['rangoGra'])'>
                            <canvas id="graficaVentas" height="200" width="764" style="display: block; width: 764px; height: 200px;" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                    {{--<div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                          <i class="fas fa-square text-primary"></i> This Week
                        </span>
      
                        <span>
                          <i class="fas fa-square text-gray"></i> Last Week
                        </span>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@stop
@push('styles')
{{-- Incluimos los links del diseño de la tabla de un solo archivo --}}
@include('auxiliares.design-datatables')
@endpush
@push('scripts')
<script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/daterangepicker.js') }}"></script>


{{-- Incluimos los scripts de la tabla de un solo archivo --}}
<script src="{{asset('assets/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="{{ asset('js/reportes.js').'?r='.time() }}"></script>
@include('auxiliares.scripts-datatables')

@endpush