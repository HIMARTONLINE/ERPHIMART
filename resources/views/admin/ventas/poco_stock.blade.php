@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Productos con poco stock</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Productos con poco stock</li>
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
                                <div class="card-body">
                                    <table id="table_id" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Referencia</th>
                                            <th scope="col">Imagen</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col">Fecha de caducidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($cantidad as $row)
                                            <tr>
                                            <td>{{ $row['id'] }}</td>
                                            <td>{{ $row['referencia'] }}</td>
                                            <td><img src="https://himart.com.mx/api/images/products/{{ $row['id'] }}/{{ $row['id_img'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="100" height="100" /></td>
                                            <td>{{ $row['nombre'] }}</td>
                                            <td>{{ $row['stock'] }}</td>
                                            <td>{{ $row['caducidad'] }}</td>
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