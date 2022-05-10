@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Contraseñas</h1>
@endsection
@section('css')
    <link rel="stylesheet" href="/css_custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Contraseñas</li>
</ol>
@stop
@section('content')

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <button id="btn_add"  type="button" class="btn btn-secondary mb-3 agregar" data-toggle="modal" data-target="#exampleModal">Agregar</button>
                    <table id="table_id" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Usuario</th>
                                <th>Servicio</th>
                                <th>Enlace</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contrasenias as $key => $value)
                                <tr>
                                    <td>{{$value->empresa}}</td>
                                    <td>{{$value->usuario}}</td>
                                    <td>{{$value->servicio}}</td>
                                    <td><a href="{{$value->enlace}}" target="_blank">Link</a></td>
                                    <td>{{$value->estado}}</td>
                                    <td>
                                      <form action="{!! route('admin.password.destroy', $value->id) !!}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                          <a style="cursor: pointer" type="button" name="edit" id="{{$value->id}}" class="edit"><i class="fa fa-edit" data-toggle="modal" data-target="#exampleModal"></i></a>
                                          <button type="submit" style="background: transparent;border: none;" class="edit"><i class="fa fa-trash"></i></button>
                                          <a type="button" id="{{$value->id}}" class="fa fa-key clave" data-toggle="modal" data-target="#mostrarClave" data-original-title="Editar"></a>&nbsp;
                                      </form></td>
                                </tr>   
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
      
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <span id="form_result"></span>
                    <form method="POST" id="password_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-9">
                                <label for="enlace" class="m-0">Empresa</label>
                                <div class="input-group mb-3">
                                    <input id="empresa" name="empresa" type="text"
                                    class="form-control" value="Hi-Mart"
                                    placeholder="Empresa" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="switch0" data-on-label="" data-off-label="">Estado</label>
                                <div class="form-group">
                                    <input type="checkbox" id="estado" name="estado" checked data-switch="bool"/>
                                    <label for="estado" data-on-label="Activo" data-off-label="Desactivado"></label>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="enlace" class="m-0">Enlace</label>
                                <div class="input-group mb-3">
                                    <input id="enlace" name="enlace" type="text"
                                    class="form-control"
                                    placeholder="Enlace" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="enlace" class="m-0">Servicio</label>
                                <div class="input-group mb-3">
                                    <input id="servicio" name="servicio" type="text"
                                    class="form-control"
                                    placeholder="Servicio" required>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="usuario" class="m-0">Usuario</label>
                                <div class="input-group mb-3">
                                    <input id="usuario" name="usuario" type="text"
                                    class="form-control"
                                    placeholder="Usuario" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="m-0" for="password">Contraseña</label>
                                    <div class="input-group mb-3">
                                        <input id="clave" name="clave" type="password"
                                        class="form-control"
                                        placeholder="Contraseña" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="action" id="action" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="submit" name="action_button"
                                    id="action_button" class="btn btn-secondary" value="Agregar" />
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="close_modal2" class="btn btn-danger btn-block" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>

      <!-- Info Alert Modal -->
      <div id="mostrarClave" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-information h1 text-info"></i>
                        <h4 class="mt-2">Mi contraseña!</h4>
                        <!-- Trigger -->
                        <input type="text" id="miclave" class="form-control m-1 text-center" value="">
                        <button class="btn btn-dark" data-clipboard-target="#miclave">
                            <i class="fa fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  
@stop
@push('styles')
{{-- Incluimos los links del diseño de la tabla de un solo archivo --}}
@include('auxiliares.design-datatables')
@endpush
@push('scripts')

<script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/contraenias.js') }}"></script>
{{-- Incluimos los scripts de la tabla de un solo archivo --}}
<script src="{{asset('assets/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
@include('auxiliares.scripts-datatables')

@endpush