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
        $fecha_factura = request('fecha_factura');

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

        foreach($arrayProdu['products']['product'] as $inPro => $valPro) {
            $id_producto = $valPro['id'];
            $array_products[$id_producto] = $valPro['id_tax_rules_group'];
        }

        if(isset($_REQUEST['factura-individual'])){
            $mes = $request->mes_factura;
            $rfc_cte = $request->rfc;
            $razon_social_cte = $request->razon;
            $orden = $request->id_orden;
            $uso_factura_cte = $request->factura;
            $forma_pago_cte = $request->forma_pago;
            $metodo_pago_cte = $request->metodo_pago;

            function orden_a_facturar($orden, $mes, $array_products){
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
    
                            foreach($array_products as $index => $produ){
    
                                if($index == $id_produ){
                                    if(!array_key_exists($ejem[$key]['product_id'], $array_productos)){
                                        $array_productos[$id_produ] = array(
                                            'id_producto' => $ejem[$key]['product_id'],
                                            'cantidad' => $ejem[$key]['product_quantity'],
                                            'nombre' => $ejem[$key]['product_name'],
                                            'referencia' => $ejem[$key]['product_reference'],
                                            'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                            'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl'],
                                            'rate_iva' => $produ
                                        );
                                    }else{
                                        $array_productos[$id_produ] = array(
                                            'id_producto' => $ejem[$key]['product_id'],
                                            'cantidad' => $ejem[$key]['product_quantity'],
                                            'nombre' => $ejem[$key]['product_name'],
                                            'referencia' => $ejem[$key]['product_reference'],
                                            'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                            'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl'],
                                            'rate_iva' => $produ
                                        );
                                    }
                                }
    
                            }
                          
                        }else{
    
                            foreach($ejem[$key] as $filas){
                               
                                $id_produ = $filas['product_id'];
    
                                foreach($array_products as $index => $produ){
    
                                    if($index == $id_produ){
                                        if(!array_key_exists($filas['product_id'], $array_productos)){
                                            $array_productos[$id_produ] = array(
                                                'id_producto' => $filas['product_id'],
                                                'cantidad' => $filas['product_quantity'],
                                                'nombre' => $filas['product_name'],
                                                'referencia' => $filas['product_reference'],
                                                'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                                'precio_unitario_sin_iva' => $filas['unit_price_tax_excl'],
                                                'rate_iva' => $produ
                                            );
                                        }else{
                                            $array_productos[$id_produ] = array(
                                                'id_producto' => $filas['product_id'],
                                                'cantidad' => $filas['product_quantity'],
                                                'nombre' => $filas['product_name'],
                                                'referencia' => $filas['product_reference'],
                                                'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                                'precio_unitario_sin_iva' => $filas['unit_price_tax_excl'],
                                                'rate_iva' => $produ
                                            );
                                        }
                                    }
                                }
                            }
                        
                        }
                    }
                    
                }
                // $suma_produ = array_sum($cdad_piezas);
    
                return $array_productos;
    
            }
    
            foreach($arrayOrdenes['orders']['order'] as $key => $value) {
    
                $fecha = date("Y-m", strtotime($value['date_add']));
                // $orden = $value['id'];
           
                if($value['current_state'] == "3" || $value['current_state'] == "5" || $value['current_state'] == "4" || $value['current_state'] == "2") {
                    
                    if($value['id'] == $orden){
                        $productos_orden = orden_a_facturar($orden, $mes, $array_products);
                        // dd($productos_orden);
                        
                        foreach($productos_orden as $key => $row){
                            $producto = Product::where('id_product', $row['id_producto'])->first();

                            if($producto){
                                $searchString = " ";
                                $replaceString = "";
                                $clabe_sat = str_replace($searchString, $replaceString, $producto->clabe_sat);
                                $unidad_medida = explode(" - ", $producto->unidad_medida);
                                $unidad_medida_cod = $unidad_medida[0];
                                $unidad_medida_nom = $unidad_medida[1];
                            }else{
                                $clabe_sat = '84111506';
                                $unidad_medida_cod = 'E48';
                                $unidad_medida_nom = 'Unidad de producto';
                            }

                            $subtotal_produ = $row['cantidad'] * number_format($row['precio_unitario_sin_iva'], 2);
                            $total_produ = $row['cantidad'] * number_format($row['precio_unitario_con_iva'], 2);
                            $precio_unitario_con_iva = $row['precio_unitario_con_iva'];
                            $precio_unitario_sin_iva = $row['precio_unitario_sin_iva'];

                            // dd($precio_unitario_con_iva .' '. $precio_unitario_sin_iva .' '. $total_produ .' '. $subtotal_produ);

                            if($precio_unitario_con_iva != $precio_unitario_sin_iva){
                                // $total_iva = $precio_unitario_con_iva - $precio_unitario_sin_iva;
                                $rate_iva = $row['rate_iva'];

                                if($rate_iva == 1){
                                    $rate_iva = 0.16; 
                                    $nombre_impuesto = "IVA";
                                    $objeto_impuesto = "02";
                                }else{
                                    $rate_iva = 0.08;
                                    $nombre_impuesto = "IEPS";
                                    $objeto_impuesto = "02";
                                }
                                $total_iva = $subtotal_produ * $rate_iva;
                                // dd($total_iva);
                                $array_imp = array(
                                    "Name" => $nombre_impuesto,
                                    "Rate" => $rate_iva,
                                    "Total" => number_format($total_iva, 2, '.', ''),
                                    "Base" => number_format($subtotal_produ, 2, '.', ''),
                                    "IsRetention" => "false"
                                );

                                $total_iva_produ = number_format($total_iva, 2, '.', '');
                                $base_iva = $subtotal_produ;
                                $total_produ = $total_iva_produ + $base_iva;
                        
                            }else{
                                $array_imp = [];
                                $objeto_impuesto = "01";
                            }
                            
                            $products[] = array(
                                "Quantity" => $row['cantidad'],
                                "ProductCode" => $clabe_sat,
                                "UnitCode" => $unidad_medida_cod,
                                "Unit" => $unidad_medida_nom,
                                "Description" => $row['nombre'],
                                "IdentificationNumber" => "23",
                                "UnitPrice" => number_format($precio_unitario_sin_iva, 2, '.', ''),
                                "Subtotal" => number_format($subtotal_produ, 2, '.', ''),
                                "Discount" => "0",
                                "DiscountVal" => "0",
                                // "ObjetoImp" => $objeto_impuesto,            
                                "Taxes" => array(
                                    $array_imp
                                ),
                                "Total" => number_format($total_produ, 2, '.', '')
                            );
                                
                        }
                        // dd(orden_a_facturar($orden, $mes));
                    }
                    
                }

            }

            // $client = new Client(['base_uri' => 'https://apisandbox.facturama.mx/']);
            $client = new Client(['base_uri' => 'https://api.facturama.mx/']);
            // dd($products);
            $fecha = date("Y-m-d");
            $autenticacion = base64_encode("HIMART:Himart2022");
            $autenticacion = "Basic " . $autenticacion;
    
            $response = $client->request('POST', '/2/cfdis', [
                // 'auth' => ['DEVHIMART', 'Torre123'], 
                'auth' => ['HIMART', 'Himart2022'], 
                // 'Authorization' => $autenticacion,
                'form_params' => [
                    "Receiver" => [
                        // "Rfc" => "URE180429TM6",
                        "Name" => $razon_social_cte,
                        "CfdiUse" => $uso_factura_cte,
                        "Rfc" => $rfc_cte
                        // "FiscalRegime" => "601",
                        // "TaxZipCode" => "65000"
                    ],
                    "CfdiType" => "I",
                    "NameId" => "1",
                    "ExpeditionPlace" => "06500",
                    "PaymentForm" => $forma_pago_cte,
                    "PaymentMethod" => $metodo_pago_cte,
                    "Decimals" => "2",
                    "Currency" => "MXN",
                    "Date" => $fecha_factura,
                    "Items" => $products
                ]
            ]);

            if($response){
                Ordenes_facturadas::create([
                    'id_orden' => $orden
                ]);

                return redirect()->back();
            }else{
            }
    
            // echo $response->getBody();
            // dd($sumaTotalPiezas);

        }else{
            function orden_a_facturar($orden, $mes, $array_products){
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
    
                            foreach($array_products as $index => $produ){
    
                                if($index == $id_produ){
                                    if(!array_key_exists($ejem[$key]['product_id'], $array_productos)){
                                        $array_productos[$id_produ] = array(
                                            'id_producto' => $ejem[$key]['product_id'],
                                            'cantidad' => $ejem[$key]['product_quantity'],
                                            'nombre' => $ejem[$key]['product_name'],
                                            'referencia' => $ejem[$key]['product_reference'],
                                            'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                            'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl'],
                                            'rate_iva' => $produ
                                        );
                                    }else{
                                        $array_productos[$id_produ] = array(
                                            'id_producto' => $ejem[$key]['product_id'],
                                            'cantidad' => $ejem[$key]['product_quantity'],
                                            'nombre' => $ejem[$key]['product_name'],
                                            'referencia' => $ejem[$key]['product_reference'],
                                            'precio_unitario_con_iva' => $ejem[$key]['unit_price_tax_incl'],
                                            'precio_unitario_sin_iva' => $ejem[$key]['unit_price_tax_excl'],
                                            'rate_iva' => $produ
                                        );
                                    }
                                }
    
                            }
                          
                        }else{
    
                            foreach($ejem[$key] as $filas){
                               
                                $id_produ = $filas['product_id'];
    
                                foreach($array_products as $index => $produ){
    
                                    if($index == $id_produ){
                                        if(!array_key_exists($filas['product_id'], $array_productos)){
                                            $array_productos[$id_produ] = array(
                                                'id_producto' => $filas['product_id'],
                                                'cantidad' => $filas['product_quantity'],
                                                'nombre' => $filas['product_name'],
                                                'referencia' => $filas['product_reference'],
                                                'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                                'precio_unitario_sin_iva' => $filas['unit_price_tax_excl'],
                                                'rate_iva' => $produ
                                            );
                                        }else{
                                            $array_productos[$id_produ] = array(
                                                'id_producto' => $filas['product_id'],
                                                'cantidad' => $filas['product_quantity'],
                                                'nombre' => $filas['product_name'],
                                                'referencia' => $filas['product_reference'],
                                                'precio_unitario_con_iva' => $filas['unit_price_tax_incl'],
                                                'precio_unitario_sin_iva' => $filas['unit_price_tax_excl'],
                                                'rate_iva' => $produ
                                            );
                                        }
                                    }
                                }
                            }
                        
                        }
                    }
                    
                }
                // $suma_produ = array_sum($cdad_piezas);
    
                return $array_productos;
    
            }
    
            foreach($arrayOrdenes['orders']['order'] as $key => $value) {
    
                $fecha = date("Y-m", strtotime($value['date_add']));
                $orden = $value['id'];
           
                if($fecha == $mes_select) {
                    if(!in_array($orden, $no_facturar)){
                        if($value['current_state'] == "3" || $value['current_state'] == "5" || $value['current_state'] == "4" || $value['current_state'] == "2") {
                            $array_ordenes[] = $orden;
                            
                            $productos_orden = orden_a_facturar($orden, $mes, $array_products);
                            // dd($productos_orden);
                            
                            foreach($productos_orden as $key => $row){
                                $producto = Product::where('id_product', $row['id_producto'])->first();
    
                                if($producto){
                                    $searchString = " ";
                                    $replaceString = "";
                                    $clabe_sat = str_replace($searchString, $replaceString, $producto->clabe_sat);
                                    /*$unidad_medida = explode(" - ", $producto->unidad_medida);
                                    $unidad_medida_cod = $unidad_medida[0];
                                    $unidad_medida_nom = $unidad_medida[1];*/
                                    $unidad_medida_cod = 'H87';
                                    $unidad_medida_nom = 'Pieza';
                                }else{
                                    $clabe_sat = '84111506';
                                    $unidad_medida_cod = 'E48';
                                    $unidad_medida_nom = 'Unidad de producto';
                                }
    
                                $subtotal_produ = $row['cantidad'] * $row['precio_unitario_sin_iva'];
                                $total_produ = $row['cantidad'] * $row['precio_unitario_con_iva'];
                                $precio_unitario_con_iva = $row['precio_unitario_con_iva'];
                                $precio_unitario_sin_iva = $row['precio_unitario_sin_iva'];
    
                                // dd($precio_unitario_con_iva .' '. $precio_unitario_sin_iva .' '. $total_produ .' '. $subtotal_produ);
    
                                if($precio_unitario_con_iva != $precio_unitario_sin_iva){
                                    // $total_iva = $precio_unitario_con_iva - $precio_unitario_sin_iva;
                                    $rate_iva = $row['rate_iva'];
    
                                    if($rate_iva == 1){
                                        $rate_iva = 0.16; 
                                        $nombre_impuesto = "IVA";
                                        $objeto_impuesto = "02";
                                    }else{
                                        $rate_iva = 0.08;
                                        $nombre_impuesto = "IEPS";
                                        $objeto_impuesto = "02";
                                    }
                                    $total_iva = $subtotal_produ * $rate_iva;
                                    // dd($total_iva);
                                    $array_imp = array(
                                        "Name" => $nombre_impuesto,
                                        "Rate" => $rate_iva,
                                        "Total" => number_format($total_iva, 2, '.', ''),
                                        "Base" => number_format($subtotal_produ, 2, '.', ''),
                                        "IsRetention" => "false"
                                    );

                                    $total_iva_produ = number_format($total_iva, 2, '.', '');
                                    $base_iva = $subtotal_produ;
                                    $total_produ = $total_iva_produ + $base_iva;
                           
                                }else{
                                    $array_imp = [];
                                    $objeto_impuesto = "01";
                                }

                                $products[] = array(
                                    "Quantity" => $row['cantidad'],
                                    "ProductCode" => "$clabe_sat",
                                    "UnitCode" => $unidad_medida_cod,
                                    "Unit" => $unidad_medida_nom,
                                    "Description" => $row['nombre'],
                                    "IdentificationNumber" => "23",
                                    "UnitPrice" => number_format($precio_unitario_sin_iva, 2, '.', ''),
                                    "Subtotal" => number_format($subtotal_produ, 2, '.', ''),
                                    "Discount" => "0",
                                    "DiscountVal" => "0",
                                    // "ObjetoImp" => $objeto_impuesto,            
                                    "Taxes" => array(
                                        $array_imp
                                    ),
                                    "Total" => number_format($total_produ, 2, '.', '')
                                );
    
                            }
                            // dd(orden_a_facturar($orden, $mes));
                        }
                    }else{
                    }
                }
                
            }
            
            // $client = new Client(['base_uri' => 'https://apisandbox.facturama.mx/']);
            $client = new Client(['base_uri' => 'https://api.facturama.mx/']);
            // dd($products);

            // $fecha = date("Y-m-d");
            $autenticacion = base64_encode("HIMART:Himart2022");
            $autenticacion = "Basic " . $autenticacion;
    
            $response = $client->request('POST', '/2/cfdis', [
                // 'auth' => ['DEVHIMART', 'Torre123'], 
                'auth' => ['HIMART', 'Himart2022'],
                'form_params' => [
                    "Receiver" => [
                        // "Rfc" => "URE180429TM6",
                        "Name" => "PUBLICO EN GENERAL",
                        "CfdiUse" => "G03",
                        "Rfc" => "XAXX010101000"
                        // "FiscalRegime" => "601",
                        // "TaxZipCode" => "65000"
                    ],
                    "CfdiType" => "I",
                    "NameId" => "1",
                    "ExpeditionPlace" => "06500",
                    // "ExpeditionPlace" => "45200",
                    "PaymentForm" => "03",
                    "PaymentMethod" => "PUE",
                    "Decimals" => "2",
                    "Currency" => "MXN",
                    "Date" => $fecha_factura,
                    "Items" => $products
                ]
            ]);

            if($response){
                foreach($array_ordenes as $row){
                    Ordenes_facturadas::create([
                        'id_orden' => $row
                    ]);
                }

                return redirect()->back();
            }else{
            }
    
            // echo $response->getBody();
            // dd($sumaTotalPiezas);
        }


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
        $regimen = [
            '*Personas',
            'Asalariados',
            'Arrendadores',
            'Empresarios y profesionistas',
            'Incorporación Fiscal',
            'Sector primario',
            'Otros ingresos',
            '*Empresas',
            'General de ley',
            'Cooperativas',
            'Sin fines de lucro',
            'Grupos',
            'Coordinados'
        ];

        $uso_cfdi = [
            'P01' => 'P01 Por definir',
            'D01' => 'D01 Honorarios médicos, dentales y gastos hospitalarios',
            'D02' => 'D02 Gastos médicos por incapacidad o discapacidad',
            'D03' => 'D03 Gastos funerales',
            'D04' => 'D04 Donativos',
            'D05' => 'D05 Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)',
            'D06' => 'D06 Aportaciones voluntarias al SAR',
            'D07' => 'D07 Primas por seguros de gastos médicos',
            'D08' => 'D08 Gastos de transportación escolar obligatoria',
            'D09' => 'D09 Depósitos en cuantas para el ahorro, primas que tengan como base planes de pensiones',
            'D10' => 'D10 Pagos por servicios educativos (colegiaturas)',
            'G01' => 'G01 Adquisición de mercancias',
            'G02' => 'G02 Devoluciones, descuentos o bonificaciones',
            'G03' => 'G03 Gastos en general',
            'I01' => 'I01 Construcciones',
            'I02' => 'I02 Mobiliario y equipo de oficina por inversiones',
            'I03' => 'I03 Equipo de transporte',
            'I04' => 'I04 Equipo de computo y accesorios'
        ];

        $forma_pago = [
            '04' => 'Tarjeta de crédito',
            '28' => 'Tarjeta de débito',
            '03' => 'Transferencia',
            '01' => 'Efectivo (Pago en OXXO)',
            '06' => 'Transferencia electrónica'
        ];

        $metodo_pago = [
            'PPD' => 'Pago en parcialidades ó diferido',
            'PUE' => 'Pago en una sola exhibición'
        ];

        $urlOrden['resource'] = 'orders/' . $id;
        $xmlOrden = Prestashop::get($urlOrden);

        $jsonOrden = json_encode($xmlOrden);
        $arrayOrden = json_decode($jsonOrden, true);
        
        foreach($arrayOrden as $key => $value) {
            $tablaOrden = $value;
        }

        $orden = $tablaOrden;
        $fecha_mes_orden = date("m", strtotime($orden['date_add']));

        return view('admin.facturas.edit', compact('orden', 'regimen', 'uso_cfdi', 'forma_pago', 'metodo_pago', 'fecha_mes_orden'));
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
