@extends('admin.layout.layout')

@section('title')
<h1 class="m-0 text-dark">Reporte de Ventas</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Reporte de Ventas</li>
</ol>
@stop
@section('content')
    {{--
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    --}}
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="uil-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reporte de Ventas</li>
                    </ol>
                </div>
                <h4 class="page-title">Reporte de Ventas</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        Obten los registros de ventas de acuerdo al rango de fechas proporcionado.
                    </div>
                    <hr />
                    <form class="mb-4" action="{{ route('export-ventas') }}" method="GET">
                    @csrf
                        <input type="hidden" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                        <input type="hidden" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
                        <button type="submit" class="btn btn-success float-right ml-3"><i class="fa fa-file-excel"></i> Exportar Ventas</button>
                    </form>
                    {{--
                    <div class="tab-content">
                    --}}
                        <div class="tab-pane" id="rango">
                            <form id="form-produ" class="mb-4" action="{{ route('filtro-ventas') }}" method="GET">
                            @csrf    
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>De: (Fecha)</label>
                                        <input class="form-control" type="date" name="de_fecha" value="{{ $filtro['de_fecha'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>A: (Fecha)</label>
                                        <input class="form-control" type="date" name="a_fecha" value="{{ $filtro['a_fecha'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input class="btn btn-info" style="margin-top: 32px;" type="submit" name="filtro_venta" value="Buscar">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($parametros['ordenes']) > 0)
    <div class="row">
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-hexagon-multiple widget-icon"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0">Pedidos Totales</h5>
                    <h3 class="mt-3 mb-3">{{ $total_pedidos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-hexagon-multiple widget-icon"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0">Piezas Totales</h5>
                    <h3 class="mt-3 mb-3">{{ $total_piezas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-currency-usd widget-icon"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0">Venta Total</h5>
                    <h3 class="mt-3 mb-3">${{ number_format($total_venta, 2) }}</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-currency-usd widget-icon"></i>
                    </div>
                    @foreach ($parametros['ordenes'] as $k => $v)
                        @if($v['comision'] == 'Conekta Prestashop' || $v['comision'] == 'Conekta tarjetas de crédito')
                            <?php 
                                $calculo1 = ($v['pagado']*2.9)/100 + 2.5;
                                $calculo2 = ($v['pagado']*2.9)/100 + 2.5 * 0.16;
                                $comision = $calculo1 + $calculo2;
                                $total_comision[] = $comision;
                            ?>
                        @elseif($v['comision'] == 'PayPal')
                            <?php 
                                $calculo1 = ($v['pagado']*3.95)/100 + 4.0;
                                $calculo2 = ($v['pagado']*3.95)/100 + 4.0 * 0.16;
                                $comision = $calculo1 + $calculo2;
                                $total_comision[] = $comision;
                            ?>
                        @elseif($v['comision'] == 'Kueski Pay')
                            <?php 
                                $calculo1 = ($v['pagado']*5.5)/100;
                                $calculo2 = ($v['pagado']*5.5)/100 * 0.16;
                                $comision = $calculo1 + $calculo2;
                                $total_comision[] = $comision;
                            ?>
                        @elseif($v['comision'] == 'Mercado Pago')
                            <?php 
                                $calculo1 = ($v['pagado']*3.49)/100 + 4.64;
                                $calculo2 = ($v['pagado']*3.49)/100 + 4.64 * 0.16;
                                $comision = $calculo1 + $calculo2;
                                $total_comision[] = $comision;
                            ?>
                        @else
                            <?php 
                                $total_comision[] = 0.00;
                            ?>
                        @endif
                    @endforeach
                    <?php
                        $sumaTotalComision = array_sum($total_comision);
                    ?>
                    <h5 class="text-muted font-weight-normal mt-0">Utilidad Total</h5>
                    <h3 class="mt-3 mb-3">{{ number_format($totalUtilidad = $total_utilidad['sumaSinIva'] - $total_utilidad['sumaCompra'] - $sumaTotalComision - $total_utilidad['sumaEnvio'] - $total_utilidad['sumaSeguro'], 2) }}</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-percent widget-icon"></i>
                    </div>
                    <?php
                        $porcentaje_utilidad = ($totalUtilidad * 100) / $total_venta;
                    ?>
                    <h5 class="text-muted font-weight-normal mt-0">Porcentaje de Utilidad</h5>
                    <h3 class="mt-3 mb-3">{{ number_format($porcentaje_utilidad, 2) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{--
                    <form id="exportar" action="{{ route('admin.reportes') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        @if($parametros['rango'])
                            <input type="hidden" name="valor" id="valor" required="true" data-tipo="txt" value="{{ $parametros['rango'] }}" />
                            <input type="hidden" name="parametro" id="parametro" required="true" data-tipo="txt" value="rango" />
                        @else
                            <input type="hidden" name="valor" id="valor" required="true" data-tipo="txt" value="{{ $parametros['mes'] }}" />
                            <input type="hidden" name="parametro" id="parametro" required="true" data-tipo="txt" value="mes" />
                        @endif
                        <button type="button" data-exportar="true" class="btn btn-success mb-3"><i class="mdi mdi-file-excel-outline"></i> {{ __('layout.exportar') }}</button>
                    </form>
                    --}}

                    <table id="table_id" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>#Orden</th>
                                <th>Referencia</th>
                                <th>Total</th>
                                <th>Descuento</th>
                                <th>Envío</th>
                                <th>Seguro de envío</th>
                                <th>Pagado</th>
                                <th>Sin IVA</th>
                                <th>Compra</th>
                                <th>Paquetería</th>
                                <th>Seguro</th>
                                <th>Comisión</th>
                                <th>Utilidad</th>
                                <th>Confirmación</th>
                                <th>Método de pago</th>
                                <th>Productos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parametros['ordenes'] as $k => $v)

                            <tr>
                                <td><span style="display: none;">{{ strtotime($v['fecha']) }}</span>{{ date('d/m/Y', strtotime($v['fecha'])) }}</td>
                                <td>{{ $v['orden'] }}</td>
                                <td>{{ $v['referencia'] }}</td>
                                <td>{{ number_format($v['pagado'], 2) }}</td>
                                <td>{{ number_format($v['descuento'], 2) }}</td>
                                <td>{{ number_format($v['envio'], 2) }}</td>
                                <td>{{ number_format($v['seguro_envio'], 2) }}</td>
                                <td>{{ number_format($v['total'], 2) }}</td>
                                <td>{{ number_format($v['sin_iva'], 2) }}</td>
                                <td>{{ number_format($v['compra'], 2) }}</td>
                                <td>{{ number_format($v['paqueteria'], 2) }}</td>
                                <td>{{ number_format($v['seguro'], 2) }}</td>
                                <td>
                                    @if($v['comision'] == 'Conekta Prestashop' || $v['comision'] == 'Conekta tarjetas de crédito')
                                        <?php 
                                            $calculo1 = ($v['pagado']*2.9)/100 + 2.5;
                                            $calculo2 = ($v['pagado']*2.9)/100 + 2.5 * 0.16;
                                            $comision = $calculo1 + $calculo2;
                                            echo number_format($comision, 2);
                                        ?>
                                    @elseif($v['comision'] == 'PayPal')
                                        <?php 
                                            $calculo1 = ($v['pagado']*3.95)/100 + 4.0;
                                            $calculo2 = ($v['pagado']*3.95)/100 + 4.0 * 0.16;
                                            $comision = $calculo1 + $calculo2;
                                            echo number_format($comision, 2);
                                        ?>
                                    @elseif($v['comision'] == 'Kueski Pay')
                                        <?php 
                                            $calculo1 = ($v['pagado']*5.5)/100;
                                            $calculo2 = ($v['pagado']*5.5)/100 * 0.16;
                                            $comision = $calculo1 + $calculo2;
                                            echo number_format($comision, 2);
                                        ?>
                                    @elseif($v['comision'] == 'Mercado Pago')
                                        <?php 
                                            $calculo1 = ($v['pagado']*3.49)/100 + 4.64;
                                            $calculo2 = ($v['pagado']*3.49)/100 + 4.64 * 0.16;
                                            $comision = $calculo1 + $calculo2;
                                            echo number_format($comision, 2);
                                        ?>
                                    @else
                                        {{ $v['comision'] }}
                                        <?php
                                            $comision = 0;
                                        ?>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($utilidad = $v['sin_iva'] - $v['compra'] - $comision - $v['paqueteria'] - $v['seguro'], 2) }}
                                </td>                                 
                                <td class="text-center">
                                    @if(intval($v['confirmacion']) == 4)
                                    <button type="button" class="btn btn-warning btn-sm">Enviado</button>
                                    @elseif(intval($v['confirmacion']) == 5)
                                    <button type="button" class="btn btn-secondary btn-sm">Entregado</button>
                                    @elseif($v['status'] == 'si')
                                    <button type="button" class="btn btn-success btn-sm btn-confirm r-{{ $v['orden'] }}" value="no-{{ $v['orden'] }}">Autorizado</button>
                                    @elseif($v['status'] == 'no')
                                    <button type="button" class="btn btn-info btn-sm btn-confirm a-{{ $v['orden'] }}" value="si-{{ $v['orden'] }}">Aceptar</button>
                                    <button type="button" class="btn btn-success btn-sm btn-confirm r-{{ $v['orden'] }}" style="display: none;" value="no-{{ $v['orden'] }}">Autorizado</button>
                                    @else
                                    <button type="button" class="btn btn-info btn-sm btn-confirm a-{{ $v['orden'] }}" value="si-{{ $v['orden'] }}">Aceptar</button>
                                    <button type="button" class="btn btn-success btn-sm btn-confirm r-{{ $v['orden'] }}" style="display: none;" value="no-{{ $v['orden'] }}">Autorizado</button>
                                    @endif
                                </td>
                                <td>{{ $v['comision'] }}</td>
                                <td>
                                    <?php
                                        $ultima_key = end( $v['productos'] );
                                    ?>
                                    <br />
                                    @foreach($v['productos'] as $key => $producto)
                                        @if($ultima_key == $producto)
                                            {{ $key . ' x ' . $producto }}
                                        @else
                                            {{ $key . ' x ' . $producto }}<br />
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table_id" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>#Orden</th>
                                <th>Referencia</th>
                                <th>Total</th>
                                <th>Descuento</th>
                                <th>Envío</th>
                                <th>Pagado</th>
                                <th>Sin IVA</th>
                                <th>Compra</th>
                                <th>Paquetería</th>
                                <th>Comisión</th>
                                <th>Utilidad</th>
                                <th>Confirmación</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form id="formdelete" action="" method="post">
        @csrf
        {{ method_field('delete') }}
        <input type="hidden" name="registro_id" id="registro_id" />
    </form>
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