@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Ventas</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    
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
                            <form id="consultaMes" action="{{ route('buscar.mes')}}" method="GET"
                                 autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mes">Mes</label>
                                            <select name="mes" id="mes" class="form-control select2" data-toggle="select2"
                                                required="true" data-tipo="txt">
                                                <option>Selecciona mes</option>
                                                <option value="1" {{ $parametros['mes'] == 1 ? 'selected="selected"' : '' }}>Enero</option>
                                                <option value="2" {{ $parametros['mes'] == 2 ? 'selected="selected"' : '' }}>Febrero</option>
                                                <option value="3" {{ $parametros['mes'] == 3 ? 'selected="selected"' : '' }}>Marzo</option>
                                                <option value="4" {{ $parametros['mes'] == 4 ? 'selected="selected"' : '' }}>Abril</option>
                                                <option value="5" {{ $parametros['mes'] == 5 ? 'selected="selected"' : '' }}>Mayo</option>
                                                <option value="6" {{ $parametros['mes'] == 6 ? 'selected="selected"' : '' }}>Junio</option>
                                                <option value="7" {{ $parametros['mes'] == 7 ? 'selected="selected"' : '' }}>Julio</option>
                                                <option value="8" {{ $parametros['mes'] == 8 ? 'selected="selected"' : '' }}>Agosto</option>
                                                <option value="9" {{ $parametros['mes'] == 9 ? 'selected="selected"' : '' }}>Septiembre</option>
                                                <option value="10" {{ $parametros['mes'] == 10 ? 'selected="selected"' : '' }}>Octubre</option>
                                                <option value="11" {{ $parametros['mes'] == 11 ? 'selected="selected"' : '' }}>Noviembre</option>
                                                <option value="12" {{ $parametros['mes'] == 12 ? 'selected="selected"' : '' }}>Diciembre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" data-consultarmes="true" id="validacion"
                                            class="btn btn-secondary margenbtnfloat">Consultar</button>
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
                                            <input type="text" class="form-control date" name="fecha" id="fecha"
                                                required="true" data-tipo="txt" data-toggle="date-picker"
                                                data-cancel-class="btn-warning" value="{{ $parametros['rango'] }}" />
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
            <div class="card">
                <div class="card-header border-0">
                    <h3>Cantidad total de Piezas</h3>
                </div>
                <div class="card-body">
                    <h3 class="mt-3 mb-3">{{ number_format($parametros['totalStock']) }} Piezas</h3>
                </div>
            </div>
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
                            <div class="chartjs-size-monitor-expand">

                            </div>
                            <div class="chartjs-size-monitor-shrink">

                            </div>
                            <canvas id="visitors-chart" height="200" width="764" style="display: block; width: 764px; height: 200px;" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                          <i class="fas fa-square text-primary"></i> This Week
                        </span>
      
                        <span>
                          <i class="fas fa-square text-gray"></i> Last Week
                        </span>
                      </div>
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
{{-- Incluimos los scripts de la tabla de un solo archivo --}}
<script src="{{asset('assets/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
@include('auxiliares.scripts-datatables')

@endpush