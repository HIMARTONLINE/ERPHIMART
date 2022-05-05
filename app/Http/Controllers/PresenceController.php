<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use File;
use Response;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personal = User::all(['id','serial','clave','foto','name'])->toArray();
        $tiempo = date('Y-m-d H:i:s');
        foreach ($personal as $key => $value) {
            $personal[$key]['registros'] = Presence::where('users_id', '=', $value['id'])->whereRaw("DATE_FORMAT(presences.registro, '%Y-%m-%d') = DATE_FORMAT('".$tiempo."', '%Y-%m-%d')")->count();
        }
        
        $parametros = ['personal' => $personal,
                       'entrada'  => $this->configuracion['Entrada']];

        return view('presencia', compact('parametros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setPresence(Request $request) {
        try {
            $usuario = User::where('serial', '=', request('clave'))
                             ->orWhere('clave', '=', request('clave'))
                             ->first();

            $limite = date("Y-m-d {$this->configuracion['Entrada']}");
            $tiempo = date('Y-m-d H:i:s');

            if($usuario != null) {
                $clase = 'neutro';
                $registro = ['users_id' => $usuario->id,
                             'registro' => $tiempo,
                             'estatus'  => 0];

                if(Presence::where('users_id', '=', $usuario->id)->whereRaw("DATE_FORMAT(presences.registro, '%Y-%m-%d') = DATE_FORMAT('".$tiempo."', '%Y-%m-%d')")->count() == 0) {
                    $datetime1 = new \DateTime($limite);
                    $datetime2 = new \DateTime($tiempo);
                    $intervalo = $datetime1->diff($datetime2);
                    
                    if($intervalo->invert == 0) { //Tarde
                        $registro['estatus'] = -1;
                        $clase = 'tarde';
                    } else if($intervalo->invert == 1) { //A tiempo
                        $registro['estatus'] = 1;
                        $clase = 'atiempo';
                    }
                }
                
                Presence::create($registro);

                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $tiempo);
                
                $resultado = ['res'    => true,
                              'id'     => $usuario->id,
                              'nombre' => $usuario->name,
                              'avatar' => $usuario->foto,
                              'clase'  => $clase,
                              'tiempo' => $fecha->format('H:i')];
            } else {
                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $tiempo);
                $resultado = ['res'    => false,
                              'id'     => 0,
                              'nombre' => 'Usuario no encontrado',
                              'avatar' => 'assets/images/users/avatar.jpg',
                              'clase'  => 'fallo',
                              'tiempo' => $fecha->format('H:i')];
            }
            
        } catch(Exception $exception) {
            $usuario = User::where('serial', '=', request('clave'))
                             ->orWhere('clave', '=', request('clave'))
                             ->first();

            $resultado = ['res'    => false,
                          'id'     => $usuario->id,
                          'nombre' => $usuario->name,
                          'avatar' => $usuario->foto,
                          'clase'  => 'fallo',
                          'tiempo' => date('H:i')];

            
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
        die();
    }

    public function displayImage($filename) {
        $path = storage_path('app/public/images/usuarios/'.$filename);
        if(!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function displayImageG($filename) {
        $path = storage_path("app/public/img/$filename");

        if(!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function token() {
        header('Content-Type: application/json');
        echo json_encode(['_token' => csrf_token()]);
        die();
    }

    public function getPersonal() {
        $resultado = User::all(['id','serial','clave','foto','name'])->toArray();
        $tiempo = date('Y-m-d H:i:s');
        foreach ($resultado as $key => $value) {
            $resultado[$key]['registros'] = Presence::where('users_id', '=', $value['id'])->whereRaw("DATE_FORMAT(presences.registro, '%Y-%m-%d') = DATE_FORMAT('".$tiempo."', '%Y-%m-%d')")->count();
        }
        header('Content-Type: application/json');
        echo json_encode($resultado);
        die();
    }
}
