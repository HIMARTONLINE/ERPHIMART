@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Clientes</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Clientes</li>
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
                                    <div class="d-flex justify-content-between">
                                        
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="table_id" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Correo electronico</th>
                                                {{--<th>Fecha de cumpleaños</th>--}}
                                                <th>No. de Pedidos</th>
                                                <th>Total cantidad pagado</th>
                                                {{--<th>Desplegar</th>--}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parametros['parametros'] as $key => $value)
                                                <tr>
                                                    <td>{{ $value['id'] }}</td>
                                                    <td>{{ $value['firstname'] }}</td>
                                                    <td>{{ $value['lastname'] }}</td>
                                                    <td>{{ $value['email'] }}</td>
                                                    {{--<td>{{ $value['birthday'] }}</td>--}}
                                                    <td>{{ $value['quantity'] }}</td>
                                                    <td>$ {{ number_format($value['total_paid'], 2) }}</td>
                                                    {{--<td><a href="" data-id="" class="right fas fa-angle-left" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> </a></td>--}}
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