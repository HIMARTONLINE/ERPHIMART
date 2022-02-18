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
                    <a href="#" class="btn btn-danger float-right ml-3">
                        <i class="fa fa-file-excel"></i> Exportar Inventario
                    </a>
                    <a href="{{route('admin.productos.create')}}" class="btn btn-success float-right">
                        <i class="fa fa-plus"></i> Añadir Producto
                    </a>
                </div>
                <br>
                <!-- /.card-header -->
                <div class="card-body">
                    <form id="form-produ" class="mb-4" action="{{ route('filtro-productos') }}" method="GET">
                        @csrf    
                        <div class="row">
                            <div class="col-md-2">
                                <label>Categoría:</label>
                                <select class="form-control" name="categoria" id="categoria">
                                    <option value="1" selected>Seleccionar...</option>
                                    <?php $categorias = [];?>
                                    @foreach($parametros['productos'] as $val)
                                    <?php array_push($categorias, $val['category']);
                                    ?>
                                    @endforeach
            
                                    @foreach(array_unique($categorias) as $val)
                                        <option value="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>De: (Stock)</label>
                                <select class="form-control" name="de_stock" id="stock">
                                    <option value="0" selected>Seleccionar...</option>
                                    @for($i=1; $i<=200; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>A: (Stock)</label>
                                <select class="form-control" name="a_stock" id="stock">
                                    <option value="200" selected>Seleccionar...</option>
                                    @for($i=1; $i<=200; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>De: (Precio)</label>
                                <select class="form-control" name="de_precio" id="de_precio">
                                    <option value="0.00" selected>Seleccionar...</option>
                                    @for($i=0; $i<=2000; $i+=20)
                                    <option value="{{ $i . '.00' }}">{{ $i . '.00' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>A: (Precio)</label>
                                <select class="form-control" name="a_precio" id="a_precio">
                                    <option value="2000.00" selected>Seleccionar...</option>
                                    @for($i=0; $i<=2000; $i+=20)
                                    <option value="{{ $i . '.00' }}">{{ $i . '.00' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>De: (Fecha)</label>
                                <input class="form-control" type="date" name="de_fecha" value="2020-01-01">
                            </div>
                            <div class="col-md-2">
                                <label>A: (Fecha)</label>
                                <input class="form-control" type="date" name="a_fecha" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <input class="btn btn-info" style="margin-top: 32px;" type="submit" name="filtro_produ" value="Buscar">
                            </div>
                        </div>
                    </form>
                    <table id="table_id" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>SKU</th>
                                <th>Nombre</th>
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
                                    <td><img src="https://himart.com.mx/api/images/products/{{ $val['id'] }}/{{ $val['id_image'] }}" width="100" height="100" alt=""></td>
                                    @if ($val['reference'] == [])
                                        <td>Ref. vacío</td>
                                    @else
                                        <td>{{ $val['reference'] }}</td>
                                    @endif
                                    <td>{{ $val['name'] }}</td>
                                    <td>{{ $val['category'] }}</td>
                                    <td>{{ $val['stock'] }}</td>
                      
                                    <td>$ {{ number_format($val['price'], 2) }}</td>
                                    <td>{{ $val['date_upd'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.productos.edit', $val['id'])}}" data-id="{{ $val['id'] }}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> <i class="mdi mdi-pencil"></i></a>
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
