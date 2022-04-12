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
    <li class="breadcrumb-item active">Festivos</li>
</ol>
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <a id="btnnuevo" href="{{route('admin.festivos.create')}}" class="btn btn-secondary mb-3">Agregar</a>
                <table id="table_id" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>FESTIVIDAD</th>
                            <th>FECHA DESCANSO</th>
                            <th>FECHA CONMEMORATIVA</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($parametros['parametros'] as $rows => $value)
                        <tr>
                            <td>{{$value['festividad']}}</td>
                            <td>{{$value['fecha_descanso']}}</td>
                            <td>{{$value['fecha_conmemorativa']}}</td>
                            <td>
                                <form action="{{route('admin.festivos.destroy', $value['id'])}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    {{--<a href="{{ route('admin.festivos.edit', $value['id'])}}" data-id="{{ $value['id'] }}" data-toggle="tooltip" data-placement="top" data-original-title="Editar"> <i class="bi bi-pencil-fill"></i></a>--}}
                                    <button style="border: 0;" type="submit"><i class="bi bi-trash3"></i></button>               
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="formdelete" action="" method="post">
    @csrf
    {{ method_field('delete') }}
    <input type="hidden" name="registro_id" id="registro_id" />
</form>
@endsection

@push('styles')
{{-- Incluimos los links del diseño de la tabla de un solo archivo --}}
@include('auxiliares.design-datatables')
@endpush
@push('scripts')
<script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('page/assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{asset('js/festivos.js')}}"></script>

{{-- Incluimos los scripts de la tabla de un solo archivo --}}
<script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
@include('auxiliares.scripts-datatables')

@endpush