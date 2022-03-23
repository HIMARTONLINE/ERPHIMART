<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Prestashop;
use App\Ordenes_facturadas;

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
    public function index(Request $request)
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
        }else{
            $mes = '';
        }

        $mes = request('mes');
        $ordenes_facturadas = Ordenes_facturadas::select('id_orden')->whereMonth('created_at', $mes)->get();

        $parametros = ['mes'    => "",
                       'rango'  => "",
                       'ordenes'=> $ordenes,
                       'meses'  => $meses,
                       'mes_factura' => $mes,
                    ];

        return view('admin.facturas.index', compact('parametros','ordenes_facturadas'));

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
        $mes = request('mes_factura');

        if(!empty($_REQUEST['orden'])){
            $no_facturar = request('orden');
            /*
            if(in_array(299, $no_facturar)){
                echo 'EXISTE 299';
                return false;
            }*/
        }else{
            $no_facturar = [];
        }

        $urlOrdenes['resource'] = 'orders/?display=full';
        $xmlOrdenes = Prestashop::get($urlOrdenes);

        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $jsonOrdenes = json_encode($xmlOrdenes);
        $arrayOrdenes = json_decode($jsonOrdenes, true);

        $jsonProdu = json_encode($xmlProdu);
        $arrayProdu = json_decode($jsonProdu, true);  

        $mes_select = date("Y-$mes");

        foreach($arrayOrdenes['orders']['order'] as $i => $v) {

            if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                
                $fecha = date("Y-m", strtotime($v['date_add']));
                $orden = $v['id'];

                if($fecha == $mes_select) {
                    if(!in_array($orden, $no_facturar)){
                        // $suma[] = floatval($v['total_paid']);
                        $id_orden = $v['id'];
                        $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];
                    }
                }
            }
        }
        /*
        $cdad_piezas = [];

        foreach($arrayOrdenes['orders']['order'] as $index => $value) {

            $fecha = date("Y-m", strtotime($value['date_add']));
            $orden = $value['id'];

            if(!in_array($orden, $no_facturar)){

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    foreach($arrayProdu['products']['product'] as $inPro => $valPro) {
                        
                        $fecha = date("Y-m", strtotime($value['date_add']));
                        $orden = $value['id'];
                        
                        if($fecha == $mes_select) {
        */
        function orden_a_facturar($orden, $mes){
            $urlOrdenes['resource'] = 'orders/?display=full';
            $xmlOrdenes = Prestashop::get($urlOrdenes);

            $jsonOrdenes = json_encode($xmlOrdenes);
            $arrayOrdenes = json_decode($jsonOrdenes, true);

            $mes_select = date("Y-$mes");

            foreach($arrayOrdenes['orders']['order'] as $i => $v) {

                if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                    
                    $fecha = date("Y-m", strtotime($v['date_add']));

                    if($fecha == $mes_select) {
                        if($v['id'] == $orden){
                            // $suma[] = floatval($v['total_paid']);
                            $ejem[$orden] = $v['associations']['order_rows']['order_row'];
                        }
                    }
                }
            }
            
            $cdad_piezas = [];

            foreach($ejem as $key => $row){
                if($orden == $key){
                    if(in_array(0, $ejem[$key])){              

                        $id_produ = $ejem[$key]['product_id'];

                        if(!array_key_exists($ejem[$key]['product_id'], $cdad_piezas)){
                            $cdad_piezas[$id_produ] = $ejem[$key]['product_quantity'];
                        }else{
                            $cdad_piezas[$id_produ] += $ejem[$key]['product_quantity'];
                        }
                      
                    }else{

                        foreach($ejem[$key] as $filas){
                           
                            $id_produ = $filas['product_id'];

                            if(!array_key_exists($filas['product_id'], $cdad_piezas)){
                                $cdad_piezas[$id_produ] = $filas['product_quantity'];
                            }else{
                                $cdad_piezas[$id_produ] += $filas['product_quantity'];
                            }
                            
                        }
                    
                    }
                }
                
            }
            $suma_produ = array_sum($cdad_piezas);
            return $suma_produ;
        }
        /*
                            
                        }
                    }
                    
                }
            
            }
            
        }
        */
        foreach($arrayOrdenes['orders']['order'] as $key => $value) {

            $fecha = date("Y-m", strtotime($value['date_add']));
            $orden = $value['id'];
            
            if($fecha == $mes_select) {
                if(!in_array($orden, $no_facturar)){
                    if($value['current_state'] == "3" || $value['current_state'] == "5" || $value['current_state'] == "4" || $value['current_state'] == "2") {
                    
                       dd(orden_a_facturar($orden, $mes));

                    }
                }else{
                }
            }
        }

        // dd($sumaTotalPiezas);

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
