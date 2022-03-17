@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Facturas</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Facturación</li>
</ol>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        Consulta el mes que de las ordenes que deseas facturar
                    </div>
                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#meses" data-toggle="tab"
                                aria-expanded="{{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'true' : 'false' }}{{ $parametros['mes'] != '' ? 'true' : '' }}"
                                class="nav-link {{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'active' : '' }}{{ $parametros['mes'] != '' ? 'active' : '' }}">
                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                <span class="d-none d-md-block">Meses</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane {{ $parametros['mes'] == '' && $parametros['rango'] == '' ? 'show active' : '' }}{{ $parametros['mes'] != '' ? 'show active' : '' }}"
                            id="meses">
                            <form id="consultaMes" action="{{ route('admin.facturas.index') }}" method="GET"
                                 autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mes">Mes</label>
                                            <select name="mes" id="mes" class="form-control select2" data-toggle="select2"
                                                required="true" data-tipo="txt">
                                                <option value="00" >Selecciona mes</option>
                                                @foreach($parametros['meses'] as $ke => $v)
                                                        @if($ke + 1 < 10)
                                                            <option value="0{{ $ke + 1 }}">{{ $v }}</option>
                                                        @else
                                                            <option value="{{ $ke + 1 }}">{{ $v }}</option>
                                                        @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" data-consultarmes="true" id="validacion"
                                            class="btn btn-secondary ">Consultar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if (count($parametros['ordenes']) > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Ordenes</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="table_id" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Id</th>
                                                    <th>Referencia</th>
                                                    <th>Total</th>
                                                    <th>Pagado</th>
                                                    <th>Facturar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($parametros['ordenes'] as $key => $value)
                                                    <tr>
                                                        <td>{{ date('Y-m-d', strtotime($value['date_add'])) }}</td>
                                                        <td>{{ $value['id'] }}</td>
                                                        <td>{{ $value['reference'] }}</td>
                                                        <td>${{ number_format($value['total_products'], 2) }}</td>
                                                        <td>${{ number_format($value['total_paid'], 2) }}</td>
                                                        <td><a href="{{ route('admin.facturas.edit', $value['id']) }}" data-id="{{ $value['id'] }}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> <i class="mdi mdi-pencil"></i></a></td>
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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Ordenes</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="table_id" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Id</th>
                                                    <th>Referencia</th>
                                                    <th>Total</th>
                                                    <th>Pagado</th>
                                                    <th>Facturar</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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