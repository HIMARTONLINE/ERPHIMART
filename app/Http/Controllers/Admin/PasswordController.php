<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Savepassword;
use DB;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $contrasenias = Savepassword::select('*')->get();
        
        //dd($contrasenias);
        return view('admin.password.index', compact('contrasenias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
        DB::table('contrasenias')
            ->insert([
                [
                    'empresa'  => $request->empresa,
                    'servicio' => $request->servicio,
                    'enlace'   => $request->enlace,
                    'usuario'  => $request->usuario,
                    'estado'   => $request->estado,
                    'clave'    => $request->clave
                ]
            ]);

        return redirect()->route('admin.password.index');
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
        if (request()->ajax()) {
            $data = Savepassword::findOrFail($id);
            return response()->json(['data' => $data]);
        }
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
        //dd($request->hidden_id);
        DB::table('contrasenias')
        ->where('id', '=', $request->hidden_id)
        ->update([
            'empresa'  => $request->empresa,
            'servicio' => $request->servicio,
            'enlace'   => $request->enlace,
            'usuario'  => $request->usuario,
            'estado'   => $request->estado,
            'clave'    => $request->clave
        ]);

        return redirect()->route('admin.password.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Eliminando la categori por id
        $data = Savepassword::findOrFail($id);
        $data->delete();
        return redirect()->route('admin.password.index');
    }
}
