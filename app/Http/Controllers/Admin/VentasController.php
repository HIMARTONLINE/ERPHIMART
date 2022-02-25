<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $mes = $parametros['mes'] = request('mes');
        
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

            $sumaStock[] = intval($valorstock['quantity']);
            
            foreach($arrayProduct['products']['product'] as $indexProdu => $valorProduct) {
                
                if($valorstock['id_product'] == $valorProduct['id']) {

                    $sumaCompra[] = floatval($valorProduct['wholesale_price'])  * floatval($valorstock['quantity']);
                    $sumaVenta[] =  floatval($valorProduct['price']) * floatval($valorstock['quantity']);
                }
                
            }
        }

        
        
        foreach($arrayOrders['orders']['order'] as $indexOrder => $valorOrder) {

            $fecha[] = date('Y-m-d', strtotime($valorOrder['date_add']));

            if($valorOrder['current_state'] == "5" || $valorOrder['current_state'] == "4" || $valorOrder["current_state"] == "44") {

                $suma[] = floatval($valorOrder['total_paid']);

            }       
                
        }
        //dd($fecha);
        $totalStock = array_sum($sumaStock);
        $totalCompra = array_sum($sumaCompra);
        $totalVenta = array_sum($sumaVenta);
        $total = array_sum($suma);
        
        $parametros = ['totalVentaOrden'     => $total,
                        'totalCompra'        => $totalCompra,
                        'totalVentaProdu'    => $totalVenta,
                        'totalStock'         => $totalStock,
                        'mes'                => '',
                        'rango'              => ''];
                    

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
