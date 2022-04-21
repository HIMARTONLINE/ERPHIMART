<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacation;
use App\Models\Crew;
use App\Models\RequestVacation;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $vacaciones = Arr::pluck(Vacation::select(DB::raw('SUM(vacations.pendientes) AS vacaciones'))
                                         ->leftjoin('crews', 'vacations.crew_id', '=', 'crews.id')
                                         ->where('crews.user_id', '=', Auth::user()->id)
                                         ->get()
                                         ->toArray(), 'vacaciones');

        $datos = RequestVacation::select('id', 'autorizacion', 'dias_solicitados', 'pendientes',
                                         DB::raw('DATE_FORMAT(fecha_ingreso, \'%d/%m/%Y\') AS fecha_ingreso'), 
                                         DB::raw('DATE_FORMAT(created_at, \'%d/%m/%Y\') AS creado'))
                                      ->where('crew_id', '=', Auth::user()->crew->id)
                                      ->orderBy('created_at', 'desc')
                                      ->get()->toArray();

        $solicitudes = [];
        foreach ($datos as $key => $value) {
            $dias_solicitados = $this->anexarFestivos(json_decode($value['dias_solicitados'], true));
            switch($value['autorizacion']) {
                case 1:
                    $autorizacion = 'text-success';
                break;

                case 2:
                    $autorizacion = 'text-warning';
                break;

                default:
                    $autorizacion = 'text-danger';
                break;
            }

            $solicitudes[] = ['id'               => $value['id'],
                              'autorizacion'     => $autorizacion,
                              'dias_solicitados' => $dias_solicitados,
                              'tomados'          => sizeof($dias_solicitados),
                              'pendientes'       => $value['pendientes'],
                              'fecha_ingreso'    => $value['fecha_ingreso'],
                              'created_at'       => $value['creado'],];
        }
        
        $parametros = ['url'         => route('admin.vacaciones.create'),
                       'vacaciones'  => $vacaciones[0]==null?0:$vacaciones[0],
                       'solicitudes' => $solicitudes];
        
        return view('admin.vacaciones.index', compact('parametros'));
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
        $dias_solicitados = json_decode(request('dias_solicitados'), true);
        sort($dias_solicitados);
        $ultimo = end($dias_solicitados);
        $festivos = Arr::pluck(Holiday::all()->toArray(), 'fecha_descanso');
        $dia_valido = false;
        while($dia_valido == false) {
            $regreso = date('Y-m-d', strtotime($ultimo.' +1 day'));
            if(date('N', strtotime($regreso)) <= 5 && !in_array($regreso, $festivos)) {
                $dia_valido = true;
            } else {
                $ultimo = $regreso;
            }
        }

        $mensaje = ['tipo'    => 'success',
                    'mensaje' => __('layout.guardado')];
        try {
            $vacaciones = Vacation::select('id','servicio', 'correspondientes', 'pendientes', 'tomados')
                                  ->where('crew_id', '=', Auth::user()->crew->id)
                                  ->where('pendientes', '>', '0')
                                  ->orderBy('created_at', 'asc')
                                  ->get()->toArray();

            $total = sizeof($dias_solicitados);
            foreach ($vacaciones as $key => $value) {
                $pendientes = $value['pendientes'];
                if($total > 0) {
                    if($value['pendientes'] <= $total) {
                        Vacation::where('id', '=', $value['id'])->update(['pendientes' => 0,
                                                                           'tomados'    => $value['correspondientes']]);
                        $total -= $value['pendientes'];
                    } else {
                        Vacation::where('id', '=', $value['id'])->update(['pendientes' => $value['pendientes']-$total,
                                                                           'tomados'    => $value['tomados']+$total]);
                        $total = 0;
                    }
                }
            }

            $vacaciones = Arr::pluck(Vacation::select(DB::raw('SUM(vacations.pendientes) AS vacaciones'))
                                             ->leftjoin('crews', 'vacations.crew_id', '=', 'crews.id')
                                             ->where('crews.user_id', '=', Auth::user()->crew->id)
                                             ->get()
                                             ->toArray(), 'vacaciones');

            $registro = RequestVacation::create(['crew_id'          => Auth::user()->crew->id,
                                                 'autorizacion'     => 2,
                                                 'dias_solicitados' => json_encode($dias_solicitados),
                                                 'pendientes'       => $pendientes - $total,
                                                 'fecha_ingreso'    => $regreso,]);
        } catch(Exception $exception) {
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('layout.problemas')];
        }
                                                 

        return redirect()->route('admin.vacaciones.index')->with($mensaje['tipo'], $mensaje['mensaje']);
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
        $mensaje = ['tipo'    => 'success',
            'mensaje' => __('layout.eliminado')];
        try {
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
                $registro = RequestVacation::where('id', '=', $id)->delete();

        } catch(Exception $exception) {
            $mensaje = ['tipo'    => 'error',
                        'mensaje' => __('layout.problemas')];
        }

        return redirect()->route('admin.vacaciones.index')->with($mensaje['tipo'], $mensaje['mensaje']);
    }
    public function addVacation(Request $request) {

        $mes = date('m', strtotime(date('Y-m-d').' +4 month'));
        $datos = Crew::select('crews.id', 'crews.ingreso', 'users.name', 'users.email')
                     ->leftjoin('users', 'crews.user_id', '=', 'users.id')
                     ->where('crews.activo', '=', '1')
                     ->whereMonth('crews.ingreso', $mes)
                     ->get()->toArray();

        foreach ($datos as $key => $value) {
            $datetime1 = date_create($value['ingreso']);
            $datetime2 = date_create(date('Y').date('-m-d', strtotime($value['ingreso'])));
            $interval = date_diff($datetime1, $datetime2);
            $servicio = $interval->format('%y');

            if($servicio >= 1) {
                switch($servicio) {
                    case 1:
                        $dias = 6;
                    break;

                    case 2:
                        $dias = 8;
                    break;

                    case 3:
                        $dias = 10;
                    break;

                    case 4:
                        $dias = 12;
                    break;

                    default:
                        $dias = 12 + (2*floor($servicio/5));
                    break;
                }

                $resultado = Vacation::create(['crew_id'          => $value['id'],
                                               'servicio'         => $servicio,
                                               'correspondientes' => $dias,
                                               'pendientes'       => $dias,
                                               'tomados'          => 0,]);

            }
        }
    }
}
