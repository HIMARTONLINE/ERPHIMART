<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Prestashop;
use App\Product;
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
                  'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
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

        if(!$ordenes_facturadas){
            $ordenes_facturadas = [];
        }

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
            
            $array_productos = [];

            foreach($ejem as $key => $row){
                if($orden == $key){
                    if(in_array(0, $ejem[$key])){              

                        $id_produ = $ejem[$key]['product_id'];

                        if(!array_key_exists($ejem[$key]['product_id'], $array_productos)){
                            $array_productos[$id_produ] = array(
                                'id_producto' => $ejem[$key]['product_id'],
                                'cantidad' => $ejem[$key]['product_quantity'],
                                'nombre' => $ejem[$key]['product_name'],
                                'referencia' => $ejem[$key]['product_reference'],
                                'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl']
                            );
                        }else{
                            $array_productos[$id_produ] = array(
                                'id_producto' => $ejem[$key]['product_id'],
                                'cantidad' => $ejem[$key]['product_quantity'],
                                'nombre' => $ejem[$key]['product_name'],
                                'referencia' => $ejem[$key]['product_reference'],
                                'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl']
                            );
                        }
                      
                    }else{

                        foreach($ejem[$key] as $filas){
                           
                            $id_produ = $filas['product_id'];

                            if(!array_key_exists($filas['product_id'], $array_productos)){
                                $array_productos[$id_produ] = array(
                                    'id_producto' => $filas['product_id'],
                                    'cantidad' => $filas['product_quantity'],
                                    'nombre' => $filas['product_name'],
                                    'referencia' => $filas['product_reference'],
                                    'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                    'precio_unitario_sin_iva' => $filas['unit_price_tax_excl']
                                );
                            }else{
                                $array_productos[$id_produ] = array(
                                    'id_producto' => $filas['product_id'],
                                    'cantidad' => $filas['product_quantity'],
                                    'nombre' => $filas['product_name'],
                                    'referencia' => $filas['product_reference'],
                                    'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                    'precio_unitario_sin_iva' => $filas['unit_price_tax_excl']
                                );
                            }
                            
                        }
                    
                    }
                }
                
            }
            // $suma_produ = array_sum($cdad_piezas);
            /*$productos_orden = [
                'cdad_piezas' => $cdad_piezas
            ];*/ 
            return $array_productos;

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
                        
                        $productos_orden = orden_a_facturar($orden, $mes);

                        // dd($productos_orden);
                        
                        foreach($productos_orden as $key => $row){
                            $producto = Product::where('id_product', $row['id_producto'])->first();

                            if($producto){
                                $clabe_sat = $producto->clabe_sat;
                                $unidad_medida_cod = $producto->unidad_medida;
                                $unidad_medida_nom = 'Unidad de producto';
                            }else{
                                $clabe_sat = '84111506';
                                $unidad_medida_cod = 'E48';
                                $unidad_medida_nom = 'Unidad de producto';
                            }

                            $subtotal_produ = $row['cantidad'] * number_format($row['precio_unitario_sin_iva'], 2);
                            $total_produ = $row['cantidad'] * number_format($row['precio_unitario_sin_iva'], 2);
                            $precio_unitario_con_iva = number_format($row['precio_unitario_con_iva'], 2);
                            $precio_unitario_sin_iva = number_format($row['precio_unitario_sin_iva'], 2);

                            if($precio_unitario_con_iva != $precio_unitario_sin_iva){
                                // $total_iva = $precio_unitario_con_iva - $precio_unitario_sin_iva;
                                $array_imp = array(
                                    "Name" => "IVA",
                                    "Rate" => "0.16",
                                    "Total" => $precio_unitario_con_iva,
                                    "Base" => "40",
                                    "IsRetention" => "false"
                                );
                            }else{
                                $array_imp = [];
                            }

                            $products[] = array(
                                "Quantity" => $row['cantidad'],
                                "ProductCode" => $clabe_sat,
                                "UnitCode" => $unidad_medida_cod,
                                "Unit" => $unidad_medida_nom,
                                "Description" => $row['nombre'],
                                "IdentificationNumber" => $row['id_producto'],
                                "UnitPrice" => number_format($row['precio_unitario_sin_iva'], 2),
                                "Subtotal" => $subtotal_produ,            
                                "Discount" => "0",
                                "DiscountVal" => "0",
                                "Taxes" => array($array_imp),
                                "Total" => $total_produ
                            );

                        }

                        // dd(orden_a_facturar($orden, $mes));

                    }
                }else{
                }
            }
        }
        
        $client = new Client(['base_uri' => 'https://apisandbox.facturama.mx/']);
        // dd($products);


        $response = $client->request('POST', '/2/cfdis', [
            'auth' => ['DEVHIMART', 'Torre123'], 
            // 'auth' => ['JERA EBUSINESS SA DE CV', 'Moon2392610'], 
            'form_params' => [
                "Receiver" => [
                    "Name" => "Prueba Himart 2022",
                    "CfdiUse" => "P01",
                    "Rfc" => "XAXX010101000"        
                ],
                "CfdiType" => "I",
                "NameId" => "1",
                "ExpeditionPlace" => "45200",
                "PaymentForm" => "03",
                "PaymentMethod" => "PUE",
                "Decimals" => "2",
                "Currency" => "MXN",
                "Date" => "2022-03-25",
                "Items" => $products
            ]
        ]);

        echo $response->getBody();

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
