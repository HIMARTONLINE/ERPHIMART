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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="nuevo" action="{{ url($parametros['url']) }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf

                    @isset($parametros['usuario']->foto)
                    {{ method_field('patch') }}
                    @endif
                    <h4 class="header-title">{{ $parametros['subtitulo'] }}</h4>
                    <p class="text-muted font-14">{{ $parametros['descripcion'] }}</p>
                    <hr />
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="marco mt-3">
                                        <div class="foto">
                                            @if(isset($parametros['usuario']->foto))
                                                <img src="{{ $parametros['usuario']->foto != ''?asset('images/usuarios/'.$parametros['usuario']->foto):asset('images/avatar.jpg') }}" class="img-fluid" />
                                            @else
                                                <img src="{{ asset('images/avatar.jpg') }}" class="img-fluid" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('usuarios.campo1') }}</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="{{ __('usuarios.etiqueta1') }}" required="true" data-tipo="txt" value="@isset($parametros['usuario']->name){{ $parametros['usuario']->name }}@endisset" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="area_id">{{ __('usuarios.campo8') }}</label>
                                        <select name="area_id" id="area_id" class="form-control" required="true" data-tipo="txt">
                                            <option value="">{{ __('usuarios.etiqueta8') }}</option>
                                            @foreach ($parametros['areas'] as $k => $v)
                                            <option value="{{ $v['id'] }}" @isset($parametros['usuario']['area_id']){{ $parametros['usuario']['area_id'] == $v['id'] ? 'selected="selected"' : '' }}@endisset>{{ $v['area'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="permision_id">{{ __('usuarios.campo2') }}</label>
                                        <select name="permision_id" id="permision_id" class="form-control" required="true" data-tipo="txt">
                                            <option value="">{{ __('usuarios.etiqueta2') }}</option>
                                                @foreach ($parametros['roles'] as $k => $v)
                                                    <option value="{{ $v['id'] }}" @isset($parametros['usuario']->permision_id){{ $parametros['usuario']->permision_id == $v['id'] ? 'selected="selected"' : '' }}@endisset>{{ $v['rol'] }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">{{ __('usuarios.campo3') }}</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="{{ __('usuarios.etiqueta3') }}" required="true" data-tipo="txt" value="@isset($parametros['usuario']->email){{ $parametros['usuario']->email }}@endisset" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">{{ __('usuarios.campo4') }}</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('usuarios.etiqueta4') }}" required="true" data-tipo="txt" />
                                    </div>
                                </div>
                                {{--<div class="col-md-4">
                                    <div class="form-group">
                                        <label for="idioma">{{ __('usuarios.campo6') }}</label>
                                        <select name="idioma" id="idioma" class="form-control" required="true" data-tipo="txt">
                                            <option value="">{{ __('usuarios.etiqueta6') }}</option>
                                            <option value="es" @isset($parametros['usuario']->idioma){{ $parametros['usuario']->idioma =='es'?'selected="selected"':'' }}@endisset>{{ __('layout.Español') }}</option>
                                            @foreach ($parametros['idiomas'] as $k => $v)
                                            <option value="{{ $v->prefijo }}" @isset($parametros['usuario']->idioma){{ $parametros['usuario']->idioma==$v->prefijo?'selected="selected"':'' }}@endisset>{{ __('layout.'.$v->idioma) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>--}}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial">{{ __('usuarios.campo7') }}</label>
                                        <input type="text" name="serial" id="serial" class="form-control" placeholder="{{ __('usuarios.etiqueta7') }}" required="true" data-tipo="txt" value="@isset($parametros['usuario']->serial){{ $parametros['usuario']->serial }}@endisset" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="foto">{{ __('usuarios.campo5') }}</label>
                                        <input type="file" name="foto" id="foto" class="form-control" placeholder="{{ __('usuarios.etiqueta5') }}"  data-tipo="txt" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" data-guardar="true" class="btn btn-secondary"><i class="fas fa-save"></i> {{ __('layout.guardar') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@push('scripts')
<script src="{{ asset('page/assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script>
    if($("#name") != "") {
        $("#password").prop('required', false);
    }
</script>
@endpush