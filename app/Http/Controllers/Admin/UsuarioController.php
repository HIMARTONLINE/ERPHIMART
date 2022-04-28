<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Permision;
use Illuminate\Support\Facades\Hash;
use App\Models\Crew;
use App\User;
use Exception;


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
        $request->validate(['permision_id' => 'required',
                            'area_id'      => 'required',
                            'serial'       => 'required',
                            'name'         => 'required|max:255',
                            'email'        => 'required|email|max:255',
                            'password'     => 'required|min:8|max:255',]);

        $imagePath = '';
        if ($request->file('foto')) {
            $imagePath = $request->foto->store('avatar', 'public');
        }

       $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.guardado')];
        try {
            $registro = User::create(['permision_id' => request('permision_id'),
                                      'area_id'      => request('area_id'),
                                      'serial'       => request('serial'),
                                      'clave'        => substr(time(), -6, 6),
                                      'name'         => request('name'),
                                      'email'        => request('email'),
                                      'password'     => Hash::make(request('password')),
                                      'foto'         => $imagePath,
                                      ]);
        } catch(Exception $exception) {
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('usuarios.alerta2')];
        }

        return redirect()->route('admin.usuario.index')->with($mensaje['tipo'], $mensaje['mensaje']);
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

         $parametros = ['url'           => "admin/usuario/$id",
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
        $request->validate(['permision_id' => 'required',
                            'area_id'      => 'required',
                            'serial'       => 'required',
                            'name'         => 'required|max:255',
                            'email'        => 'required|email|max:255',
                            ]);

        $datos = User::findOrFail($id);
        $imagePath = $datos->foto;

        if($request->hasFile('foto')){

            $path = $request->file('foto');
            $imagePath = $path->getClientOriginalName();
            $path->move(public_path().'/images/usuarios/avatar', $imagePath);
        }
        
        $data = ['permision_id' => request('permision_id'),
                 'area_id'      => request('area_id'),
                 'serial'       => request('serial'),
                 'name'         => request('name'),
                 'email'        => request('email'),
                 'foto'         => $imagePath];
        
        if(request('password') != null) {
            $data['password'] = Hash::make(request('password'));
        }

        $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.actualizado')];
        try {
            $registro = User::where('id', '=', $id)->update($data);
        } catch(Exception $exception) {
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('usuarios.alerta2')];
        }

        return redirect()->route('admin.usuario.index')->with($mensaje['tipo'], $mensaje['mensaje']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Crew::where('user_id', $id)->delete();
            User::where('id', $id)->delete();
        } catch(Exception $exception) {

        }

        return redirect()->route('admin.usuario.index');
    }
}
