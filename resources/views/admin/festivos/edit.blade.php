@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Festivos</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Nueva Festivos</li>
</ol>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="nuevo" action="{{ route('admin.festivos.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <h4 class="header-title">EDITAR FESTIVIDAD</h4>
                        <p class="text-muted font-14">Completa el siguiente formulario para crear una festividad</p>
                        <hr />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="festividad">Festividad</label>
                                    <input type="text" name="festividad" id="festividad" value="{{$parametros['festividad']}}" class="form-control" placeholder="Evento no laborable" required="true" data-tipo="txt"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_descanso">Fecha Descanso</label>
                                    <input type="text" name="fecha_descanso" id="fecha_descanso" value="{{date('d/m/Y',strtotime($parametros['fecha_descanso']))}}" class="form-control" required="true" data-tipo="txt"  data-traducir="false" data-toggle="input-mask" data-mask-format="00/00/0000" />
                                    <span class="font-13 text-muted">ej. "DD/MM/YYYY"</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_conmemorativa">Fecha Conmemorativa</label>
                                    <input type="text" name="fecha_conmemorativa" id="fecha_conmemorativa" value="{{date('d/m/Y',strtotime($parametros['fecha_conmemorativa']))}}" class="form-control" required="true" data-tipo="txt"  data-traducir="false" data-toggle="input-mask" data-mask-format="00/00/0000" />
                                    <span class="font-13 text-muted">ej. "DD/MM/YYYY"</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" data-guardar="true" class="btn btn-secondary"><i class="bi bi-pencil-fill"></i> Editar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

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