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
                        <input type="hidden" name="venta" value="{{ $filtro['venta'] }}">
                        <input type="hidden" name="de_precio" value="{{ $filtro['de_precio'] }}">
                        <input type="hidden" name="a_precio" value="{{ $filtro['a_precio'] }}">
                        <input type="hidden" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                        <input type="hidden" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary float-right ml-3" data-toggle="modal" data-target="#exampleModalCenter">
                            <i class="fa fa-file-excel"></i>
                            Importar Inventario
                        </button>
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
                                        <option value="{{ $filtro['id_categoria'] .'-'. $filtro['categoria'] }}" selected>{{ $filtro['categoria'] }}</option>
                                    @endif
                                    <?php $catego = [];?>
                                    @foreach($categorias['categorias'] as $val)
                                    <?php 
                                        $id_cat = $val['id'];
                                        $catego[$id_cat] = $val['nombre'];
                                    ?>
                                    @endforeach
                                    @foreach(array_unique($catego) as $key => $val)
                                        <option value="{{ $key .'-'. $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Subcategoría:</label>
                                <select class="form-control" name="sub_categoria" id="sub_categoria">
                                    @if($filtro['sub_categoria'] == 1)
                                        <option value="1" selected>Seleccionar...</option>
                                    @else
                                        <option value="{{ $filtro['sub_categoria'] }}" selected>{{ $filtro['sub_categoria'] }}</option>
                                    @endif
                                    <?php $catego = [];?>
                                    @foreach($sub_categorias['sub_categorias'] as $val)
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
                            <div class="col-md-1">
                                <label>De: (Fecha)</label>
                                <input class="form-control" type="date" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                            </div>
                            <div class="col-md-1">
                                <label>A: (Fecha)</label>
                                <input class="form-control" type="date" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
                            </div>
                            <div class="col-md-2">
                                <input class="btn btn-info" style="margin-top: 32px;" type="submit" name="filtro_produ" value="Buscar">&nbsp;
                                <a href="{{ url('admin/productos') }}" style="text-decoration:underline;vertical-align: bottom;font-weight: bold;">Borrar filtro</a>
                            </div>
                        </div>
                    </form>
                    <table id="table_id" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>SKU</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Total compras</th>
                                <th>Total ventas</th>
                                <th>Stock</th>
                                <th>Merma</th>
                                <th>Precio de venta</th>
                                <th>Precio de compra</th>
                                <th>Fecha de caducidad</th>
                                <th>NO. de fechas de caducidad</th>
                                <th>Fecha</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($parametros['productos'] as $ke => $val)

                            <tr>
                                @if ($val['state'] != "0")
                                    <td>{{ $val['category'] }}</td>
                                    @if ($val['reference'] == [])
                                        <td>Ref. vacío</td>
                                    @else
                                        <td>{{ $val['reference'] }}</td>
                                    @endif
                                    <td><img src="https://himart.com.mx/api/images/products/{{ $val['id'] }}/{{ $val['id_image'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="100" height="100" alt=""></td>
                                    <td>{{ $val['name'] }}</td>
                                    <td>{{ $val['total_compras'] }}</td>
                                    <td>{{ $val['total_piezas'] }}</td>
                                    <td>{{ $val['stock'] }}</td>
                                    <td>{{ $val['merma'] }}</td>
                                    <td>$ {{ number_format($val['price'], 2) }}</td>
                                    <td>$ {{ number_format($val['compra'], 2) }}</td>
                                    <td>{{ $val['caducidad'] }}</td>
                                    <td>{{ $val['totalRegis'] }}</td>
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
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="mb-4" action="{{ route('import-excel') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Sube un archivo de excel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="file" name="archivo" accept=".xlsx, .xls">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Importar datos</button>
      </div>
    </div>
    </form>
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
