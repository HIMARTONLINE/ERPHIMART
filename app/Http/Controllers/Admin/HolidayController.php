<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;
use DB;

class HolidayController extends Controller
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
        //
        $sqlQuery = Holiday::select('*')
                                    ->get()->toArray();

        $parametros = ['parametros' => $sqlQuery];

        return view('admin.festivos.index', compact('parametros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.festivos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $festividad = request('festividad');
        $fecha_descanso = date('Y-m-d', strtotime(request('fecha_descanso')));
        $fecha_conm = date('Y-m-d', strtotime(request('fecha_conmemorativa')));

        DB::table('holidays')->insert(['festividad' => $festividad, 'fecha_descanso' => $fecha_descanso,
                                      'fecha_conmemorativa' => $fecha_conm]);
        DB::commit();

        return redirect('admin/festivos');
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
        $sqlQuery = Holiday::select('*')->where('id', "$id")->get()->toArray();

        foreach($sqlQuery as $key => $value) {

            $parametros = $value;
        }
        //dd($parametros);
        return view('admin.festivos.edit', compact('parametros'));
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
        dd(request('festividad'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Holiday::where('id', $id)->delete();

        return redirect('admin/festivos');
    }
    public function getRegistros(Request $request) {
        $permitido = $this->buscaPermiso('festivos.index', Auth::user()->permision_id);
        $buscar = request('search');
        $orden = request('order');
        $ordenamiento = ['campo' => 'holidays.fecha_descanso',
                         'dir'   => 'asc'];
        $campos = ['holidays.festividad', 'holidays.fecha_descanso', 'holidays.fecha_conmemorativa'];
        if(!empty($orden)) {
            $ordenamiento = ['campo' => $campos[$orden[0]['column']],
                             'dir'   => $orden[0]['dir']];
        }

        $registros = Holiday::select('holidays.id', 'holidays.festividad',
                                     DB::raw('DATE_FORMAT(holidays.fecha_descanso, \'%d/%m/%Y\') AS fecha_descanso'), 
                                     DB::raw('DATE_FORMAT(holidays.fecha_conmemorativa, \'%d/%m/%Y\') AS fecha_conmemorativa'))
                            ->when(!empty($buscar['value']) , function($query) use($buscar) {
                               return $query->where('holidays.festividad', 'LIKE', "%{$buscar['value']}%")
                                            ->orWhere('holidays.fecha', 'LIKE', "%{$buscar['value']}%");
                            })
                            ->orderBy($ordenamiento['campo'], $ordenamiento['dir'])
                            ->skip(request('start'))->take(request('length'))->get();

        $datos = [];
        foreach ($registros as $key => $value) {
            $datos[] = ['<div class="nt"><span class="nl">'.$value->festividad.'</span></div>',
                        '<div class="nt"><span class="nl"><span style="display: none;">'.strtotime($value->fecha_descanso).'</span>'.$value->fecha_descanso.'</span></div>',
                        '<div class="nt"><span class="nl"><span style="display: none;">'.strtotime($value->fecha_descanso).'</span>'.$value->fecha_conmemorativa.'</span></div>',
                        '<a href="editar" data-id="'.$value->id.'" class="action-icon" data-toggle="tooltip" data-placement="top" data-original-title="'.__('layout.editar').'"> <i class="mdi mdi-pencil"></i></a>
                         <a href="eliminar" data-id="'.$value->id.'" class="action-icon" data-toggle="tooltip" data-placement="top" data-original-title="'.__('layout.eliminar').'"> <i class="mdi mdi-delete"></i></a>'];
        }

        $resultado = ['draw'            => request('draw'),
                      'recordsTotal'    => sizeof($datos),
                      'recordsFiltered' => sizeof($datos),
                      'data'            => $datos];

        header('Content-Type: application/json');
        echo json_encode($resultado);
        die();
    }

    public function getBloqueados() {
        $registros = Holiday::select('holidays.id', 'holidays.festividad', 'holidays.fecha_descanso', 'holidays.fecha_conmemorativa')
                            ->orderBy('holidays.fecha_descanso', 'asc')->get()->toArray();

        $resultado = [];
        foreach ($registros as $key => $value) {
            $resultado[] = ['id'              => $value['id'],
                            'title'           => $value['festividad'],
                            'start'           => $value['fecha_descanso'],
                            'allDay'          => true,
                            'rendering'       => 'background',
                            'backgroundColor' => '#536de6',
                            'eventConstraint' => ['start'  => $value['fecha_descanso'],
                                                  'allDay' => date('Y-m-d', strtotime($value['fecha_descanso'] . ' +1 day')), ]];
        }

        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        die();
    }
}
