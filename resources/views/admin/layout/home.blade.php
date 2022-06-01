@extends('admin.layout.layout')

<script src="{{asset('fullcalendar-scheduler/main.min.js')}}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/calendario.js').'?r='.time() }}"></script>

@section('title')
<h1 class="m-0 text-dark">Inicio</h1>
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Administración</li>
</ol>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        Obten el Top Teen de los productos más vendidos del mes o rango y las ordenes realizadas en el mes
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
                            <form id="consultaMes" action="{{ route('home')}}" method="GET"
                                autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mes">Mes</label>
                                            <select name="mes" id="mes" class="form-control select2" data-toggle="select2"
                                                required="true" data-tipo="txt">
                                                <option value="00">Selecciona mes</option>
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
                            <form id="consulta" action="{{ route('home') }}" method="GET"
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
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Calendario</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <div class="chartjs-size-monitor">
                            <div id="calendario"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3>Venta Total</h3>
                        <!--aqui puede ir otra etiqueta que se mostrara en la esquina superior derecha-->
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <h3 class="mt-3 mb-3">${{ number_format($parametros['totalVentaOrden'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
        <div class="col-lg-6">
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
                            {{--<input id="productoGrafica" type="text" value='@json($parametros['CantidadVendida'])'>--}}
                            <canvas id="graficaVentas" height="200" width="764" style="display: block; width: 764px; height: 200px;" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header border-0">
                    <h3>Top 10 de productos más vendidos</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center;">
                        <img id="topImg" src="{{$parametros['CantidadVendida'][0]['imagen']}}" width="15%" alt="" srcset="">
                        <br>
                        <label for="">Nombre: <span id="topNombre">{{$parametros['CantidadVendida'][0]['nombre']}}</span></label>
                        <br>
                        <label>Vendidos: <span id="topVendidos">{{$parametros['CantidadVendida'][0]['cantidad']}}</span> piezas</label>
                    </div>
                    <div class="list-group" style="overflow-y: auto; height: 180px;">
                        @foreach($parametros['CantidadVendida'] as $key => $value) 
                            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <img src="{{$value['imagen']}}" width="15%" alt="" srcset="">
                                    <h5 class="mb-1">{{$value['nombre']}}</h5>
                                    <small>{{$value['cantidad']}}</small>
                                </div>
                            </a>
                        @endforeach  
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('page/assets/js/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('page/assets/js/daterangepicker/daterangepicker.js') }}"></script>

    {{-- Incluimos los scripts de la tabla de un solo archivo --}}
    <script src="{{asset('assets/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>

    <script src="{{ asset('chart.js/dist/chart.min.js')}}"></script>
    <script src="{{ asset('js/reportes.js').'?r='.time() }}"></script>

@endpush