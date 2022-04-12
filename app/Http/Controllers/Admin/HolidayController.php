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
}
