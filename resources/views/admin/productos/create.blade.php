@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Crear Producto</h1>
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
                <h3 class="card-title">Nuevo Producto</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <form method="POST" action="{{route('admin.productos.store')}}">
                    @csrf

                    <div class="row">
                        <div class="col-md-3 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ingrese el nombre del producto"
                                    value="{{old('nombre')}}">
                            </div>
                        </div>
                        <div class="col-md-3 text-left-right">
                            <div class="form-group">
                                <label for="codigo">Referencia:</label>
                                <input type="text" name="codigo" class="form-control"
                                    placeholder="Ingrese el codigo del producto" required
                                   minlegth="1" maxlength="20"
                                    title="Solo se permiten letras. Tamaño mínimo: 2. Tamaño máximo: 20"
                                    value="{{old('codigo')}}">
                            </div>
                        </div>

                            <div class="col-md-3 text-left-right">
                                <div class="form-group">
                                    <label for="categoria_id">Categoría:</label>
                                    <select id="categoria_id" name="categoria_id" class="form-control"
                                        required title="Por favor, seleccione la categoría del producto.">
                                        <option value="">Elija una categoría</option>
                                        @foreach ($parametros['categorias'] as $categoriaProducto)
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
                                    <input type="checkbox" name="activo" id="activo" data-switch="succes">
                                    <label for="activo" data-on-label="si" data-off-label="no"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Precio sin IVA</label>
                                <input class="form-control" name="sinIVA" type="text" placeholder="$0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Precio con IVA</label>
                                <input class="form-control" id="" name="conIVA" type="text" placeholder="$0.00" >
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="">Cantidad</label>
                                <input class="form-control" type="number" name="cantidad" id="" value="0">
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                                <label for="">IVA</label>
                                <div>
                                    <input type="checkbox" name="IVA" id="activo" data-switch="succes">
                                    <label for="activo" data-on-label="si" data-off-label="no"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="resumen">Descripción corta:</label>
                                <textarea class="form-control" name="resumen" id="" rows="3"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción completa:</label>
                                <textarea class="form-control" name="descripcion" id="" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Clave SAT:</label>
                                <input type="text" name="clabe_sat" class="form-control"
                                    placeholder="Ingrese la clave SAT del producto" required
                                    value="{{old('clabe_sat')}}">
                            </div>
                        </div>
                        <div class="col-md-6 text-left-right">
                            <div class="form-group">
                                <label for="codigo">Unidad Medida:</label>
                                <input type="text" name="unidad_medida" class="form-control"
                                    placeholder="Ingrese la unidad de medida del producto" required
                                    value="{{old('unidad_medida')}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-left-right">
                            <div class="form-group">
                                <label for="nombre">Caducidad:</label>
                                <hr />
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="nombre">No. de piezas:</label>
                                        <input type="text" name="num_cad[]" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nombre">Fecha de caducidad:</label>
                                        <input type="date" name="fecha_cad[]" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div id="caducidad-produ" class="form-group"></div>
                            <div class="form-group">
                                <button type="button" id="agregar-caducidad" class="btn btn-success">+ Agregar caducidad</button>
                            </div>
                            <hr />
                        </div>
                    </div>
                    <button class="btn btn-info">Crear Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
