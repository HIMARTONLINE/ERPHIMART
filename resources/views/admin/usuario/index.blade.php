@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Usuarios</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Usuarios</li>
</ol>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <a id="btnnuevo" href="{{route('admin.usuario.create')}}" class="btn btn-secondary mb-3">Agregar</a>
                <table id="table_id" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ __('usuarios.columna1') }}</th>
                            <th>{{ __('usuarios.columna2') }}</th>
                            <th>{{ __('usuarios.columna5') }}</th>
                            <th>{{ __('usuarios.columna3') }}</th>
                            <th>{{ __('usuarios.columna4') }}</th>
                            <th>{{ __('layout.accion') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parametros['usuarios'] as $k => $v)
                        <tr>
                            
                            @if($v['foto'] != '')
                                <td class="table-user"><img class="rounded-circle" src="{{ asset('images/usuarios/'.$v['foto']) }}" alt="table-user" width="40" />
                                    {{ $v['name'] }}</td>
                            @else
                                <td class="table-user"><img class="rounded-circle" src="{{ asset('images/avatar.jpg') }}" alt="table-user" width="40" />
                                    {{ $v['name'] }}</td>
                            @endif
                            <td>{{ $v['email'] }}</td>
                            <td>{{ $v['area'] }}</td>
                            <td>{{ $v['rol'] }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($v['updated_at'])) }}</td>
                            <td>
                                <form action="{{route('admin.usuario.destroy', $v['id'])}}" method="POST">
                                @csrf
                                @method('DELETE')
                                    <a href="{{ route('admin.usuario.edit', $v['id'])}}" class="icon-pencil" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="mdi mdi-pencil"></i></a>&nbsp;
                                    <button type="submit" style="background: transparent;border: none;color: #007bff;"><i class="fa fa-trash"></i></button>
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