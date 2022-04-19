@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Personal</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Personal</li>
</ol>
@stop
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="uil-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#">Recursos humanos</a></li>
                        <li class="breadcrumb-item active">Personal</li>
                    </ol>
                </div>
                <h4 class="page-title">Personal</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('admin.personal.create')}}" class="btn btn-success float-right">
                        <i class="fa fa-plus"></i> Agregar persona
                    </a>
                </div>
                <div class="card-body">
                    <table id="table_id" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre(s)</th>
                                <th>Apellidos</th>
                                <th>Teléfono</th>
                                <th>Correo electrónico</th>
                                <th>Área</th>
                                <th>Ingreso</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $key => $value)
                                <tr data-widget="expandable-table" aria-expanded="false">
                                    @if($value->foto != '')
                                        <td><img class="rounded-circle" src="{{ asset('images/usuarios/'.$value->foto) }}" width="40"></td>
                                    @else
                                        <td><img class="rounded-circle" src="{{ asset('images/avatar.jpg') }}" width="40"></td>
                                    @endif
                                    <td>{{ $value->nombres }}</td>
                                    <td>{{ $value->apellidos }}</td>
                                    <td>{{ $value->movil }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ $value->area }}</td>
                                    <td>{{ $value->fecha }}</td>
                                    <td>
                                    <a href="{{ route('admin.personal.edit', $value->id)}}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="mdi mdi-pencil"></i></a>
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