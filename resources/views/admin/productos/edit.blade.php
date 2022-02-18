@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Editar Producto</h1>
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Administración</li>
</ol>
@stop
@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Editar Producto</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            @foreach($parametros['producto'] as $val)
                <?php
                    $id = $val['id'];
                    $nombre = $val['name'];
                    $id_imagen = $val['id_image'];
                    $cantidad = $val['stock'];
                    $sku = $val['reference'];
                    $categoria = $val['category'];
                    $activo = $val['activo'];
                    $precio = $val['price'];
                    $peso = $val['peso'];
                    $descripcion_corta = $val['descripcion_corta'];
                    $descripcion = $val['descripcion'];
                    $precio_compra = $val['precio_compra'];
                ?>
            @endforeach
            <div class="card-body" style="display: block;">
                <form method="PUT" action="{{route('admin.productos.update', $id)}}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <img src="https://himart.com.mx/api/images/products/{{ $id }}/{{ $id_imagen }}" width="200" height="auto" alt="{{ $nombre }}">
                            </div>
                        </div>
                        <div class="col-md-3 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ingrese el nombre del producto"
                                    value="{{ $nombre }}">
                            </div>
                        </div>
                        <div class="col-md-3 text-left-right">
                            <div class="form-group">
                                <label for="codigo">Referencia:</label>
                                <input type="text" name="nombre" class="form-control"
                                    value="{{ $sku }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 text-left-right">
                            <div class="form-group">
                                <label for="categoria_id">Categoría:</label>
                                <select id="categoria_id" name="categoria_id" class="form-control"
                                    required title="Por favor, seleccione la categoría del producto.">
                                    @foreach ($categorias['categorias'] as $categoriaProducto)
                                    <?php
                                        if($categoria == $categoriaProducto['id']){
                                            $id_catego = $categoriaProducto['id'];
                                            $nombre_catego = $categoriaProducto['nombre'];
                                        }
                                    ?>
                                    @endforeach
                                    <option value="{{ $id_catego }}">{{ $nombre_catego }}</option>
                                    @foreach ($categorias['categorias'] as $categoriaProducto)
                                    <option value="{{ $categoriaProducto['id'] }}"
                                        {{ old('categoria_id') == $categoriaProducto['id'] ? 'selected' : '' }}>
                                        {{ $categoriaProducto['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                                <label>Activo</label>
                                <div class="pt-1">
                                    @if($activo != 1)
                                    <input type="checkbox" name="activo" id="activo" data-switch="succes">
                                    <label for="activo" data-on-label="si" data-off-label="no"></label>
                                    @else
                                    <input type="checkbox" name="activo" id="activo" data-switch="succes" checked>
                                    <label for="activo" data-on-label="si" data-off-label="no"></label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Precio sin IVA</label>
                                <input class="form-control" name="sinIVA" type="text" placeholder="$0.00" value="{{ $precio }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php $precio_iva = $precio * .16; $precio_iva = $precio_iva + $precio;?>
                                <label for="">Precio con IVA</label>
                                <input class="form-control" id="" name="conIVA" type="text" placeholder="$0.00" value="{{ $precio_iva }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Cantidad</label>
                                <input class="form-control" type="number" name="cantidad" id="" value="{{ $cantidad }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Peso</label>
                                <input class="form-control" type="text" name="peso" placeholder="00" value="{{ $peso }}">
                            </div>
                        </div>
                        <div class="col-md-1 text-center">
                            <div class="form-group">
                                <label for="">IVA</label>
                                <div>
                                    @if($activo != 1)
                                        <input type="checkbox" name="IVA" id="activo" data-switch="succes">
                                        <label for="activo" data-on-label="si" data-off-label="no"></label>
                                    @else
                                        <input type="checkbox" name="IVA" id="activo" data-switch="succes" checked>
                                        <label for="activo" data-on-label="si" data-off-label="no"></label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="resumen">Descripción corta:</label>
                                <textarea class="form-control" name="resumen" id="" rows="3">{!! $descripcion_corta !!}</textarea>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción completa:</label>
                                <textarea class="form-control" name="descripcion" id="" rows="3">{!! $descripcion !!}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Precio de compra:</label>
                                <input type="text" name="precio_compra" class="form-control"
                                    placeholder="$0.00"
                                    value="{{ $precio_compra }}">
                            </div>
                        </div>
                        <div class="col-md-4 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Clave SAT:</label>
                                <input type="text" name="clabe_sat" class="form-control"
                                    placeholder="Ingrese la clave SAT del producto"
                                    value="{{ $producto->clabe_sat }}">
                            </div>
                        </div>
                        <div class="col-md-4 text-left-right">
                            <div class="form-group">
                                <label for="codigo">Unidad Medida:</label>
                                <input type="text" name="unidad_medida" class="form-control"
                                    placeholder="Ingrese la unidad de medida del producto"
                                    value="{{ $producto->unidad_medida }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Caducidad:</label>
                                <hr />
                                <div class="row">
                                    <div class="col-md-4">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No. de piezas</th>
                                                    <th scope="col">Fecha de caducidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($caducidad as $cad)
                                                <tr>
                                                    <td>{{ $cad->quantity }}</td>
                                                    <?php
                                                        $timestamp = strtotime($cad->expiration_date); 
                                                        $fecha = date("d/m/Y", $timestamp );
                                                    ?>
                                                    <td>{{ $fecha }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="nombre">No. de piezas:</label>
                                        <input type="text" name="num_cad[]" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nombre">Fecha de caducidad:</label>
                                        <input type="date" name="fecha_cad[]" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div id="caducidad-produ" class="form-group"></div>
                            <div class="form-group">
                                <button type="button" id="agregar-caducidad" class="btn btn-success"><i class="fa fa-plus"></i> Agregar caducidad</button>
                            </div>
                            <hr />
                        </div>
                    </div>
                    <button class="btn btn-primary">Crear Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
