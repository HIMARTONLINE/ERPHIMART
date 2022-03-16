<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Prestashop;


class VentasController extends Controller
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
    public function index(Request $request)
    {   
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril',
                  'Mayo', 'Junio', 'Julio', 'Agosto',
                  'septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ];
        
        $mes = request('mes');
        $rango = request('rango');
        
        $urlOrdes['resource'] = 'orders/?display=full';
        $xmlOrders = Prestashop::get($urlOrdes);

        $urlProduct['resource'] = 'products/?sort=[id_ASC]&display=full';
        $xmlProduct = Prestashop::get($urlProduct);

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $jsonOrders = json_encode($xmlOrders);
        $arrayOrders = json_decode($jsonOrders, true);

        $jsonProduct = json_encode($xmlProduct);
        $arrayProduct = json_decode($jsonProduct, true);

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);
        
        foreach($arrayStock['stock_availables']['stock_available'] as $indexStock => $valorstock) {

            //$sumaStock[] = intval($valorstock['quantity']);
            
            foreach($arrayProduct['products']['product'] as $indexProdu => $valorProduct) {
                
                $tablaProductos[] = $valorProduct;

                if($valorstock['id_product'] == $valorProduct['id']) {

                    $sumaVenta[] =  floatval($valorProduct['price']) * floatval($valorstock['quantity']);
                }
                
            }
        }

        foreach($arrayOrders['orders']['order'] as $i => $v) {
            
            $fecha = date('Y-m-d', strtotime($v['date_add']));

            if($rango != null) {

                $fechas = explode(' - ', $rango);

                $inicio = date('Y-m-d', strtotime($fechas[0]));
                $final = date('Y-m-d', strtotime($fechas[1]));

                if($fecha >= $inicio && $fecha <= $final) {

                    if($v['current_state'] == "3"|| $v['current_state'] == "5" || $v['current_state'] == "4" || $v['current_state'] == "2") {
                    
                        $suma[] = floatval($v['total_paid']);
                        $ejem[] = $v['associations']['order_rows']['order_row'];
                        $rangoGraf[] = date('Y-m-d', strtotime($v['date_add']));
                    }
                }

            } else {
               
                if($mes != null) {
                    
                    $mes = date("Y-$mes");
                    
                    foreach($arrayOrders['orders']['order'] as $key => $value) {
                        
                        $fecha = date('Y-m', strtotime($value['date_add']));
                        
                        if($fecha == $mes) { 

                            if($value['current_state'] == "3"|| $value['current_state'] == "5" || $value['current_state'] == "4" || $value['current_state'] == "2") {
                    
                                $suma[] = floatval($value['total_paid']);
                                $ejem[] = $value['associations']['order_rows']['order_row'];
                                $rangoGraf[] = date('Y-m-d', strtotime($value['date_add']));
                            }

                        }
                    }
                    
                } else {

                    if($v['current_state'] == "3"|| $v['current_state'] == "5" || $v['current_state'] == "4" || $v['current_state'] == "2") {
                    
                        $suma[] = floatval($v['total_paid']);
                        $ejem[] = $v['associations']['order_rows']['order_row'];
                        $rangoGraf[] = date('Y-m', strtotime($v['date_add']));
                    }
                }
            }
        }

        try {
            
            foreach($arrayProduct['products']['product'] as $inPro => $valPro) {

                foreach($ejem as $key => $row){
                    
                    if(in_array(0, $ejem[$key])){
    
                        if($valPro['id'] == $ejem[$key]['product_id']) {
    
                            $sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                        }
                        
                        
                    }else{
                        foreach($ejem[$key] as $filas){
    
                            if($valPro['id'] == $filas['product_id']) {
    
                                $sumar2[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                            }
                            
                        }
                    
                    }
                }
            }

        }catch (Exception $e) {
            return back()->with('Error', 'No se encontraron registros de pedidos con pago confirmado');
        }

        $datosGraf = array_count_values($rangoGraf);
        //dd($datosGraf);

        $sumaCompra = array_merge($sumar, $sumar2);

        $totalCompra = array_sum($sumaCompra);
        $totalVenta = array_sum($sumaVenta);
        $total = array_sum($suma);
        
        $parametros = ['totalVentaOrden'     => $total,
                        'totalCompra'        => $totalCompra,
                        'totalVentaProdu'    => $totalVenta,
                        //'totalStock'         => $totalStock,
                        'mes'                => '',
                        'meses'              => $meses,
                        'rangoGra'          => $datosGraf,
                        'rango'              => ''];
                    
        //dd($mes);

        return view('admin.ventas.index', compact('parametros'));
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
