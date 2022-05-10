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
        //Validaciones
        $rules = array(
            'empresa'  => 'required|string|max:100',
            'servicio' => 'required|string|max:255',
            'enlace'   => 'required|string|max:255',
            'usuario'  => 'required|string|max:255',
            'clave'    => 'required|string|max:255',
            'user_id'  => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

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

        return route('/admin/password');
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
