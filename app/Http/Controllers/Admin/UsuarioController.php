<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Permision;
use Illuminate\Support\Facades\Hash;
use File;
use Response;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class UsuarioController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::select('users.id', 'users.foto', 'users.name', 'users.email', 'users.updated_at', 'permisions.rol', 'areas.area')
                          ->leftjoin('permisions', 'users.permision_id', '=', 'permisions.id')
                          ->leftjoin('areas', 'users.area_id', '=', 'areas.id')
                          ->where('users.id', '>', '1')
                          ->get()->toArray();

        $parametros = ['usuarios'  => $usuarios,];
        //dd($parametros);
        return view('admin.usuario.index', compact('parametros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $roles = Permision::select('id', 'rol')->get()->toArray();
       $area = Area::select('id', 'area')->get()->toArray();

        $parametros = ['url'            => route('admin.usuario.store'),
                       'titulo'         => __('usuarios.titulo1'),
                       'subtitulo'      => __('usuarios.subtitulo1'),
                       'descripcion'    => __('usuarios.descripcion1'),
                       'areas'          => $area,
                       'roles'        => $roles
                      ];
       //dd($parametros);
       return view('admin.usuario.create', compact('parametros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Permision::select('id', 'rol')->get()->toArray();
        $area = Area::select('id', 'area')->get()->toArray();

         $parametros = ['url'           => "/usuario/$id",
                       'titulo'         => 'Editar Usuario',
                       'subtitulo'      => 'Editar usuario',
                       'descripcion'    => __('usuarios.descripcion1'),
                       'areas'          => $area,
                       'roles'          => $roles,
                       'usuario'        => User::findOrFail($id),
                    ];
                       //dd($parametros);
        return view('admin.usuario.create', compact('parametros'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
