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
                    <h3 class="card-title">Orden #{{$parametros['orden']['id']}}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('personal')}}">
                        @csrf
                        {{--<input type="hidden" name="mes_factura" value="{{ $fecha_mes_orden }}" />--}}
                        <input type="hidden" name="id_orden" value="{{$parametros['orden']['id']}}" />
                        <div class="row">
                            <div class="col-md-10 text-left-right">
                                <div class="form-group">
                                    <h3>Datos Fiscales</h3>
                                    <input class="form-control" type="hidden" name="pedido" id="pedidoId" value="{{ $parametros['orden']['id'] }}" />
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
                                    <label for="">Código Postal</label>
                                    <input class="form-control" type="text" name="cp">
                                </div>
                            </div>
                            <div class="col-md-1 text-left-right">
                                <div class="form-group">
                                    <label for="pedido">Referencia</label>
                                    <input class="form-control" type="text" name="referencia" id="pedidoId" value="{{ $parametros['orden']['reference'] }}" disabled />
                                </div>
                            </div>
                            <div class="col-md-1 text">
                                <div class="form-group">
                                    <label for="">Descuento</label><br>
                                    <input class="form-control" type="number" name="porcentaje" id="" placeholder="Porcentaje"> 
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-2 text-left-right">
                                <div class="form-group">
                                    <label for="regimen">Régimen Fiscal</label>
                                    <select class="form-control" name="regimen" id="regimen">
                                        <option value="">Seleccione una opción</option>
                                        @foreach($parametros['regimen'] as $key => $valor)
                                            <option value="{{$key}}">{{$valor}}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Uso de Factura</label>
                                    <select name="factura" id="" class="form-control">
                                        <option value="">Seleccione una opción</option>
                                        @foreach($parametros['cfdi'] as $key => $valor)
                                            <option value="{{$key}}">{{$valor}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Forma de pago</label>
                                    <select class="form-control" name="forma_pago" id="">
                                        <option value="">Seleccione un metodo de pago</option>
                                        @foreach($parametros['formaPago'] as $key => $valor)
                                            <option value="{{$key}}">{{$valor}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Método de pago</label>
                                    <select class="form-control" name="metodo_pago" id="">
                                        <option value="">Seleccione un metodo de pago</option>
                                        @foreach($parametros['metodo_pago'] as $key => $valor)
                                            <option value="{{$key}}">{{$valor}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Elegir fecha</label>
                                    <input class="form-control" type="date" name="fecha_factura" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card-header border-0">
                                    <h3>Articulos en la orden</h3>
                                </div>
                            </div>
                            <table id="table_id" class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Catidad</th>
                                        <th>Unidad</th>
                                        <th>Conceptos</th>
                                        <th>Precio U</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($parametros['articulos'] as $index => $valor)
                                        <tr>
                                            <td>{{ $valor['ProductCode'] }}</td>
                                            <td>{{ $valor['Quantity'] }}</td>
                                            <td>{{ $valor['UnitCode'] }}</td>
                                            <td>{{ $valor['Description'] }}</td>
                                            <td>{{ $valor['UnitPrice'] }}</td>
                                            <td class="importe">{{ $valor['Subtotal'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-success" name="factura-individual">Realizar factura</button>
                            </div>
                            <div class="col align-self-end">
                                <label for="">Total: $<span id="totalSuma"></span></label>
                            </div>
                        </div>                       
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    var suma = 0;
    //llamamos la clase notas
    //$('.notas2').each(function() {
       /* $('form').find('.importe').each(function() {
            if (isNaN(parseFloat($(this).html()))) {
                suma += 0;
            } else {
                suma += parseFloat($(this).html());
            }
        });
    $("#totalSuma").html(suma);*/
</script>
@endpush