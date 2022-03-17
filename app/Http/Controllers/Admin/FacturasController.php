<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Prestashop;

class FacturasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    public function index()
    {
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril',
                  'Mayo', 'Junio', 'Julio', 'Agosto',
                  'septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ];

        $ordenes = [];

        if (request('mes')) {

            $mes = request('mes');
            $mes = date("Y-$mes");

            $urlOrdenes['resource'] = 'orders/?display=full';
            $xmlOrdenes = Prestashop::get($urlOrdenes);
    
            $jsonOrdenes = json_encode($xmlOrdenes);
            $arrayOrdenes = json_decode($jsonOrdenes, true);

            foreach($arrayOrdenes['orders']['order'] as $key => $value) {

                $fecha = date("Y-m", strtotime($value['date_add']));
                
                if($fecha == $mes) {

                    if($value['current_state'] == "3"|| $value['current_state'] == "5" || $value['current_state'] == "4" || $value['current_state'] == "2") {

                        $tablaOrdenes[] = $value;
                    }
                }
            }
    
            $ordenes = $tablaOrdenes;
        }

        $parametros = ['mes'    => "",
                       'rango'  => "",
                       'ordenes'=> $ordenes,
                       'meses'  => $meses,
                    ];
        return view('admin.facturas.index', compact('parametros'));
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
        $orden = [];
        $regimen = ['*Personas', 'Asalariados'];

        $urlOrden['resource'] = 'orders/' . $id;
        $xmlOrden = Prestashop::get($urlOrden);

        $jsonOrden = json_encode($xmlOrden);
        $arrayOrden = json_decode($jsonOrden, true);
        
        foreach($arrayOrden as $key => $value) {
            $tablaOrden = $value;
        }

        $orden = $tablaOrden;
        //dd($orden);
        return view('admin.facturas.edit', compact('orden', 'regimen'));
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
