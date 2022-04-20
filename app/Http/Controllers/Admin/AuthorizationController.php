<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestVacation;
use App\Models\RequestPermisions;
use App\Models\Vacation;
use App\Models\Holiday;
use DB;

class AuthorizationController extends Controller
{
    public function __construct() {
        //parent::__construct();
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $vacaciones = RequestVacation::select('request_vacations.id', 'request_vacations.autorizacion', 'request_vacations.dias_solicitados', 'request_vacations.pendientes', 
                                              DB::raw('DATE_FORMAT(request_vacations.fecha_ingreso, \'%d/%m/%Y\') AS reingreso'), 
                                              DB::raw('DATE_FORMAT(request_vacations.created_at, \'%d/%m/%Y\') AS solicitado'), 'users.name', 'users.foto')
                                     ->leftjoin('crews', 'crews.id', '=', 'request_vacations.crew_id')
                                     ->leftjoin('users', 'users.id', '=', 'crews.user_id')
                                     ->where('request_vacations.autorizacion', '=', 2)
                                     ->orderBy('request_vacations.created_at', 'asc')
                                     ->get()->toArray();
        foreach ($vacaciones as $key => $value) {
            $dias = json_decode($value['dias_solicitados'], true);
            $vacaciones[$key]['dias_solicitados'] = $this->anexarFestivos($dias);
            $vacaciones[$key]['inicio'] = date('d/m/Y', strtotime($dias[0]));
            $vacaciones[$key]['tomados'] = sizeof($dias);
            switch($value['autorizacion']) {
                case 1:
                    $vacaciones[$key]['estatus'] = 'text-success';
                break;

                case 2:
                    $vacaciones[$key]['estatus'] = 'text-warning';
                break;

                default:
                    $vacaciones[$key]['estatus'] = 'text-danger';
                break;
            }
        }
        
        
        $permisos = RequestPermisions::select('request_permisions.id', 'request_permisions.dia_hora', 'request_permisions.autorizacion', 
                                              'request_permisions.dias_solicitados AS dias', 'request_permisions.fecha_ingreso',
                                              'request_permisions.hora_ingreso', 'request_permisions.created_at', 'request_permisions.horas_solicitadas AS horas',
                                              'request_permisions.motivo',
                                                           DB::raw('DATE_FORMAT(request_permisions.created_at, \'%d/%m/%Y\') AS solicitado'), 'users.name', 'users.foto')
                                                    ->leftjoin('crews', 'crews.id', '=', 'request_permisions.crew_id')
                                                    ->leftjoin('users', 'users.id', '=', 'crews.user_id')
                                                    ->where('request_permisions.autorizacion', '=', 2)
                                                    /*->where('request_permisions.autorizacion', '!=', 0)
                                                    ->where('request_permisions.autorizacion', '!=', 3)*/
                                                    ->orderBy('request_permisions.created_at', 'asc')
                                                    ->get()->toArray();
        foreach($permisos as $key => $value) {
            $dias = json_decode($value['dias'], true);
            $fecha = date("d/m/Y", strtotime($permisos[$key]['created_at']));
            $permisos[$key]['created_at'] = $fecha;
            switch($value['autorizacion']) {
                case 1:
                    $permisos[$key]['estatus'] = 'text-success';
                break;
                case 2:
                    $permisos[$key]['estatus'] = 'text-warning';
                break;
                case 3:
                    $autorizacion = 'text-primary';
                break;
                default:
                    $permisos[$key]['estatus'] = 'text-danger';
                break;
            }
            /*$comparar = date("d/m/Y h:i", strtotime($permisos[$key]['hora_ingreso'])); //obtener la hora registrada de reingreso de la base de datos
            $fechaReingreso = date("d/m/Y", strtotime(($permisos[$key]['fecha_ingreso'])));
            //verificar el por que actualiza ambos registros en caso de haberse creado uno nuevo para diferente hora
            if(date("d/m/Y h:i") >= $comparar) {    //hacer la comparación de la fecha de la PC con la hora guardada en la base de datos
                dd($comparar);
                if($value['autorizacion'] == 1) {   //una segunda condición para en el caso de que se haya autorizado el permiso y no cambie todas las solictudes de permisos

                    DB::connection('mysql')->table('request_permisions')->where('hora_ingreso', '>=', '{$comparar}')->update(['autorizacion' => 3]);

                }
            }

            if(date("d/m/Y") >= $fechaReingreso) {
                if($value['autorizacion'] == 1) {
                    
                    DB::connection('mysql')->table('request_permisions')->where('fecha_ingreso', '>=', '{$fechaReingreso}')->update(['autorizacion' => 3]);
                }
            }*/
            
            if($permisos[$key]['dias'] == null) {
                //Aqui van los registros creados por horas
                $fecha_hora = date("d/m/Y h:i", strtotime($permisos[$key]['dia_hora']));
                $horas = $permisos[$key]['horas'];
                $reingreso = date("d/m/Y h:i", strtotime($permisos[$key]['hora_ingreso']));
                
                $permisos[$key]['inicio'] = $fecha_hora;
                $permisos[$key]['reingreso'] = $reingreso;
                $permisos[$key]['horas'] = $horas;

            }
            else {
                //Carga los registros creados por días
                $reingreso = date("d/m/Y", strtotime($permisos[$key]['fecha_ingreso']));
                
                $permisos[$key]['dias'] = $dias;
                $permisos[$key]['inicio'] = date("d/m/Y", strtotime($dias[0]));
                $permisos[$key]['reingreso'] = $reingreso;
                $permisos[$key]['dias'] = sizeof($dias);
            }

        }    

        $parametros = ['vacaciones' => $vacaciones,
                       //'acceso'   => $permitido,
                       'permisos' => $permisos];
        
       //dd($parametros);
        
       return view('admin.autorizacion.index', compact('parametros'));
    }

    public function anexarFestivos($dias) {
        $festivos = Holiday::select()
                           ->whereBetween('fecha_descanso', [$dias[0], end($dias)])
                           ->pluck('fecha_descanso')->toArray();

        $dias = array_merge($dias, $festivos);
        sort($dias);

        $lista = [];
        foreach ($dias as $key => $value) {
            if(in_array($value, $festivos)) {
                $lista[] = ['fecha' => $value,
                            'clase' => 'text-holiday'];
            } else {
                $lista[] = ['fecha' => $value,
                            'clase' => ''];
            }
        }

        return $lista;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $request->validate(['autorizacion' => 'required',]);

        $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.actualizado')];
        

        try {
            $registro = RequestVacation::where('id', '=', $id)->update(['autorizacion' => request('autorizacion'),]);
            
            if(request('autorizacion') == 0) {
                $solicitadas = RequestVacation::where('id', '=', $id)->first();
                $datos = Vacation::select()->where('crew_id', '=', $solicitadas->crew_id)
                                 ->orderBy('created_at', 'desc')
                                 ->get()->toArray();
                $total = sizeof(json_decode($solicitadas->dias_solicitados));
                foreach ($datos as $key => $value) {
                    if($value['tomados'] > 0 && $total > 0) {
                        $total -= $value['tomados'];
                        $pendientes = $value['pendientes'] + $value['tomados'];
                        Vacation::where('id', '=', $value['id'])->update(['tomados' => 0,
                                                                          'pendientes' => $pendientes]);
                    }
                }
            } 
        } catch(Exception $exception) {
            
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('layout.problemas')];
        }
        return redirect()->route('admin.autorizacion.index')->with($mensaje['tipo'], $mensaje['mensaje']);
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
    public function Permisos(Request $request, $id) {
        $request->validate(['autorizacionpermisos' => 'required']);
 
        $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.actualizado')];
        try {
            $sqlQuery = RequestPermisions::where('id', '=', $id)->update(['autorizacion' => request('autorizacionpermisos'),]);
        }
        catch(Exception $exception) {
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('layout.problemas')];
        }

        return redirect()->route('admin.autorizacion.index')->with($mensaje['tipo'], $mensaje['mensaje']);
    }
}
