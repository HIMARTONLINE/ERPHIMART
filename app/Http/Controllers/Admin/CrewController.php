<?php

namespace App\Http\Controllers\Admin;

use App\Models\Crew;
use App\Models\Area;
use App\User;
use App\Contactos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;

class CrewController extends Controller {
    public function __construct() {
        // parent::__construct();
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $registros = Crew::select('crews.id', 'crews.user_id', 'crews.nombres', 'crews.apellidos', 'crews.foto', 'crews.movil', 'areas.area', 'users.email', DB::raw('DATE_FORMAT(crews.ingreso, \'%d/%m/%Y\') AS fecha'))
                    ->leftjoin('areas', 'crews.area_id', '=', 'areas.id')
                    ->leftjoin('users', 'crews.user_id', '=', 'users.id')
                    ->where('crews.activo', '=', 1)
                    ->orderBy('id', 'DESC')
                    ->get();

        return view('admin.personal.index', ['registros' => $registros]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $areas = Area::select('areas.id', 'areas.area')->groupBy('areas.id')->get();

        return view('admin.personal.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(['nombres'    => 'required', 
                            'apellidos'  => 'required',
                            'genero'     => 'required',
                            'nacimiento' => 'required',
                            'direccion'  => 'required',
                            'municipio'  => 'required',
                            'estado'     => 'required',
                            'movil'      => 'required',
                            'ingreso'    => 'required',
                            'nss'        => 'required',
                            'curp'       => 'required',
                            'rfc'        => 'required',
                            'cuenta'     => 'required',
        ]);

        try {
            $nombre_con = $request->nombre;
            $parentesco_con = $request->parentesco;
            $telefono_con = $request->telefono;

            if($request->hasFile('foto')){

                $path = $request->file('foto');
                $name_file = $path->getClientOriginalName();
                $path->move(public_path().'/images/usuarios/', $name_file);
            }

            $temp = \DateTime::createFromFormat('d/m/Y', request('nacimiento'));
            $nacimiento = $temp->format('Y-m-d');
            $temp = \DateTime::createFromFormat('d/m/Y', request('ingreso'));
            $ingreso = $temp->format('Y-m-d');

            $usuario = User::create(['permision_id' => 0,
                                     'area_id'      => request('area_id'),
                                     'serial'       => 0,
                                     'clave'        => substr(time(), -6, 6),
                                     'name'         => request('nombres').' '.request('apellidos'),
                                     'email'        => request('correo'),
                                     'password'     => Hash::make('noasignado'),
                                     'foto'         => 'avatar.jpg',
                                     'idioma'       => 'es',
                                     'activo'       => 1
            ]);

            Crew::create(['user_id'    => $usuario->id,
                        'area_id'    => request('area_id'),
                        'nombres'    => request('nombres'),
                        'apellidos'  => request('apellidos'),
                        'foto'       => $name_file,
                        'genero'     => request('genero'),
                        'nacimiento' => $nacimiento,
                        'direccion'  => nl2br(request('direccion')),
                        'municipio'  => request('municipio'),
                        'estado'     => request('estado'),
                        'movil'      => request('movil'),
                        'correo'     => request('correo'),
                        'contactos'  => request('contactos'),
                        'ingreso'    => $ingreso,
                        'nss'        => request('nss'),
                        'curp'       => request('curp'),
                        'rfc'        => request('rfc'),
                        'infonavit'  => request('infonavit'),
                        'cuenta'     => request('cuenta'),
                        'activo'     => 1,
            ]);

            if(isset($nombre_con)){
                for($i=0; $i<count($nombre_con); $i++){

                    Contactos::create([
                        'id_usuario' => $usuario->id,
                        'nombre' => $nombre_con[$i],
                        'parentesco' => $parentesco_con[$i],
                        'telefono' => $telefono_con[$i]
                    ]);
    
                }
            }

            $remitente_direccion = 'sistemas@jeramoda.com';
            $remitente_nombre = 'Sistemas';
            $asunto = '🤖 Alta nuevo ingreso';
            $area = Area::findOrFail(request('area_id'));
            $msj = "<h1>NUEVO INTEGRANTE</h1>
                    Nombre: ".request('nombres')."<br />
                    Apellidos: ".request('apellidos')."<br />
                    Área: ".$area->area."<br />";

            $cabeceras = "MIME-Version: 1.0  \r\n";
            $cabeceras .= "Content-type: text/html; charset=utf-8 \r\n";
            $cabeceras .= "From: $remitente_nombre <$remitente_direccion>";
            mail('sistemas@jeramoda.com', $asunto, $msj, $cabeceras);
        } catch(Exception $exception) {

        }

        return redirect()->route('admin.personal.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function show(Crew $crew) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $areas = Area::select('areas.id', 'areas.area')->groupBy('areas.id')->get();

        $data = Crew::findOrFail($id);

        $contactos = Contactos::where('id_usuario', $data->user_id)->get();

        return view('admin.personal.edit', compact('areas','data','contactos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate(['nombres'    => 'required', 
                            'apellidos'  => 'required',
                            'genero'     => 'required',
                            'nacimiento' => 'required',
                            'direccion'  => 'required',
                            'municipio'  => 'required',
                            'estado'     => 'required',
                            'movil'      => 'required',
                            'ingreso'    => 'required',
                            'nss'        => 'required',
                            'curp'       => 'required',
                            'rfc'        => 'required',
                            'cuenta'     => 'required',
        ]);

        try {
            $nombre_con = $request->nombre;
            $parentesco_con = $request->parentesco;
            $telefono_con = $request->telefono;

            if($request->hasFile('foto')){
                $path = $request->file('foto');
                $name_file = $path->getClientOriginalName();
                $path->move(public_path().'/images/usuarios/', $name_file);

                $datos = Crew::findOrFail($id);
                $datos->foto = $name_file;
                $datos->save();
            }

            $temp = \DateTime::createFromFormat('d/m/Y', request('nacimiento'));
            $nacimiento = $temp->format('Y-m-d');
            $temp = \DateTime::createFromFormat('d/m/Y', request('ingreso'));
            $ingreso = $temp->format('Y-m-d');

            Crew::where('id', '=', $id)->update(['area_id'    => request('area_id'),
                                                'nombres'    => request('nombres'),
                                                'apellidos'  => request('apellidos'),
                                                'genero'     => request('genero'),
                                                'nacimiento' => $nacimiento,
                                                'direccion'  => nl2br(request('direccion')),
                                                'municipio'  => request('municipio'),
                                                'estado'     => request('estado'),
                                                'movil'      => request('movil'),
                                                'correo'     => request('correo'),
                                                'contactos'  => request('contactos'),
                                                'ingreso'    => $ingreso,
                                                'nss'        => request('nss'),
                                                'curp'       => request('curp'),
                                                'rfc'        => request('rfc'),
                                                'infonavit'  => request('infonavit'),
                                                'cuenta'     => request('cuenta'),
            ]);

            if(isset($nombre_con)){
                for($i=0; $i<count($nombre_con); $i++){

                    Contactos::create([
                        'id_usuario' => $usuario->id,
                        'nombre' => $nombre_con[$i],
                        'parentesco' => $parentesco_con[$i],
                        'telefono' => $telefono_con[$i]
                    ]);
    
                }
            }

        } catch(Exception $exception) {

        }

        return redirect()->route('admin.personal.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
            Crew::where('user_id', $id)->delete();
            User::where('id', $id)->delete();
        } catch(Exception $exception) {

        }

        return redirect()->route('admin.personal.index');
    }

    public function delete_item_cart($id)
    {    
        $deleted = Contactos::where('id', $id)->delete();
        $message = "Se ha eliminado el contacto con éxito";
        if (!$deleted) {
            $message = "Ocurrio un error al eliminar el contacto";
        }

        return redirect()->back()->with('flash',$message);
    }
}