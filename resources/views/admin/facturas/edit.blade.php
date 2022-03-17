@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Realizar Factura</h1>
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Administración</li>
</ol>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Orden #{{$orden['id']}}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-10 text-left-right">
                                <div class="form-group">
                                    <h3>Datos Fiscales</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="rfc">RFC</label>
                                    <input type="text" name="rfc" class="form-control" placeholder="Ingrese el RFC del cliente">
                                </div>
                            </div>
                            <div class="col-md-3 text-left-right">
                                <div class="form-group">
                                    <label for="rs">Rázon Social</label>
                                    <input type="text" name="razon" class="form-control" placeholder="Escriba la Rázon Social del cliente">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="pedido">Número de pedido</label>
                                    <input class="form-control" type="text" name="pedidoId" id="pedidoId" value="{{ $orden['id'] }}">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="pedido">Referencia</label>
                                    <input class="form-control" type="text" name="referencia" id="pedidoId" value="{{ $orden['reference'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="regimen">Régimen Fiscal</label>
                                    <select class="form-control" name="regimen" id="regimen">
                                        <option value="">Seleccione una opción</option>
                                        @foreach($regimen as $key => $valor)
                                            <option value="{{$valor}}">{{$valor}}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Uso de Factura</label>
                                    <select name="factura" id="" class="form-control">
                                        <option value="">Seleccione una opción</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Método de pago</label>
                                    <select class="form-control" name="pago" id="">
                                        <option value="">Seleccione un metodo de pago</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10 text-left-right">
                                <div class="form-group">
                                    <h3>Dirección</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-left-right">
                                <div class="form-group">
                                    <label for="calle">Calle</label>
                                    <input class="form-control" type="text" name="calle" placeholder="Calle" required="true">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="calle">Número interior</label>
                                    <input class="form-control" type="text" name="NoIn" placeholder="Ej. 451" required="true">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="calle">Número exterior</label>
                                    <input class="form-control" type="text" name="NoEx" placeholder="Ej. 451" required="true">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <input class="form-control" type="text" name="estado">
                                </div>
                            </div>
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="muni">Municipio/Delegación</label>
                                    <input type="text" class="form-control" name="muni">
                                </div>
                            </div>
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="Colonia">Colonia</label>
                                    <input type="text" class="form-control" name="colonia">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="cp">Código Postal</label>
                                    <input class="form-control" type="text" name="cp" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="correo">Correo eléctronico</label>
                                    <input class="form-control" type="email" name="correo" id="correo" placeholder="exple@example.com">
                                </div>
                            </div>
                            
                        </div>
                        <button class="btn btn-success">Realizar factura</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection