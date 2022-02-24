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
                <form class="mb-4" action="{{ route('export-excel') }}" method="GET">
                @csrf
                    <div class="card-header">
                        <h3 class="card-title">Listado de productos</h3>
                        <input type="hidden" name="categoria" value="{{ $filtro['categoria'] }}">
                        <input type="hidden" name="de_stock" value="{{ $filtro['de_stock'] }}">
                        <input type="hidden" name="a_stock" value="{{ $filtro['a_stock'] }}">
                        <input type="hidden" name="de_precio" value="{{ $filtro['de_precio'] }}">
                        <input type="hidden" name="a_precio" value="{{ $filtro['a_precio'] }}">
                        <input type="hidden" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                        <input type="hidden" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
                        <button type="submit" class="btn btn-danger float-right ml-3"><i class="fa fa-file-excel"></i> Exportar Inventario</button>
                        <a href="{{route('admin.productos.create')}}" class="btn btn-success float-right">
                            <i class="fa fa-plus"></i> Añadir Producto
                        </a>
                    </div>
                </form>
                <br>
                <!-- /.card-header -->
                <div class="card-body">
                    <form id="form-produ" class="mb-4" action="{{ route('filtro-productos') }}" method="GET">
                        @csrf    
                        <div class="row">
                            <div class="col-md-2">
                                <label>Categoría:</label>
                                <select class="form-control" name="categoria" id="categoria">
                                    @if($filtro['categoria'] == 1)
                                        <option value="1" selected>Seleccionar...</option>
                                    @else
                                        <option value="{{ $filtro['categoria'] }}" selected>{{ $filtro['categoria'] }}</option>
                                    @endif
                                    <?php $catego = [];?>
                                    @foreach($categorias['categorias'] as $val)
                                    <?php array_push($catego, $val['nombre']);
                                    ?>
                                    @endforeach
                                    @foreach(array_unique($catego) as $val)
                                        <option value="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>De: (Stock)</label>
                                <select class="form-control" name="de_stock" id="stock">
                                    <option value="{{ $filtro['de_stock'] }}" selected>{{ $filtro['de_stock'] }}</option>
                                    @for($i=1; $i<=200; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>A: (Stock)</label>
                                <select class="form-control" name="a_stock" id="stock">
                                    <option value="{{ $filtro['a_stock'] }}" selected>{{ $filtro['a_stock'] }}</option>
                                    @for($i=1; $i<=200; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>De: (Precio)</label>
                                <select class="form-control" name="de_precio" id="de_precio">
                                    <option value="{{ $filtro['de_precio'] }}" selected>{{ $filtro['de_precio'] }}</option>
                                    @for($i=0; $i<=2000; $i+=20)
                                    <option value="{{ $i . '.00' }}">{{ $i . '.00' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>A: (Precio)</label>
                                <select class="form-control" name="a_precio" id="a_precio">
                                    <option value="{{ $filtro['a_precio'] }}" selected>{{ $filtro['a_precio'] }}</option>
                                    @for($i=0; $i<=2000; $i+=20)
                                    <option value="{{ $i . '.00' }}">{{ $i . '.00' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>De: (Fecha)</label>
                                <input class="form-control" type="date" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                            </div>
                            <div class="col-md-2">
                                <label>A: (Fecha)</label>
                                <input class="form-control" type="date" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
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
                                    <td><img src="https://himart.com.mx/api/images/products/{{ $val['id'] }}/{{ $val['id_image'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="100" height="100" alt=""></td>
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
                                        <form action="{{route('admin.productos.destroy', $val['id'])}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <a href="{{ route('admin.productos.edit', $val['id'])}}" data-id="{{ $val['id'] }}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> <i class="mdi mdi-pencil"></i></a>
                                            <button class="icon-trash" style="border: 0;" type="submit"><i class="mdi mdi-delete"></i></button>               
                                        </form>
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
