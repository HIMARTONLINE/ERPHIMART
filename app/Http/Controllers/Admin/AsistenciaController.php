<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Presence;

class AsistenciaController extends Controller
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
        $dias = [];
        $registros = [];
        $tabla = [];
        //VALIDAR SI EL USUARIO SELECCIONO ENTRADA DIA LABORAL O ENTRADA DE COMEDOR
        if (request('fechaComida') || request('entrada')) {
            //validación cuando selecciona la opción de entrada laboral o del día
            if (request('fechaComida')) {
                $parametros['comida'] = request('fechaComida');
                $fecha = explode(' - ', request('fechaComida'));
                $inicio = date("Y-m-d 13:00", strtotime($fecha[0]));
                $final = date("Y-m-d 14:40", strtotime($fecha[1]));
                $datetime1 = new \DateTime($inicio);
                $datetime2 = new \DateTime($final);
                $interval = $datetime1->diff($datetime2);

                for ($i = 0; $i <= $interval->format('%a'); $i++) {
                    if ($i > 0) {
                        $datetime1->modify('+1 day');
                    }

                    if ($datetime1->format('N') <= 5) {
                        $dias[] = $datetime1->format('d/m');
                    }
                }
                $sqlQuery = Presence::select( DB::raw('DATE_FORMAT(presences.registro, \'%d/%m\') AS dia'), 'presences.estatus',
                                              DB::raw('DATE_FORMAT(presences.registro, \'%H:%i\') AS hora'), 'users.name', 'presences.estatus')
                                              ->join('users', 'presences.users_id', '=', 'users.id')
                                              ->whereBetween(DB::raw('presences.registro'), [$inicio, $final])
                                              ->where('presences.estatus', '=', '0')
                                              ->whereBetween(DB::raw('DATE_FORMAT(presences.registro, \'%H:%i\')'), ['13:00', '15:00'])
                                              ->orderBy('presences.registro', 'asc')
                                              ->get()->toArray();
                foreach ($sqlQuery as $key => $value) {
                    
                    if (!array_key_exists($value['name'], $registros)) {
                        $registros[$value['name']] = []; 
                        //Condicion para marcar vacío el valor de hora ya que sin esto le asigana a la variable una hora cuando el registro no existe
                        $salida = $value['hora'];

                        foreach($dias as $ke => $val) {
                            $registros[$value['name']][$val] = ['estatus' => '',
                                                                'hora'    => '',
                                                                'salida'  => $salida
                                                            ];
                        }
                    }
                    $registros[$value['name']][$value['dia']] = ['estatus' => $value['estatus'],
                                                                    'hora'    => $value['hora'],
                                                                    'salida'  => $salida
                                                                ];
                                        
                }
                
            } 
            else {
                //dd(request('entrada'));
                $parametros['entrada'] = request('entrada');
                $fecha = explode(' - ', request('entrada'));
                $inicio = date("Y-m-d", strtotime($fecha[0]));
                $final = date("Y-m-d", strtotime($fecha[1]));
                $datetime1 = new \DateTime($inicio);
                $datetime2 = new \DateTime($final);
                $interval = $datetime1->diff($datetime2);

                for ($i = 0; $i <= $interval->format('%a'); $i++) {
                    if ($i > 0) {
                        $datetime1->modify('+1 day');
                    }

                    if ($datetime1->format('N') <= 5) {
                        $dias[] = $datetime1->format('d/m');
                    }
                }
                $sqlQuery = Presence::select( DB::raw('DATE_FORMAT(presences.registro, \'%d/%m\') AS dia'),
                                              DB::raw('DATE_FORMAT(presences.registro, \'%H:%i\') AS hora'), 'users.name', 'presences.estatus')
                                            ->join('users', 'presences.users_id', '=', 'users.id')
                                            ->whereBetween(DB::raw('DATE(presences.registro)'), [$inicio, $final])
                                            ->where('presences.estatus', '!=', '0')
                                            ->orderBy('presences.registro', 'asc')
                                            ->get()->toArray();
                foreach ($sqlQuery as $key => $value) {

                    if (!array_key_exists($value['name'], $registros)) {
                        $registros[$value['name']] = []; 
                        foreach($dias as $ke => $val) {
                            $registros[$value['name']][$val] = ['estatus' => '',
                                                                'hora'    => ''];
                        }
                    }
                    $registros[$value['name']][$value['dia']] = ['estatus' => $value['estatus'],
                                                                    'hora'    => $value['hora']
                                                                ];
                                            
                }

            }
            
        }
        
        $parametros = [
            'encabezados' => $dias,
            'registros'   => $registros,
            //'permitido'   => $permitido,
            'entrada'     => '',
            'comida'      => '',
            ];  
        //dd($registros);
        return view('admin.asistencias.index', compact('parametros'));
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
