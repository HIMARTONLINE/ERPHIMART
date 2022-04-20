<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestPermisions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Models\Holiday;
use DB;


class PermisoController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $solicitudes = [];
        //$usuario = Auth::user()->crew->id;
        $parametros = [ 'solicitudes' => [] ];

        $sqlQuery = RequestPermisions::select('id', 'autorizacion', 'dias_solicitados', 'dia_hora', 'hora_ingreso', 'horas_solicitadas',
                                        DB::raw('DATE_FORMAT(fecha_ingreso, \'%d/%m/%Y\') AS fecha_ingreso'),
                                        DB::raw('DATE_FORMAT(created_at, \'%d/%m/%Y\') AS creado'))
                                    ->where('crew_id', '=', Auth::user()->crew->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get()->toArray();

        foreach ($sqlQuery as $key => $value) {
            $dias_solicitados = json_decode($value['dias_solicitados'], true);
            switch($value['autorizacion']) {
                case 1:
                    $autorizacion = 'text-success';
                break;
                case 2:
                    $autorizacion = 'text-warning';
                break;
                case 3:
                    $autorizacion = 'text-primary';
                break;
                default:
                    $autorizacion = 'text-danger';
                break;
            }
            //dd($value['hora_ingreso']);
            if($value['hora_ingreso'] != null || $value['hora_ingreso'] != 0) {
                //Aqui van los valores en caso de solicitar horas
                $fecha_hora = date("d/m/Y h:i", strtotime($value['dia_hora']));
                $horas = $value['horas_solicitadas'];
                $reingreso = date("d/m/Y h:i", strtotime($value['hora_ingreso']));
                $solicitudes[] = [ 'id'             =>$value['id'],
                                   'created_at'     =>$value['creado'],
                                   'salida'         =>$fecha_hora,
                                   'autorizacion'  =>$autorizacion,
                                   'reingreso'     =>$reingreso,
                                   'horas'       =>$value['horas_solicitadas'],
                                 ];
                
            }
            else {
                //Aqui van los valores en caso de solicitar días
                //dd("entras aqui");
                $fecha_dia = $dias_solicitados;
                $primer_dia = date("d/m/Y", strtotime($fecha_dia[0]));
                $reingreso = $value['fecha_ingreso'];
                $dias = sizeof($dias_solicitados);
                $solicitudes[] = [
                    'id'            => $value['id'],
                    'created_at'    => $value['creado'],
                    'salida'        => $primer_dia,
                    'autorizacion'  => $autorizacion,
                    'reingreso'     => $reingreso,
                    'dias'          => $dias,
                ];

               // dd("entras aqui..?");
            }
            //dd($primer_dia);
        }
            
        $parametros['solicitudes'] = $solicitudes;

        return view('admin.permisos.index', compact('parametros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fecha = date("Y-m-d h:i:s", strtotime(request('fecha')));
        $horas = request('horas');
        $motivo = request('motivo');
        $registro = date("Y-m-d h:i:s");
        $usuario = Auth::user()->crew->id;
        $reingreso = date("Y-m-d h:i:s", strtotime("+ $horas hour", strtotime($fecha)));

        $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.guardado')];

        try {
            $sqlQuery = DB::connection('mysql')->insert("INSERT INTO request_permisions (crew_id, dia_hora, autorizacion, dias_solicitados, fecha_ingreso, hora_ingreso, horas_solicitadas, motivo, created_at)
                                                         VALUE ('{$usuario}', '{$fecha}', 2, NULL, NULL, '{$reingreso}', '{$horas}', '{$motivo}', '{$registro}')");
        }catch(Exception $exception) {
            $mensaje = ['tipo'      =>'error',
                        'mensaje'   =>__('layout.problemas')];
        }
        
        return redirect()->route('admin.permisos.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dias_solicitados = json_decode(request('dias_solicitados'), true);
        sort($dias_solicitados);
        $ultimo = end($dias_solicitados);
        $festivos = Arr::pluck(Holiday::all()->toArray(), 'fecha_descanso');
        $dia_valido = false;
        while($dia_valido == false) {
            $reingreso = date('Y-m-d', strtotime($ultimo.' +1 day'));
            if(date('N', strtotime($reingreso)) <= 5 && !in_array($reingreso, $festivos)) {
                $dia_valido = true;
            } else {
                $ultimo = $reingreso;
            }
        }
        $fecha = date("Y-m-d h:i:s");
        
        //dd($reingreso);
        try {
            $registrar = RequestPermisions::create(['crew_id'           => Auth::user()->crew->id,
                                                    'dia_hora'          => null,
                                                    'autorizacion'      => 2,
                                                    'dias_solicitados'  => json_encode($dias_solicitados),
                                                    'fecha_ingreso'     => $reingreso,
                                                    'horas_solicitadas' => 0,
                                                    'motivo'            => request('motivo2'),
                                                    'created_at'        => $fecha,
                                                    'updated_at'        => $fecha,]);
        } catch(Exception $exception) {
            $mensaje = ['tipo'  => 'error', 'mensaje' => __('layout.problemas')];
        }

        return redirect()->route('admin.permisos.index');
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
        //
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
