@extends('admin.layout.layout')

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
    {{--<script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/locale-all.js') }}"></script>
    <script src="{{ asset('chart.js/dist/chart.min.js')}}"></script>
    <script src="{{ asset('js/reportes.js').'?r='.time() }}"></script>--}}
    {{-- Incluimos los scripts de la tabla de un solo archivo --}}
    <script src="{{asset('fullcalendar-scheduler/main.js')}}"></script>
    <script src="{{ asset('chart.js/dist/chart.min.js')}}"></script>
    <script src="{{ asset('js/reportes.js').'?r='.time() }}"></script>

@endpush