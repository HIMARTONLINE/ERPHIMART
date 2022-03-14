<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DB;
use App\User;
use App\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Prestashop;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestaShopWebserviceException;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function periodSales(Request $request)
    {
        //$response = Http::get('https://queries.envia.com/payment/01/2022');
        //$response = Http::withToken('448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349')->get('https://queries.envia.com/payment/01/2022');
        $client = new Client(['base_uri' => 'https://queries.envia.com']);

        $response = $client->request(
            'GET',
            'guide/02/2022', 
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response = json_decode($response);

        foreach($response as $row){
            $ultimo_env[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env[0]->id;
        /*
        foreach($response as $row){
            for($i=0; $i<200; $i++){
                $id_envio = $row[$i]->id;
                $id_orden = explode(" - ", $row[$i]->consignee_name);
                echo $id_orden[0] . '<br>';
                if($id_env == $id_envio){
                    break;
                }
            }
        }
        */
        // echo $response;

        // return false;

        $ventas = [];
        $parametros = [
            'ventas'  => [],
            'rango'   => '',
            'mes'     => '',
            'totales' => [
                'piezas'     => 0,
                'venta'      => 0,
                'utilidad'   => 0,
                'porcentaje' => 0
            ]
        ];

        $urlOrder['resource'] = 'orders/?sort=[id_DESC]&display=full'; //pasamos los parametros por url de la apí
        $xmlOrder = Prestashop::get($urlOrder); //llama los parametros por GET

        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $jsonOrder = json_encode($xmlOrder);    //codificamos el xml de la api en json
        $arrayOrder = json_decode($jsonOrder, true);  //decodificamos el json anterior para poder manipularlos

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos
        /*
        foreach($arrayOrder['orders']['order'] as $i => $v) {
            $tabla[] = $v;
            $fecha = date('Y-m-d', strtotime($v['date_add']));

            if( $fecha >= "2022-02-01" && $fecha <= "2022-02-28") {

                if($v['current_state'] == "3"|| $v['current_state'] == "5" || $v['current_state'] == "4" || $v['current_state'] == "2") {
                    
                    $suma[] = floatval($v['total_paid']);
                    $ejem[] = $v['associations']['order_rows']['order_row'];

                }
            }
        }

        foreach($ejem as $key => $row){
            
            if(in_array(0, $ejem[$key])){
                echo $ejem[$key]['product_id'] . $ejem[$key]['product_quantity'];
                
            }else{
                foreach($ejem[$key] as $filas){
                    echo $filas['product_id'] . '-' . $filas['product_quantity'] . '----';
                    
                }
               
            }
        }
        */

        if(isset($_REQUEST['filtro_venta'])){

            foreach($arrayOrder['orders']['order'] as $i => $v) {

                if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                    
                    // $suma[] = floatval($v['total_paid']);
                    $id_orden = $v['id'];
                    $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];

                }
            }
            
            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    if(($value['date_add'] >= $_REQUEST['de_fecha']) && ($value['date_add'] <= $_REQUEST['a_fecha'])){

                        foreach($arrayProdu['products']['product'] as $inPro => $valPro) {

                            foreach($ejem as $key => $row){
                                if($value['id'] == $key){
                                    if(in_array(0, $ejem[$key])){              

                                            if($valPro['id'] == $ejem[$key]['product_id']) {
                                                $total_piezas[] = $ejem[$key]['product_quantity'];
                                                $sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                                            }                          
                                        
                                    }else{

                                        foreach($ejem[$key] as $filas){
                                            
                                                if($valPro['id'] == $filas['product_id']) {
                                                    $total_piezas[] = $filas['product_quantity'];
                                                    $sumar[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                                                }
                                        
                                        }
                                    
                                    }
                                }
                                
                            }
                        }

                        foreach($response as $row){
                            for($i=0; $i<3000; $i++){
                                $id_envio = $row[$i]->id;
                                $id_orden = explode(" - ", $row[$i]->consignee_name);
                                if(intval($value['id']) == intval($id_orden[0])){
                                    $paqueteria = $row[$i]->total;
                                    break;
                                }else{
                                    $paqueteria = 0.00;
                                }
                                // echo $id_orden[0] . '<br>';
                                if($id_env == $id_envio){
                                    break;
                                }
                            }
                        }

                        $confirmacion = Order::where('id_order', $value['id'])->first();

                        if($confirmacion){
                            $status = $confirmacion->status;
                        }else{
                            $status = 1;
                        }            

                        $sumaCompra = array_sum($sumar);

                        $tablaProdu[] = ['fecha'         => $value['date_add'],
                                        'orden'          => $value['id'],
                                        'referencia'     => $value['reference'],
                                        'total'          => $value['total_paid'],
                                        'descuento'      => $value['total_discounts'],
                                        'envio'          => $value['total_shipping_tax_incl'],
                                        'pagado'         => $value['total_products'],
                                        'sin_iva'        => $value['total_paid_tax_excl'],
                                        'compra'         => $sumaCompra,
                                        'paqueteria'     => $paqueteria,
                                        'comision'       => $value['payment'],
                                        // 'utilidad'       => 'pendiente',
                                        'confirmacion'   => $value['current_state'],
                                        'status'         => $status,
                        ];

                        $sumar = [];
                        $total_venta[] = $value['total_paid'];
                        $sumaTotalPiezas = array_sum($total_piezas);
                        $total_sin_iva[] = $value['total_paid_tax_excl'];
                        $total_compra[] = $sumaCompra;
                        $total_envio[] = $value['total_shipping_tax_incl'];

                    }
                    
                }

            }

            $sumaTotalVenta = array_sum($total_venta);

            $ordenarTabla = $tablaProdu;

            $parametros = ['ordenes' => $ordenarTabla];
            $total_piezas = $sumaTotalPiezas;
            $total_venta = $sumaTotalVenta;
            $sumaSinIva = array_sum($total_sin_iva);
            $sumaCompra = array_sum($total_compra);
            $sumaEnvio = array_sum($total_envio);

            $total_utilidad = [
                'sumaSinIva'  => $sumaSinIva,
                'sumaCompra'  => $sumaCompra,
                'sumaEnvio'   => $sumaEnvio
            ];

            $filtro = [
                'de_fecha' => $_REQUEST['de_fecha'],
                'a_fecha' => $_REQUEST['a_fecha']            
            ];

        }else{
            
            foreach($arrayOrder['orders']['order'] as $i => $v) {

                if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                    
                    // $suma[] = floatval($v['total_paid']);
                    $id_orden = $v['id'];
                    $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];

                }
            }
            
            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    foreach($arrayProdu['products']['product'] as $inPro => $valPro) {

                        foreach($ejem as $key => $row){
                            if($value['id'] == $key){
                                if(in_array(0, $ejem[$key])){              

                                        if($valPro['id'] == $ejem[$key]['product_id']) {
                                            $total_piezas[] = $ejem[$key]['product_quantity'];
                                            $sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                                        }                          
                                    
                                }else{

                                    foreach($ejem[$key] as $filas){
                                        
                                            if($valPro['id'] == $filas['product_id']) {
                                                $total_piezas[] = $filas['product_quantity'];
                                                $sumar[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                                            }
                                    
                                    }
                                
                                }
                            }
                            
                        }
                    }

                    foreach($response as $row){
                        for($i=0; $i<3000; $i++){
                            $id_envio = $row[$i]->id;
                            $id_orden = explode(" - ", $row[$i]->consignee_name);
                            if(intval($value['id']) == intval($id_orden[0])){
                                $paqueteria = $row[$i]->total;
                                break;
                            }else{
                                $paqueteria = 0.00;
                            }
                            // echo $id_orden[0] . '<br>';
                            if($id_env == $id_envio){
                                break;
                            }
                        }
                    }

                    $confirmacion = Order::where('id_order', $value['id'])->first();

                    if($confirmacion){
                        $status = $confirmacion->status;
                    }else{
                        $status = 1;
                    }            

                    $sumaCompra = array_sum($sumar);

                    $tablaProdu[] = ['fecha'         => $value['date_add'],
                                    'orden'          => $value['id'],
                                    'referencia'     => $value['reference'],
                                    'total'          => $value['total_paid'],
                                    'descuento'      => $value['total_discounts'],
                                    'envio'          => $value['total_shipping_tax_incl'],
                                    'pagado'         => $value['total_products'],
                                    'sin_iva'        => $value['total_paid_tax_excl'],
                                    'compra'         => $sumaCompra,
                                    'paqueteria'     => $paqueteria,
                                    'comision'       => $value['payment'],
                                    // 'utilidad'       => 'pendiente',
                                    'confirmacion'   => $value['current_state'],
                                    'status'         => $status,
                    ];

                    $sumar = [];
                    $total_venta[] = $value['total_paid'];
                    $sumaTotalPiezas = array_sum($total_piezas);
                    $total_sin_iva[] = $value['total_paid_tax_excl'];
                    $total_compra[] = $sumaCompra;
                    $total_envio[] = $value['total_shipping_tax_incl'];
                    
                }

            }

            $sumaTotalVenta = array_sum($total_venta);

            $ordenarTabla = $tablaProdu;

            $parametros = ['ordenes' => $ordenarTabla];
            $total_piezas = $sumaTotalPiezas;
            $total_venta = $sumaTotalVenta;
            $sumaSinIva = array_sum($total_sin_iva);
            $sumaCompra = array_sum($total_compra);
            $sumaEnvio = array_sum($total_envio);

            $total_utilidad = [
                'sumaSinIva'  => $sumaSinIva,
                'sumaCompra'  => $sumaCompra,
                'sumaEnvio'   => $sumaEnvio
            ];

            $filtro = [
                'de_fecha' => 2020-01-01,
                'a_fecha' => date('Y-m-d')            
            ];

        }

        // dd($arrayOrder);

        /*
        if (request('fecha') || request('mes')) {
            if (request('fecha')) {
                $parametros['rango'] = request('fecha');
                $rango = explode(' - ', request('fecha'));
                $inicio =  date('Y-m-d', strtotime($rango[0]));
                $final =  date('Y-m-d', strtotime($rango[1]));

                $registros = DB::connection('mysql2')->select("SELECT o.id_order, o.reference, o.payment,o.current_state, o.module, oi.total_products_wt, oi.total_shipping_tax_incl, 
                                                                      oi.total_discount_tax_incl, oi.total_paid_tax_excl, r.free_shipping, oi.total_paid_tax_incl, o.date_add,
                                                                      (SELECT GROUP_CONCAT(d.product_quantity, 'x', d.product_name) AS productos
                                                                         FROM psj_order_detail d
                                                                        WHERE d.id_order=o.id_order) AS productos,
                                                                      (SELECT SUM(d.product_quantity) AS piezas
                                                                         FROM psj_order_detail d
                                                                        WHERE d.id_order=o.id_order) AS piezas,
                                                                      (SELECT 'true' AS aplicar
                                                                         FROM psj_order_detail od 
                                                                        WHERE (od.reduction_percent>0 OR od.reduction_amount>0)
                                                                          AND od.id_order=o.id_order
                                                                        LIMIT 1) AS precio_especial
                                                                 FROM psj_orders o 
                                                                 LEFT JOIN psj_order_invoice oi ON o.id_order=oi.id_order
                                                                 LEFT JOIN psj_order_cart_rule r ON o.id_order=r.id_order
                                                                WHERE o.current_state<>6
                                                                  AND DATE(o.date_add) BETWEEN :inicio AND :final
                                                                GROUP BY o.id_order
                                                                ORDER BY oi.date_add DESC", ['inicio' => $inicio, 'final' => $final]);
            } else {
                $mes = $parametros['mes'] = request('mes');
                $anio= date('Y');
                
                $sQuery= "SELECT o.id_order, o.reference, o.payment,o.current_state, o.module, oi.total_products_wt, oi.total_shipping_tax_incl, 
                oi.total_discount_tax_incl, oi.total_paid_tax_excl, r.free_shipping, oi.total_paid_tax_incl, o.date_add,
                (SELECT GROUP_CONCAT(d.product_quantity, 'x', d.product_name) AS productos
                   FROM psj_order_detail d
                  WHERE d.id_order=o.id_order) AS productos,
                (SELECT SUM(d.product_quantity) AS piezas
                   FROM psj_order_detail d
                  WHERE d.id_order=o.id_order) AS piezas,
                (SELECT 'true' AS aplicar
                   FROM psj_order_detail od 
                  WHERE (od.reduction_percent>0 OR od.reduction_amount>0)
                    AND od.id_order=o.id_order
                  LIMIT 1) AS precio_especial
           FROM psj_orders o 
           LEFT JOIN psj_order_invoice oi ON o.id_order=oi.id_order
           LEFT JOIN psj_order_cart_rule r ON o.id_order=r.id_order
          WHERE o.current_state<>6
            AND YEAR(o.date_add) =  '2021'
            AND MONTH(o.date_add) ='1'
          GROUP BY o.id_order
          ORDER BY oi.date_add DESC";


                $registros = DB::connection('mysql2')->select("SELECT o.id_order, o.reference, o.payment,o.current_state, o.module, oi.total_products_wt, oi.total_shipping_tax_incl, 
                                                                      oi.total_discount_tax_incl, oi.total_paid_tax_excl, r.free_shipping, oi.total_paid_tax_incl, o.date_add,
                                                                      (SELECT GROUP_CONCAT(d.product_quantity, 'x', d.product_name) AS productos
                                                                         FROM psj_order_detail d
                                                                        WHERE d.id_order=o.id_order) AS productos,
                                                                      (SELECT SUM(d.product_quantity) AS piezas
                                                                         FROM psj_order_detail d
                                                                        WHERE d.id_order=o.id_order) AS piezas,
                                                                      (SELECT 'true' AS aplicar
                                                                         FROM psj_order_detail od 
                                                                        WHERE (od.reduction_percent>0 OR od.reduction_amount>0)
                                                                          AND od.id_order=o.id_order
                                                                        LIMIT 1) AS precio_especial
                                                                 FROM psj_orders o 
                                                                 LEFT JOIN psj_order_invoice oi ON o.id_order=oi.id_order
                                                                 LEFT JOIN psj_order_cart_rule r ON o.id_order=r.id_order
                                                                WHERE o.current_state<>6
                                                                  AND YEAR(o.date_add) = :anio
                                                                  AND MONTH(o.date_add) = :mes
                                                                GROUP BY o.id_order
                                                                ORDER BY oi.date_add DESC", ['mes' => $mes, 'anio' => date('Y')]);
            }

            foreach ($registros as $key => $value) {
                $siniva = $value->total_paid_tax_incl / 1.16;

                //Cálculo de descuento en caso de que el producto lleve aplicado el descuento directo 
                if ($value->precio_especial != null) {
                    $detalle = DB::connection('mysql2')->select("SELECT product_name, product_quantity, original_product_price, unit_price_tax_incl
                                                                   FROM psj_order_detail
                                                                  WHERE id_order = :id_order", ['id_order' => $value->id_order]);
                    $real = 0;
                    $rebajado = 0;
                    foreach ($detalle as $ke => $va) {
                        $precio = round($va->original_product_price + ($va->original_product_price * 0.16));
                        $real += ($va->product_quantity * $precio);
                        $rebajado += ($va->product_quantity * $va->unit_price_tax_incl);
                        $value->total_discount_tax_incl = $real - $rebajado;
                    }

                    $value->total_products_wt = $real;
                }


              
                
                $comision = 0.00;
                switch ($value->payment) {
                    case 'Conekta oxxo_cash':
                        $valor = $value->total_paid_tax_incl * 0.039;
                        $iva = $valor * 0.16;
                        $comision = round($valor + $iva, 2);
                        // $comision = bcdiv($valor+$iva, 1, 2);
                        break;

                    case 'Conekta tarjetas de crédito':
                    case 'Conekta Cards':
                        $valor = $value->total_paid_tax_incl * 0.029 + 2.5;
                        $iva = $valor * 0.16;
                        $comision = round($valor + $iva, 2);
                        break;

                    case 'Pago por transferencia bancaria':
                        $comision = 0.00;
                        break;

                    case 'PayPal':
                        $valor = $value->total_paid_tax_incl * 0.0395 + 4;
                        $comision = round($valor, 2);
                        break;

                    case 'Stripe Payment Pro':
                        $valor = $value->total_paid_tax_incl * 0.036 + 3;
                        $comision = round($valor, 2);
                        break;
                }

                $pedido = [
                    'fecha'      => $value->date_add,
                    'current_state'      => $value->current_state,
                    'productos'  => $value->productos,
                    'orden'      => $value->id_order,
                    'referencia' => $value->reference,
                    'total'      => number_format($value->total_products_wt, 2, '.', ''),
                    'descuento'  => number_format(($value->total_discount_tax_incl * -1), 2, '.', ''),
                    'envio'      => number_format($value->total_shipping_tax_incl, 2, '.', ''),
                    'pagado'     => number_format($value->total_paid_tax_incl, 2, '.', ''),
                    'espacio'    => '',
                    'siniva'     => number_format($siniva, 2, '.', ''),
                    'compra'     => 0.00,
                    'paqueteria' => 0.00,
                    'comision'   => number_format(($comision * -1), 2, '.', ''),
                    'plataforma' => $value->payment,
                    'utilidad'   => 0.00
                ];

                //Checar si tiene valores como tarjeta de regalo
                $detailgift = DB::connection('mysql2')->select("SELECT COUNT(*) AS existe
                                                                  FROM psj_gift_card_customer gcc
                                                                 WHERE gcc.id_order=:orden", ['orden' => $pedido['orden']]);
                if ($detailgift[0]->existe > 0) {
                    $detailproducts = DB::connection('mysql2')->select("SELECT (od.product_price * od.product_quantity) AS valor
                                                                          FROM psj_order_detail od
                                                                         WHERE od.id_order=:orden
                                                                           AND product_reference='GIFT_PRODUCT_'", ['orden' => $pedido['orden']]);

                    $total_sin_iva_ni_tarjeta = ($value->total_paid_tax_incl - $detailproducts[0]->valor) / 1.16 + $detailproducts[0]->valor;
                    $pedido['siniva'] = number_format($total_sin_iva_ni_tarjeta, 2, '.', '');
                }

                $detalles = DB::connection('mysql2')->select("SELECT od.product_quantity, pl.name
                                                                FROM psj_order_detail od
                                                                LEFT JOIN psj_product_lang pl ON od.product_id=pl.id_product
                                                               WHERE od.id_order=:orden
                                                                 AND pl.id_lang=:idioma", [
                    'orden'  => $value->id_order,
                    'idioma' => 2
                ]);

                foreach ($detalles as $ke => $va) {
                    try {
                        $precio = Product::select('costo')->where('producto', '=', $va->name)->first();
                        $paqueteria = Delivery::select('precio')->where('id_orden', '=', $value->id_order)->first();
                        if (isset($precio['costo'])) {
                            $pedido['compra'] += number_format(($precio['costo'] * $va->product_quantity) * -1, 2, '.', '');
                        }
                        $pedido['paqueteria'] = number_format($paqueteria == null ? 0.00 : $paqueteria['precio'] * -1, 2, '.', '');
                        $utilidad = $pedido['siniva'] + $pedido['compra'] + $pedido['comision'] + $pedido['paqueteria'];
                        $pedido['utilidad'] = number_format($utilidad, 2, '.', '');
                    } catch (Exception $exception) {
                    }
                }

                $pedido['confirmacion'] = false;
                $confirmacion = Confirmation::select('confirmado', DB::raw('DATE_FORMAT(created_at,  \'%Y-%m-%d\') AS created_at'))->where('referencia', '=', $pedido['referencia'])->first();
              
             
                if (isset($confirmacion['confirmado']) && $confirmacion['confirmado'] == 1) {
                    $pedido['confirmacion'] = true;
                    $fecha_confirmacion = strtotime($confirmacion['created_at'] . ' 00:00:00');
                    $hoy = strtotime(date('Y-m-d') . ' 00:00:00');
                    $pedido['color'] = 'danger';
                    if ($hoy > $fecha_confirmacion) {
                        $pedido['color'] = 'success';
                    }
                    //Sumatoria solo si el pedido esta confirmado
                    $parametros['totales']['piezas'] += $value->piezas;
                    $parametros['totales']['venta'] += $value->total_paid_tax_incl;
                    $parametros['totales']['utilidad'] += $utilidad;
                } else {
                    $pedido['color'] = 'danger';
                }

                $ventas[] = $pedido;
            }

            $parametros['totales']['porcentaje'] = 0;
            if ($parametros['totales']['utilidad'] > 0 && $parametros['totales']['venta'] > 0) {
                $parametros['totales']['porcentaje'] = ($parametros['totales']['utilidad'] * 100) / $parametros['totales']['venta'];
            }
            $parametros['ventas'] = $ventas;

  
        }
        */
        
        // $total_utilidad = $sumaSinIva - $sumaCompra - $comision - $sumaEnvio

        return view('admin.ventas.ventas', compact('parametros','total_piezas','total_venta','total_utilidad','filtro'));

    }

    public function confirmacion_p(Request $request)
    {   
        $confirmacion = $request->confirm;
        $id_orden = $request->id_orden;

        $output = "";

        $valida = Order::where('id_order', $id_orden)->first();
        
        if($valida){
            $orden = Order::where('id_order', $id_orden)->first();
            $orden->status = $confirmacion;
            $orden->save();
        }else{
            $orden = new Order();
            $orden->id_order = $id_orden;
            $orden->status = $confirmacion;
            $orden->save();
        }   
        
        if($confirmacion == 'si'){
            $output = 1;
        }else{
            $output = 0;
        }

        $response = array(
            'status' => 'success',
            'msg' => $output,
        );

        return response()->json($response);

    }

    //Funciones para Realizar Exportaciones
    public function exportSales(Request $request)
    {
        $nombre = 'ventas-' . date('dMYHi');

    
        return Excel::download(new SalesExport(request('valor'), request('parametro')), "$nombre.xlsx");
    }

    public function listaPrecios()
    {
        $permitido = $this->buscaPermiso('reportes.listaprecios', Auth::user()->permision_id);
        if (!array_key_exists('consultar', $permitido) && !array_key_exists('todo', $permitido)) {
            return redirect()->route('home')->with('warning', __('layout.sinpermiso'));
        }

        $categories = [];
        if (request('categorias') != null) {
            $categories = request('categorias');
            $registros = Product::select(
                'products.producto',
                'products.sku',
                'products.codigo',
                DB::raw('(SELECT GROUP_CONCAT(c.categoria) AS categoria
                                                     FROM product_to_categories pc
                                                     LEFT JOIN categories c ON pc.categoria_id=c.id
                                                    WHERE pc.producto_id=products.id) AS categoria'),
                DB::raw('(SELECT SUM(stock) FROM stocks WHERE producto_id=products.id) AS stock'),
                'products.costo',
                'products.precio_iva',
                'products.activo',
                DB::raw('DATE_FORMAT(products.updated_at, \'%d/%m/%Y %H:%i\') AS fecha')
            )
                ->leftjoin('product_to_categories', 'products.id', '=', 'product_to_categories.producto_id')
                ->whereIn('product_to_categories.categoria_id', $categories)->get()->toArray();
        } else {
            $registros = Product::select(
                'products.producto',
                'products.sku',
                'products.codigo',
                DB::raw('(SELECT GROUP_CONCAT(c.categoria) AS categoria
                                                     FROM product_to_categories pc
                                                     LEFT JOIN categories c ON pc.categoria_id=c.id
                                                    WHERE pc.producto_id=products.id) AS categoria'),
                DB::raw('(SELECT SUM(stock) FROM stocks WHERE producto_id=products.id) AS stock'),
                'products.costo',
                'products.precio_iva',
                'products.activo',
                DB::raw('DATE_FORMAT(products.updated_at, \'%d/%m/%Y %H:%i\') AS fecha')
            )->get()->toArray();
        }

        $totales = [
            'existencia' => 0,
            'compra'     => 0,
            'venta'      => 0
        ];

        foreach ($registros as $key => $value) {
            $registros[$key]['costo'] = number_format($value['costo'], 2);
            $vcompra = $value['costo'] * $value['stock'];
            $registros[$key]['precio_iva'] = number_format($value['precio_iva'], 2);
            $vventa = $value['precio_iva'] * $value['stock'];

            $totales['existencia'] += $value['stock'];
            $totales['compra'] += $vcompra;
            $totales['venta'] += $vventa;
        }

        $totales['existencia'] = number_format($totales['existencia'], 2);
        $totales['compra'] = number_format($totales['compra'], 2);
        $totales['venta'] = number_format($totales['venta'], 2);

        $parametros = [
            'registros'   => $registros,
            'totales'     => $totales,
            'categories'  => json_encode($categories),
            'categorias'  => Category::select('id', 'categoria')->where('activo', '=', '1')->orderBy('categoria', 'asc')->get()->toArray()
        ];

        return view('reporte.listaprecios', compact('parametros'));
    }

}