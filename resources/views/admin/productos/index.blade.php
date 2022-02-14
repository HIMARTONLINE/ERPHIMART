@extends('admin.layout.layout')

@section('title')
<h1 class="m-0 text-dark">Productos</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Administración</li>
</ol>
@stop
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de productos</h3>
                    <a href="{{route('admin.productos.create')}}" class="btn btn-secondary float-right">
                        <i class="fa fa-plus"></i> Añadir Producto
                    </a>

                </div>
                <br>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table_id" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>SKU</th>
                                <th>Categoria</th>
                                <th>Stock</th>
                                <th>Precio de venta</th>
                                <th>Fecha</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($parametros['productos'] as $ke => $val)

                            <tr>
                                @if ($val['state'] != "0")
                                    <td>{{ $val['name'] }}</td>
                                    @if ($val['reference'] == [])
                                        <td>Ref. vacío</td>
                                    @else
                                        <td>{{ $val['reference'] }}</td>
                                    @endif
                                    <td>{{ $val['category'] }}</td>
                                    <td>{{ $val['stock'] }}</td>
                      
                                    <td>$ {{ number_format($val['price'], 2) }}</td>
                                    <td>{{ $val['date_upd'] }}</td>
                                    <td>
                                        <a href="editar" data-id="{{ $val['id'] }}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> <i class="mdi mdi-pencil"></i></a>
                                        <a href="eliminar" data-id="{{ $val['id'] }}" class="icon-trash" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"> <i class="mdi mdi-delete"></i></a>
                                       {{-- @if($val['activo'] != 1)
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                                <label class="custom-control-label" for="customSwitch1"></label>
                                            </div>
                                        @else
                                            <div class="custom-control custom-switch">
                                                <input class="custom-control-input" type="checkbox" id="customSwitch1" checked>
                                                <label class="custom-control-label" for="customSwitch1"></label>
                                            </div>
                                        @endif --}}
                                    </td>
                                @endif
                            @endforeach
                          
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
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
