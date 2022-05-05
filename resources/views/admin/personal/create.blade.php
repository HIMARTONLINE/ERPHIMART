@extends('admin.layout.layout')
@section('title')
<h1 class="m-0 text-dark">Añadir persona</h1>
@endsection
@section('content-header')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
    <li class="breadcrumb-item active">Administración</li>
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
                        <li class="breadcrumb-item"><a href="#">Recursos Humanos</a></li>
                        <li class="breadcrumb-item"><a href="#">Personal</a></li>
                        <li class="breadcrumb-item active">Añadir persona</li>
                    </ol>
                </div>
                <h4 class="page-title">Añadir persona</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{route('admin.personal.store')}}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <h4 class="header-title"></h4>
                        <p class="text-muted font-14"></p>
                        <h5 class="mb-2 text-uppercase bg-light p-2"><i class="mdi mdi-account-badge-outline mr-1"></i> INTEGRANTE</h5>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="marco mt-3">
                                            <div class="foto">
                                                <img src="{{ asset('images/avatar.jpg') }}" class="img-fluid rounded-circle" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombres">Nombre(s)</label>
                                            <input type="text" name="nombres" id="nombres" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->nombres){{ $parametros['data']->nombres }}@endisset" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apellidos">Apellidos</label>
                                            <input type="text" name="apellidos" id="apellidos" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->apellidos){{ $parametros['data']->apellidos }}@endisset" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nacimiento">Fecha de nacimiento</label>
                                            <input type="text" name="nacimiento" id="nacimiento" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->nacimiento){{ $parametros['data']->nacimiento }}@endisset" data-tipo="txt" value="@isset($parametros['data']->nacimiento){{ $parametros['data']->nacimiento }}@endisset" data-toggle="input-mask" data-mask-format="00/00/0000" maxlength="10" />
                                            <span class="text-muted">ej. "DD/MM/YYYY"</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="genero">Género</label>
                                            <select name="genero" id="genero" class="form-control" required="true" data-tipo="txt" data-traducir="false">
                                                <option value="">Selecciona género</option>
                                                <option value="F">Femenino</option>
                                                <option value="M">Masculino</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="movil">Teléfono</label>
                                            <input type="text" name="movil" id="movil" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->movil){{ $parametros['data']->movil }}@endisset" data-toggle="input-mask" data-mask-format="00.0000.0000" maxlength="12" />
                                            <span class="text-muted">ej. "xx.xxxx.xxxx"</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="correo">Correo electrónico</label>
                                            <input type="text" name="correo" id="correo" class="form-control" required="false" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->correo){{ $parametros['data']->correo }}@endisset" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="foto">Foto</label>
                                            <input type="file" name="foto" id="foto" class="form-control" data-tipo="txt" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="area_id">Área</label>
                                            <select name="area_id" id="area_id" class="form-control" required="true" data-tipo="txt">
                                                <option value="">Selecciona área</option>
                                                @foreach ($areas as $k => $v)
                                                <option value="{{ $v->id }}">{{ $v->area }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-home-flood mr-1"></i> DOMICILIO</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <textarea name="direccion" id="direccion" cols="30" rows="5" class="form-control" required="true" data-tipo="txt" data-traducir="false">@isset($parametros['data']->direccion){{ $parametros['data']->direccion }}@endisset</textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="municipio">Municipio</label>
                                            <input type="text" name="municipio" id="municipio" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->municipio){{ $parametros['data']->municipio }}@endisset" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="estado">Estado</label>
                                            <input type="text" name="estado" id="estado" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="Jalisco" readonly="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-account-card-details-outline mr-1"></i> INFORMACIÓN FISCAL</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nss">NSS</label>
                                    <input type="text" name="nss" id="nss" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->nss){{ $parametros['data']->nss }}@endisset" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="curp">CURP</label>
                                    <input type="text" name="curp" id="curp" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->curp){{ $parametros['data']->curp }}@endisset" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="rfc">RFC</label>
                                    <input type="text" name="rfc" id="rfc" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->rfc){{ $parametros['data']->rfc }}@endisset" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="infonavit">INFONAVIT</label>
                                    <input type="text" name="infonavit" id="infonavit" class="form-control" required="false" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->infonavit){{ $parametros['data']->infonavit }}@endisset" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cuenta">Cuenta CLABE</label>
                                    <input type="text" name="cuenta" id="cuenta" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->cuenta){{ $parametros['data']->cuenta }}@endisset" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ingreso">Fecha de ingreso</label>
                                    <input type="text" name="ingreso" id="ingreso" class="form-control" required="true" data-tipo="txt" data-traducir="false" value="@isset($parametros['data']->ingreso){{ $parametros['data']->ingreso }}@endisset" data-tipo="txt" value="@isset($parametros['data']->ingreso){{ $parametros['data']->ingreso }}@endisset" data-toggle="input-mask" data-mask-format="00/00/0000" maxlength="10" />
                                    <span class="text-muted">ej. "DD/MM/YYYY"</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="uil-users-alt mr-1"></i> CONTACTOS DE EMERGENCIAS</h5>
                        <table id="tablacontactos" class="table table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th>Parentesco</th>
                                    <th>Teléfono</th>
                                    <th>
                                        <button type="button" data-addcontacto="true" id="agregar-contactos" class="btn btn-info btn-sm float-right"><i class="mdi mdi-account-multiple-plus-outline"></i> Agregar contacto</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="campos-contactos">
                            </tbody>
                        </table>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <input type="hidden" name="contactos" id="contactos" required="false" data-tipo="txt" data-traducir="false" />
                                <button type="submit" data-guardar="true" class="btn btn-secondary"><i class="fas fa-save"></i> Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection