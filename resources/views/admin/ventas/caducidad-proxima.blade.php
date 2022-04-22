@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Productos por caducar</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Productos con pocas ventas</li>
</ol>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="">
                                        <form class="mb-4" action="{{ route('export-productos') }}" method="GET">
                                        @csrf
                                            <button type="submit" class="btn btn-success float-right ml-3"><i class="fa fa-file-excel"></i> Exportar Productos</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="table_id" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Referencia</th>
                                            <th scope="col">Imagen</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Precio de venta</th>
                                            <th scope="col">Precio de compra</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col">Fecha de expiración</th>
                                            <th scope="col">Fecha inicio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($array_produ as $key => $produ)
                                        <tr>
                                            <td>{{ $array_produ[$key]['id'] }}</td>
                                            <td>{{ $array_produ[$key]['referencia'] }}</td>
                                            <td><img src="https://himart.com.mx/api/images/products/{{ $array_produ[$key]['id'] }}/{{ $array_produ[$key]['id_img'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="100" height="100" /></td>
                                            <td>{{ $array_produ[$key]['nombre'] }}</td>
                                            <td>{{ number_format($array_produ[$key]['precio'], 2) }}</td>
                                            <td>{{ number_format($array_produ[$key]['compra'], 2) }}</td>
                                            <td>{{ $array_produ[$key]['stock'] }}</td>
                                            <td>{{ $array_produ[$key]['expiracion'] }}</td>
                                            <td>{{ $array_produ[$key]['fecha'] }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
<script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/daterangepicker.js') }}"></script>


{{-- Incluimos los scripts de la tabla de un solo archivo --}}
<script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
@include('auxiliares.scripts-datatables')

@endpush