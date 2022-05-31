<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\RegimenFiscal;
use Prestashop;
use DB;
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
        $fecha_factura = request('fecha_factura');
        $taxesIva = [];
        $taxesIeps = [];

        if(request('orden')) { //condicional para obtener los id de las ordenes

            $ordenes = request('orden');    //asignar variable al arreglo de ordenes seleccionados

            foreach($ordenes as $idOrden) { //recorremos los id de las ordenes para realizar la consulta a la APi de prestashop

                $urlOrdenes['resource'] = 'orders/'. $idOrden; //conusltamos la API con el id de la orden si tener que desplegar un (display=full) así reducimos el tiempo de carga
                $xmlOrden = Prestashop::get($urlOrdenes);

                $jsonOrdenes = json_encode($xmlOrden);
                $arrayOrdenes[] = json_decode($jsonOrdenes, true); //guardamos las consultas en un nuevo arreglo
            
                foreach($arrayOrdenes as $key => $value) {
                        
                    if($value['order']['id'] == $idOrden) {

                        $TaxObject = "01";

                        if($value['order']['total_wrapping_tax_excl'] != "0.000000") {
                            
                            $TaxObject = "02";
                            $seguroIva[] =["iva"        => floatval($value['order']['total_wrapping_tax_excl'] * 0.16),
                                            "impuesto"  => "iva",
                                           "subtotal"   => floatval($value['order']['total_wrapping_tax_excl']),
                                           "referencia" => $value['order']['reference']
                                          ];
                        }

                        if($value['order']['total_shipping_tax_excl'] != "0.000000") {

                            $TaxObject = "02";
                            $envioIva[] = ["iva"        => floatval($value['order']['total_shipping_tax_excl'] * 0.16),
                                            "impuesto"  => "iva",
                                           "subtotal"   => floatval($value['order']['total_shipping_tax_excl']),
                                           "referencia" => $value['order']['reference']
                                          ];
                        }

                        if($value['order']['total_discounts_tax_excl'] == "0.000000" && $value['order']['total_shipping_tax_excl'] == "0.000000" && $value['order']['total_wrapping_tax_excl'] == "0.000000") {

                            $TaxObject = "01";
                        }
                        
                        if($value['order']['total_discounts_tax_incl'] == "0.000000") {

                            $descuento = $value['order']['total_discounts_tax_incl'] - $value['order']['total_discounts_tax_excl'];

                            $item[] = ["ProductCode"   => "01010101",
                                       "Description"   => $value['order']['reference'],
                                       "UnitCode"      => "ACT",
                                       "Quantity"      => 1.0,
                                       "UnitPrice"     => floatval($value['order']['total_paid_tax_excl']),
                                       "Subtotal"      => floatval($value['order']['total_paid_tax_excl']),
                                       "Discount"      => round($descuento, 2), 
                                       "TaxObject"     => $TaxObject,
                                       "Taxes"         => [],
                                       "Total"         => floatval($value['order']['total_paid'])
                                      ];
                        }else {

                            $item[] = ["ProductCode"   => "01010101",
                                    "Description"   => $value['order']['reference'],
                                    "UnitCode"      => "ACT",
                                    "Quantity"      => 1.0,
                                    "UnitPrice"     => floatval($value['order']['total_paid_tax_excl']),
                                    "Subtotal"      => floatval($value['order']['total_paid_tax_excl']),
                                    "TaxObject"     => $TaxObject,
                                    "Taxes"         => [],
                                    "Total"         => floatval($value['order']['total_paid'])
                                    ];
                        }

                        $associations[$value['order']['reference']] = $value['order']['associations']['order_rows']['order_row'];

                        
                        
                    }
                }
            }
            
            foreach($arrayOrdenes as $i => $v) {

                foreach($associations as $dev => $row) {
                    
                    if($v['order']['reference'] == $dev) {

                        if(in_array(0, $associations[$dev])) {

                            if($associations[$dev]['unit_price_tax_incl'] !== $associations[$dev]['unit_price_tax_excl']) { 

                                $comparativo = number_format($associations[$dev]['unit_price_tax_excl'], 0);
                                $calcular = $associations[$dev]['unit_price_tax_incl'] / 116;
                                $verificar = $calcular * 100;
                                $iva = number_format($verificar, 0);

                                if($iva == $comparativo) { 

                                    $iva = floatval($associations[$dev]['unit_price_tax_excl'] * 0.16);
                                    $iva = $associations[$dev]['product_quantity'] * $iva;
                                    $productPres = floatval($associations[$dev]['unit_price_tax_excl'] * $associations[$dev]['product_quantity']);

                                    $tablaImpuestos[] = ['impuesto'     => 'iva',
                                                         'iva'          => $iva, 
                                                         'referencia'   => $dev,
                                                         'sinImpuesto'  => $productPres
                                                        ];
                                }else { 

                                    $ieps = floatval($associations[$dev]['unit_price_tax_excl'] * 0.08);
                                    $ieps = $associations[$dev]['product_quantity'] * $ieps;
                                    $productPres = floatval($associations[$dev]['unit_price_tax_excl'] * $associations[$dev]['product_quantity']);

                                    $tablaImpuestos[] = ['impuesto'     => 'ieps', 
                                                         'ieps'         => $ieps, 
                                                         'referencia'   => $dev,
                                                         'sinImpuesto'  => $productPres,
                                                        ];
                                }
                            }/*else { 

                                $sinImpuesto = $associations[$dev]['product_quantity'] * $associations[$dev]['unit_price_tax_excl'];
                                $tablaImpuestos[] = ['impuesto'     => 'sin impuesto', 
                                                     'referencia'   => $dev,
                                                     'sinImpuesto'  => $sinImpuesto,     
                                                    ];
                            }*/
                            
                        }else {

                            foreach($associations[$dev] as $fillas) { 

                                if($fillas['unit_price_tax_incl'] !== $fillas['unit_price_tax_excl']) { 
                                    
                                    $comparativo = number_format($fillas['unit_price_tax_excl'], 0);
                                    $calcular = $fillas['unit_price_tax_incl'] / 116;
                                    $verificar = $calcular * 100;
                                    $iva = number_format($verificar, 0);

                                    if($iva == $comparativo) { 

                                        $iva = floatval($fillas['unit_price_tax_excl'] * 0.16);
                                        $iva = $fillas['product_quantity'] * $iva;
                                        $productPres = floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);

                                        $tablaImpuestos[] = ['impuesto'     => 'iva',
                                                             //'valor'        => 0.16,
                                                             'iva'          => $iva, 
                                                             'referencia'   => $dev,
                                                             'sinImpuesto'  => $productPres
                                                            ];
                                    }else { 

                                        $ieps = floatval($fillas['unit_price_tax_excl'] * 0.08);
                                        $ieps = $fillas['product_quantity'] * $ieps;
                                        $productPres = floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);

                                        $tablaImpuestos[] = ['impuesto'     => 'ieps', 
                                                             //'valor'        => 0.08,
                                                             'ieps'         => $ieps, 
                                                             'referencia'   => $dev,
                                                             'sinImpuesto'  => $productPres,
                                                            ];
                                    }
                                }/*else { 

                                    $sinImpuesto = $fillas['product_quantity'] * $fillas['unit_price_tax_excl'];
                                    $tablaImpuestos[] = ['impuesto'     => 'sin impuesto', 
                                                        'referencia'   => $dev,
                                                        'sinImpuesto'  => $sinImpuesto,     
                                                        ];
                                }*/
                            }
                        }
                    }
                }
            }
            
            if(isset($seguroIva) && isset($envioIva)) {

                $temp = array_merge($envioIva, $seguroIva);

            }elseif(isset($seguroIva)) {

                $temp = $seguroIva;
            }else {

                $temp = $envioIva;
            }
            if(isset($tablaImpuestos)) { 

                foreach($arrayOrdenes as $index => $valor2) {

                    $sumaIva = 0;
                    $subIva = 0;
                    $sumaIeps = 0;
                    $subIeps = 0;
                    foreach($tablaImpuestos as $inTabla => $valTabla) {
                
                        if($valor2['order']['reference'] == $valTabla['referencia']) {

                            if($valTabla['impuesto'] == 'iva') {
                                
                                $taxesIva[$valTabla['referencia']] = ['Total'    => $sumaIva += $valTabla['iva'],
                                                                      'Name'     => "IVA", 
                                                                      'Base'     => $subIva += $valTabla['sinImpuesto'],
                                                                      'Rate'     => 0.16
                                                                     ]; 
                            }else {
                                
                                $taxesIeps[$valTabla['referencia']] = ['Total'    => $sumaIeps += $valTabla['ieps'],
                                                                       'Name'     => "IEPS", 
                                                                       'Base'     => $subIeps += $valTabla['sinImpuesto'],
                                                                       'Rate'     => 0.08,
                                                                      ];
                                
                            }
                        }
                    }
                }
            }
            if(isset($temp) && isset($taxesIva)) {

                foreach($temp as $inTemp => $valTemp) {

                    foreach($taxesIva as $inTaxIva => $valTaxIva) {

                        if($valTemp['referencia'] == $inTaxIva) {

                            $taxesIva[$valTemp['referencia']] = ['Total'    => $valTemp['iva'] + $valTaxIva['Total'],
                                                                 'Name'     => "IVA",
                                                                 'Base'     => $valTemp['subtotal'] + $valTaxIva['Base'],
                                                                 'Rate'     => 0.16,
                                                                ];
                        }
                    }
                }
            }
            if(isset($taxesIva) && isset($taxesIeps)) {

                foreach($item as $inItem => $valItem) {

                    foreach($taxesIva as $indexTaxIva => $valueTaxIVa) {

                        foreach($taxesIeps as $inTaxIeps => $valTaxIeps) {
                            
                            if($valItem['Description'] == $indexTaxIva && $valItem['Description'] == $inTaxIeps) {
                                
                                $item[$inItem]['Description'] = "Venta - ".$indexTaxIva;
                                $item[$inItem]['TaxObject'] = "02";
                                $item[$inItem]['Taxes'] = [["Total"         => round($valueTaxIVa['Total'], 2),
                                                            "Name"          => $valueTaxIVa['Name'],
                                                            "Base"          => round($valueTaxIVa['Base'], 2),
                                                            "Rate"          => $valueTaxIVa['Rate'],
                                                            "IsRetention"   => "false"
                                                           ],
                                                           ["Total"         => round($valTaxIeps['Total'], 2),
                                                            "Name"          => $valTaxIeps['Name'],
                                                            "Base"          => round($valTaxIeps['Base'], 2),
                                                            "Rate"          => $valTaxIeps['Rate'],
                                                            "IsRetention"   => "false"
                                                           ]
                                                          ];

                                if(isset($item[$inItem]['Discount'])) {

                                    if($item[$inItem]['Discount'] == 0.0) {

                                        unset($item[$inItem]['Discount']);
                                        

                                    }else {

                                        $resta = round($item[$inItem]['Subtotal'] + $valueTaxIVa['Total'] + $valTaxIeps['Total'], 2);
                                        $descontar = round($item[$inItem]['Discount'], 2);
                                        $item[$inItem]['Total'] = round($resta - $descontar, 2);
                                    }

                                }else {
                                    
                                    $item[$inItem]['Total'] = round($item[$inItem]['Subtotal'] + $valueTaxIVa['Total'] + $valTaxIeps['Total'], 2);
                                }
                                
                            }
                        }
                    }
                }
            }
            if(isset($taxesIva)) {

                foreach($item as $index2 => $value3) {

                    foreach($taxesIva as $inTaxesIva2 => $valTaxIva2) {

                        if($value3['Description'] == $inTaxesIva2) {

                            $totalIva = $valTaxIva2['Base'] * 0.16;

                            $item[$index2]['Description'] = "Venta - ".$inTaxesIva2;
                            $item[$index2]['TaxObject'] = "02";
                            $item[$index2]['Taxes'] = [['Total'         => round($totalIva, 2),
                                                        'Name'          => $valTaxIva2['Name'],
                                                        'Base'          => round($valTaxIva2['Base'], 2),
                                                        'Rate'          => $valTaxIva2['Rate'],
                                                        "IsRetention"   => "false"
                                                      ]];

                            if(isset($item[$index2]['Discount'])) {

                                if($item[$index2]['Discount'] == 0.0) {

                                    unset($item[$index2]['Discount']);

                                }else {

                                    $resta1 = floatval($item[$index2]['Subtotal'] + $totalIva);
                                    $descontar1 = floatval($item[$index2]['Discount']);
                                    $item[$index2]['Total'] = round($resta1 - $descontar1, 2);
                                }

                            }else {

                                $item[$index2]['Total'] = round($item[$index2]['Subtotal'] + $totalIva, 2);
                            }
                            
                        }
                    }
                }
            }
            if(isset($taxesIeps)) {

                foreach($item as $index3 => $value4) {

                    foreach($taxesIeps as $inTaxIeps2 => $valueTaxIeps) {

                        if($value4['Description'] == $inTaxIeps2) {

                            $totalIeps = $valueTaxIeps['Base'] * 0.08;
                            
                            $item[$index3]['Description'] = "Venta - ".$inTaxIeps2;
                            $item[$index3]['TaxObject'] = "02";
                            $item[$index3]['Taxes'] = [['Total'         => round($totalIeps, 2),
                                                        'Name'          => $valueTaxIeps['Name'],
                                                        'Base'          => round($valueTaxIeps['Base'], 2),
                                                        'Rate'          => $valueTaxIeps['Rate'],
                                                        "IsRetention"   => "false"
                                                      ]];
                            
                            if(isset($item[$index3]['Discount'])) {

                                if($item[$index3]['Discount'] == 0.0) {

                                    unset($item[$index3]['Discount']);

                                }else {

                                    $resta2 = floatval($item[$index3]['Subtotal'] + $valueTaxIeps['Total']);
                                    $descontar2 = floatval($item[$index3]['Discount']);
                                    $item[$index3]['Total'] = round($resta2 - $descontar2, 2);
                                }
                            }else {

                                $item[$index3]['Total'] = round($item[$index3]['Subtotal'] + $valueTaxIeps['Total'], 2);
                            }
                            
                        }
                    }
                }

            }
            //if(!isset($temp)) {

                foreach ($item as $iItem => $vItem) {
                    
                    $sumaIva2 = 0;
                    $subIva2 = 0;
                    foreach($temp as $iTemp => $vtemp) {

                        if($vItem['Description'] == $vtemp['referencia']) {

                            $item[$iItem]['Description'] = "Venta - ".$vtemp['referencia'];
                            $item[$iItem]['TaxObject'] = "02";
                            $item[$iItem]['Taxes'] = [['Total'          => round($sumaIva2 += $vtemp['iva'], 2),
                                                       'Name'           => "IVA",
                                                       'Base'           => round($subIva2 += $vtemp['subtotal'], 2),
                                                       'Rate'           => 0.16,
                                                       "IsRetention"    => "false"
                                                     ]];
                                                     
                            if(isset($item[$iItem]['Discount'])) {

                                if($item[$iItem]['Discount'] == 0.0) {

                                    unset($item[$iItem]['Discount']);
                                }else {
                                    
                                    $resta3 = round($item[$iItem]['Subtotal'] + $sumaIva2, 2);
                                    $descontar3 = round($item[$iItem]['Discount'], 2);
                                    $item[$iItem]['Total'] = round($resta3 - $descontar3, 2);
                                }

                            }else {
                                
                                $item[$iItem]['Total'] = round($item[$iItem]['Subtotal'] + $sumaIva2, 2);
                            }
                            
                        }
                    }
                }
            //}
        }  
        $cfdi = [
            'auth' => ['HIMART', 'Himart2022'],
            'form_params' => [ 
                "CfdiType"          => "I",
                "PaymentForm"       => "03",
                "PaymentMethod"     => "PUE",
                "ExpeditionPlace"   => "06500",
                "Receiver" => ["Rfc"            => "XAXX010101000",
                               "CfdiUse"        => "S01", //este campo puede variar cuando sea factura solicitada
                               "Name"           => "PUBLICO GENERAL",
                               "FiscalRegime"   => "616",
                               "TaxZipCode"     => "06500"
                              ],
                "Items" => $item
            ]
        ];
        //dd($cfdi);
        //dd($cfdi); https://api.facturama.mx/ https://apisandbox.facturama.mx/
        //$urlApi = new Client(['base_uri' => 'https://api.facturama.mx/']);
        $urlApi = new Client(['base_uri' => 'https://apisandbox.facturama.mx/']);

        $posApi = $urlApi->POST('3/cfdis', $cfdi);

        /*foreach($ordenes as $facturada) {

            Ordenes_facturadas::create(['id_orden' => $facturada]);
        } */

        return redirect('admin/facturas');    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
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
        $parametros = [];
        $tablaRegime = [];
        $uso_cfdi = [];
        $forma_pago = [];
        $metodo_pago = [];

        $urlOrden['resource'] = 'orders/' . $id;
        $xmlOrden = Prestashop::get($urlOrden);

        $jsonOrden = json_encode($xmlOrden);
        $arrayOrden = json_decode($jsonOrden, true);

        $cfdi = DB::connection('mysql')->select("SELECT cfdi, descripcion FROM uso_cfdi");
        $regimen = DB::connection('mysql')->select("SELECT regimenFiscal, descripcion FROM regimen_fiscal");
        $formaPago = DB::connection('mysql')->select("SELECT formaPago, descripcion FROM forma_pago");
        $metodoPago = DB::connection('mysql')->select("SELECT metodoPago, descripcion FROm metodo_pago");
        
        foreach($arrayOrden as $key => $valor) {

            $tablaOrden = ["id"         => $valor['id'],
                           "reference"  => $valor['reference']
                          ];
            
            $totalPagado = $valor['total_paid'];
            
            if($valor['total_wrapping_tax_excl'] != "0.000000") {
                
                $subTotal = round($valor['total_wrapping_tax_excl'] * 0.16, 2);
                
                $item[] =["ProductCode" => "78102200",
                          "Description" => "Seguro de Viaje",
                          "UnitCode"    => "SX - Envio",
                          "Quantity"    => 1.0,
                          "UnitPrice"   => floatval($valor['total_wrapping_tax_excl']),
                          "Subtotal"    => floatval($valor['total_wrapping_tax_excl']),
                          "Total"        => $subTotal,
                          "Name"         => "IVA",
                          "Base"         => round($valor['total_wrapping_tax_excl'], 2),
                          "Rate"         => 0.16,
                          "IsRetention"  => "false",
                          "Total"       => round($valor['total_wrapping_tax_excl'] + $subTotal, 2),
                          
                         ];
            }

            if($valor['total_shipping_tax_excl'] != "0.000000") {
                
                $subTotal2 = round($valor['total_shipping_tax_excl'] * 0.16, 2);

                if($valor['total_shipping_tax_incl'] === $valor['total_discounts_tax_incl']) {

                    $sinenvio = "";
                }else {

                    $item[] = ["ProductCode" => "78102200",
                               "Description" => "Envio",
                               "UnitCode"    => "SX - Envio",
                               "Quantity"    => 1.0,
                               "UnitPrice"   => floatval($valor['total_shipping_tax_excl']),
                               "Subtotal"    => floatval($valor['total_shipping_tax_excl']),
                               "Total"        => $subTotal2,
                               "Name"         => "IVA",
                               "Base"         => round($valor['total_shipping_tax_excl'], 2),
                               "Rate"         => 0.16,
                               "IsRetention"  => "false",
                               "Total"        => round($valor['total_shipping_tax_excl'] + $subTotal2, 2)
                              ];
                }
            }

            $associations[$valor['id']] = $valor['associations']['order_rows']['order_row'];

        }
        
        foreach($associations as $key2 =>$row) {
            
            if(in_array(0, $associations[$key2])) {

                $clavesSat[] = Product::select("id_product", "clabe_sat", "unidad_medida")->where('id_product', $row['product_id'])->get()->first()->toArray();

                foreach($clavesSat as $index2 => $valor2) {

                    if($valor2['id_product'] == $row['product_id']) {
                        
                        if($associations[$key2]['unit_price_tax_incl'] !== $associations[$key2]['unit_price_tax_excl']) {

                            $comparativo = number_format($associations[$key2]['unit_price_tax_excl'], 0);
                            $calcular = $associations[$key2]['unit_price_tax_incl'] / 1.16;
                            $iva = number_format($calcular, 0);

                            if($iva == $comparativo) {
                                
                                $subTotalIva = round($row['unit_price_tax_excl'] * 0.16, 2);
                                $totalIVa = $subTotalIva * $row['product_quantity'];

                                $sub = round($row['unit_price_tax_excl'] * $row['product_quantity']);

                                $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                           "Description"    => $row['product_name'],
                                           "UnitCode"       => $valor2['unidad_medida'],
                                           "Quantity"       => floatval($row['product_quantity']),
                                           "UnitPrice"      => round($row['unit_price_tax_excl'], 2),
                                           "Subtotal"       => $sub,
                                           "Total"        => $totalIVa,
                                           "Name"         => "IVA",
                                           "Base"         => $sub,
                                           "Rate"         => 0.16,
                                           "IsRetention"  => "false",
                                           "Total"          => floatval($totalIVa + $sub)
                                          ];
                            }else {

                                $subTotalIeps = round($row['unit_price_tax_excl'] * 0.08, 2);
                                $totalIeps = $subTotalIeps * $row['product_quantity'];
                                $subIeps = round($row['unit_price_tax_excl'] * $row['product_quantity']);

                                $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                           "Description"    => $row['product_name'],
                                           "UnitCode"       => $valor2['unidad_medida'],
                                           "Quantity"       => floatval($row['product_quantity']),
                                           "UnitPrice"      => round($row['unit_price_tax_excl'], 2),
                                           "Subtotal"       => $subIeps,
                                           "Total"        => $totalIeps,
                                           "Name"         => "IEPS",
                                           "Base"         => $subIeps,
                                           "Rate"         => 0.08,
                                           "Total"         => floatval($totalIeps + $subIeps)
                                          ];
                            }
    
                        }else {
    
                            $item[] = ["ProductCode"   => $valor2['clabe_sat'],
                                       "Description"   => $row['product_name'],
                                       "UnitCode"      => $valor2['unidad_medida'],
                                       "Quantity"      => floatval($row['product_quantity']),
                                       "UnitPrice"     => floatval($row['unit_price_tax_excl']),
                                       "Subtotal"      => floatval($row['unit_price_tax_excl'] * $row['product_quantity']),
                                       "Total"         => floatval($row['unit_price_tax_excl'] * $row['product_quantity'])
                                      ];
                        }
                    }
                }
                
            }else {
                
                foreach($associations[$key2] as $fillas) {

                    $clavesSat[] = Product::select("id_product", "clabe_sat", "unidad_medida")->where('id_product', $fillas['product_id'])->get()->first()->toArray();
                    foreach($clavesSat as $index2 => $valor2) {

                        if($valor2['id_product'] == $fillas['product_id']) {
                            
                            if($fillas['unit_price_tax_incl'] !== $fillas['unit_price_tax_excl']) {
    
                                $comparativo = number_format($fillas['unit_price_tax_excl'], 0);
                                $calcular = $fillas['unit_price_tax_incl'] / 1.16;
                                $iva = number_format($calcular, 0);
    
                                if($iva == $comparativo) {
                                    
                                    $subTotalIva = round($fillas['unit_price_tax_excl'] * 0.16, 2);
                                    $totalIVa = $subTotalIva * $fillas['product_quantity'];
    
                                    $sub = round($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);
    
                                    $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                               "Description"    => $fillas['product_name'],
                                               "UnitCode"       => $valor2['unidad_medida'],
                                               "Quantity"       => floatval($fillas['product_quantity']),
                                               "UnitPrice"      => round($fillas['unit_price_tax_excl'], 2),
                                               "Subtotal"       => $sub,
                                               "Total"        => $totalIVa,
                                               "Name"         => "IVA",
                                               "Base"         => $sub,
                                               "Rate"         => 0.16,
                                               "Total"          => floatval($totalIVa + $sub)
                                              ];
                                }else {
    
                                    $subTotalIeps = round($fillas['unit_price_tax_excl'] * 0.08, 2);
                                    $totalIeps = $subTotalIeps * $fillas['product_quantity'];
                                    $subIeps = round($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);
    
                                    $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                               "Description"    => $fillas['product_name'],
                                               "UnitCode"       => $valor2['unidad_medida'],
                                               "Quantity"       => floatval($fillas['product_quantity']),
                                               "UnitPrice"      => round($fillas['unit_price_tax_excl'], 2),
                                               "Subtotal"       => $subIeps,
                                               "Total"        => $totalIeps,
                                               "Name"         => "IEPS",
                                               "Base"         => $subIeps,
                                               "Rate"         => 0.08,
                                               "Total"         => floatval($totalIeps + $subIeps)
                                              ];
                                }
        
                            }else {
        
                                $item[] = ["ProductCode"   => $valor2['clabe_sat'],
                                           "Description"   => $fillas['product_name'],
                                           "UnitCode"      => $valor2['unidad_medida'],
                                           "Quantity"      => floatval($fillas['product_quantity']),
                                           "UnitPrice"     => floatval($fillas['unit_price_tax_excl']),
                                           "Subtotal"      => floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity']),
                                           "Total"         => floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity'])
                                          ];
                            }
                        }
                    }
                }
            }

        }
        //$clavesSat[] = Product::select("id_product", "clabe_sat", "unidad_medida")->where('id_product', $id)->get();

        foreach($regimen as $index => $value) {

            $tablaRegime[$value->regimenFiscal] = $value->descripcion;
        }
        
        foreach($cfdi as $index2 => $value2) {

            $uso_cfdi[$value2->cfdi] = $value2->descripcion;
        }
        foreach($formaPago as $index3 => $value3) {

            $forma_pago[$value3->formaPago] =  $value3->descripcion;
        }
        foreach($metodoPago as $index4 => $value4) {
            
            $metodo_pago[$value4->metodoPago] = $value4->descripcion;
        }

        $parametros = ['orden'      => $tablaOrden,
                       'regimen'    => $tablaRegime,
                       'cfdi'       => $uso_cfdi,
                       'formaPago'  => $forma_pago,
                       'metodo_pago'=> $metodo_pago,
                       'articulos'  => $item,
                       'total'      => $totalPagado,
                    ];

        //dd(number_format($totalPagado, 2));
        return view('admin.facturas.edit', compact('parametros'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = request('pedido');
        $rfc = request('rfc');
        $razonS = request('razon');
        $factura = request('factura');
        $paymentForm = request('forma_pago');
        $metodo = request('metodo_pago');
        $cp = request('cp');
        $regimen = request('regimen');

        $urlOrden['resource'] = 'orders/' . $id;
        $xmlOrden = Prestashop::get($urlOrden);

        $jsonOrden = json_encode($xmlOrden);
        $arrayOrden = json_decode($jsonOrden, true);
        foreach($arrayOrden as $key => $valor) {

            $tablaOrden = ["id"         => $valor['id'],
                           "reference"  => $valor['reference']
                          ];
        if($valor['total_discounts_tax_excl'] != "0.000000") {

            $descuentos = floatval($valor['total_discounts_tax_incl'] - $valor['total_discounts_tax_excl']);
        }
            if($valor['total_wrapping_tax_excl'] != "0.000000") {
                
                $subTotal = round($valor['total_wrapping_tax_excl'] * 0.16, 2);
                
                $item[] =["ProductCode" => "78102200",
                          "Description" => "Seguro de Viaje",
                          "UnitCode"    => "SX",
                          "Quantity"    => 1.0,
                          "UnitPrice"   => floatval($valor['total_wrapping_tax_excl']),
                          "Subtotal"    => floatval($valor['total_wrapping_tax_excl']),
                          "TaxObject"   => "02",
                          "Taxes"       => [["Total"        => $subTotal,
                                             "Name"         => "IVA",
                                             "Base"         => round($valor['total_wrapping_tax_excl'], 2),
                                             "Rate"         => 0.16,
                                             "IsRetention"  => "false"
                                            ]],
                          "Total"       => round($valor['total_wrapping_tax_excl'] + $subTotal, 2),
                          
                         ];
            }

            if($valor['total_shipping_tax_excl'] != "0.000000") {
                
                $subTotal2 = round($valor['total_shipping_tax_excl'] * 0.16, 2);

                if($valor['total_shipping_tax_incl'] === $valor['total_discounts_tax_incl']) {

                    $sinenvio = "";
                }else {

                    $item[] = ["ProductCode" => "78102200",
                               "Description" => "Envio",
                               "UnitCode"    => "SX",
                               "Quantity"    => 1.0,
                               "UnitPrice"   => floatval($valor['total_shipping_tax_excl']),
                               "Subtotal"    => floatval($valor['total_shipping_tax_excl']),
                               "TaxObject"   => "02",
                               "Taxes"       => [["Total"        => $subTotal2,
                                                    "Name"         => "IVA",
                                                    "Base"         => round($valor['total_shipping_tax_excl'], 2),
                                                    "Rate"         => 0.16,
                                                    "IsRetention"  => "false"
                                                    ]],
                               "Total"        => round($valor['total_shipping_tax_excl'] + $subTotal2, 2)
                              ];
                }
            }

            $associations[$valor['id']] = $valor['associations']['order_rows']['order_row'];

        }
        
        foreach($associations as $key2 =>$row) {
            
            if(in_array(0, $associations[$key2])) {

                $clavesSat[] = Product::select("id_product", "clabe_sat", "unidad_medida")->where('id_product', $row['product_id'])->get()->first()->toArray();

                foreach($clavesSat as $index2 => $valor2) {

                    if($valor2['id_product'] == $row['product_id']) {
                        
                        if($associations[$key2]['unit_price_tax_incl'] !== $associations[$key2]['unit_price_tax_excl']) {

                            $comparativo = number_format($associations[$key2]['unit_price_tax_excl'], 0);
                            $calcular = $associations[$key2]['unit_price_tax_incl'] / 1.16;
                            $iva = number_format($calcular, 0);

                            if($iva == $comparativo) {
                                
                                $subTotalIva = round($row['unit_price_tax_excl'] * 0.16, 2);
                                $totalIVa = $subTotalIva * $row['product_quantity'];

                                $sub = round($row['unit_price_tax_excl'] * $row['product_quantity']);

                                $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                           "Description"    => $row['product_name'],
                                           "UnitCode"       => $valor2['unidad_medida'],
                                           "Quantity"       => floatval($row['product_quantity']),
                                           "UnitPrice"      => round($row['unit_price_tax_excl'], 2),
                                           "Subtotal"       => $sub,
                                           "TaxObject"      => "02",
                                           "Taxes"          => [["Total"        => $totalIVa,
                                                                 "Name"         => "IVA",
                                                                 "Base"         => $sub,
                                                                 "Rate"         => 0.16,
                                                                 "IsRetention"  => "false"
                                                               ]],
                                           "Total"          => floatval($totalIVa + $sub)
                                          ];
                            }else {

                                $subTotalIeps = round($row['unit_price_tax_excl'] * 0.08, 2);
                                $totalIeps = $subTotalIeps * $row['product_quantity'];
                                $subIeps = round($row['unit_price_tax_excl'] * $row['product_quantity']);

                                $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                           "Description"    => $row['product_name'],
                                           "UnitCode"       => $valor2['unidad_medida'],
                                           "Quantity"       => floatval($row['product_quantity']),
                                           "UnitPrice"      => round($row['unit_price_tax_excl'], 2),
                                           "Subtotal"       => $subIeps,
                                           "TaxObject"      => "02",
                                           "Taxes"          => [["Total"        => $totalIeps,
                                                                 "Name"         => "IEPS",
                                                                 "Base"         => $subIeps,
                                                                 "Rate"         => 0.08,
                                                                 "IsRetention"  => "false"
                                                               ]],
                                            "Total"         => floatval($totalIeps + $subIeps)
                                          ];
                            }
    
                        }else {
    
                            $item[] = ["ProductCode"   => $valor2['clabe_sat'],
                                       "Description"   => $row['product_name'],
                                       "UnitCode"      => $valor2['unidad_medida'],
                                       "Quantity"      => floatval($row['product_quantity']),
                                       "UnitPrice"     => floatval($row['unit_price_tax_excl']),
                                       "Subtotal"      => floatval($row['unit_price_tax_excl'] * $row['product_quantity']),
                                       "TaxObject"     => "01",
                                       "Taxes"         => [],
                                       "Total"         => floatval($row['unit_price_tax_excl'] * $row['product_quantity'])
                                      ];
                        }
                    }
                }
                
            }else {
                
                foreach($associations[$key2] as $fillas) {

                    $clavesSat[] = Product::select("id_product", "clabe_sat", "unidad_medida")->where('id_product', $fillas['product_id'])->get()->first()->toArray();
                    foreach($clavesSat as $index2 => $valor2) {

                        if($valor2['id_product'] == $fillas['product_id']) {
                            
                            if($fillas['unit_price_tax_incl'] !== $fillas['unit_price_tax_excl']) {
    
                                $comparativo = number_format($fillas['unit_price_tax_excl'], 0);
                                $calcular = $fillas['unit_price_tax_incl'] / 1.16;
                                $iva = number_format($calcular, 0);
    
                                if($iva == $comparativo) {
                                    
                                    $subTotalIva = floatval($fillas['unit_price_tax_excl'] * 0.16);
                                    $totalIVa = $subTotalIva * $fillas['product_quantity'];
    
                                    $sub = floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);
    
                                    $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                               "Description"    => $fillas['product_name'],
                                               "UnitCode"       => $valor2['unidad_medida'],
                                               "Quantity"       => round($fillas['product_quantity'], 2),
                                               "UnitPrice"      => round($fillas['unit_price_tax_excl'], 2),
                                               "Subtotal"       => round($sub, 2),
                                               "TaxObject"      => "02",
                                               "Taxes"          => [["Total"        => round($totalIVa, 2),
                                                                     "Name"         => "IVA",
                                                                     "Base"         => round($sub, 2),
                                                                     "Rate"         => 0.16,
                                                                     "IsRetention"  => "false"
                                                                   ]],
                                               "Total"          => round($totalIVa + $sub, 2)
                                              ];
                                }else {
    
                                    $subTotalIeps = round($fillas['unit_price_tax_excl'] * 0.08, 2);
                                    $totalIeps = $subTotalIeps * $fillas['product_quantity'];
                                    $subIeps = round($fillas['unit_price_tax_excl'] * $fillas['product_quantity']);
    
                                    $item[] = ["ProductCode"    => $valor2['clabe_sat'],
                                               "Description"    => $fillas['product_name'],
                                               "UnitCode"       => $valor2['unidad_medida'],
                                               "Quantity"       => floatval($fillas['product_quantity']),
                                               "UnitPrice"      => round($fillas['unit_price_tax_excl'], 2),
                                               "Subtotal"       => $subIeps,
                                               "TaxObject"      => "02",
                                               "Taxes"          => [["Total"        => $totalIeps,
                                                                     "Name"         => "IEPS",
                                                                     "Base"         => $subIeps,
                                                                     "Rate"         => 0.08,
                                                                     "IsRetention"  => "false"
                                                                   ]],
                                                "Total"         => floatval($totalIeps + $subIeps)
                                              ];
                                }
        
                            }else {
        
                                $item[] = ["ProductCode"   => $valor2['clabe_sat'],
                                           "Description"   => $fillas['product_name'],
                                           "UnitCode"      => $valor2['unidad_medida'],
                                           "Quantity"      => floatval($fillas['product_quantity']),
                                           "UnitPrice"     => floatval($fillas['unit_price_tax_excl']),
                                           "Subtotal"      => floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity']),
                                           "TaxObject"     => "01",
                                           "Taxes"         => [],
                                           "Total"         => floatval($fillas['unit_price_tax_excl'] * $fillas['product_quantity'])
                                          ];
                            }
                        }
                    }
                }
            }

        }
        $cfdi = [
            'auth' => ['HIMART', 'Himart2022'],
            'form_params' => [ 
                "CfdiType"          => "I",
                "PaymentForm"       => $paymentForm,
                "PaymentMethod"     => $metodo,
                "ExpeditionPlace"   => "44940", //cambiar a codigo porstal de JERA
                "Receiver" => ["Rfc"            => $rfc,
                               "CfdiUse"        => $factura, //este campo puede variar cuando sea factura solicitada
                               "Name"           => $razonS,
                               "FiscalRegime"   => $regimen, //cambiar por variable
                               "TaxZipCode"     => $cp //cambiar por variable de código postal
                              ],
                "Items" => $item
            ]
        ];
        dd($arrayOrden);
        //dd($cfdi); https://api.facturama.mx/  https://apisandbox.facturama.mx/
        $urlApi = new Client(['base_uri' => 'https://apisandbox.facturama.mx/']);
        /*$urlApi = new Client(['base_uri' => 'https://api.facturama.mx/']);*/
        
        $posApi = $urlApi->POST('3/cfdis', $cfdi);

        //Ordenes_facturadas::create(['id_orden' => $id]);
        
        return redirect('admin/facturas');
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
