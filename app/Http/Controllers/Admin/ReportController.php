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

        $anio = date('Y');

        $response1 = $client->request(
            'GET',
            'guide/01/' . $anio, 
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response1 = json_decode($response1);

        foreach($response1 as $row){
            $ultimo_env[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env[0]->id;

        foreach($response1 as $row){
            for($i=0; $i<3000; $i++){
                $id_envio = $row[$i]->id;
                $id_orden = explode(" - ", $row[$i]->consignee_name);
                $id_orden = $id_orden[0];
                $seguro = $row[$i]->insurance_cost;
                $total_orden = $row[$i]->total;
                $response11[$i] = array(0 => $id_envio, 1 => $id_orden, 2 => $total_orden, 3 => $seguro);

                if($id_env == $id_envio){
                    break;
                }
            }
        }

        $response2 = $client->request(
            'GET',
            'guide/02/' . $anio,
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response2 = json_decode($response2);

        foreach($response2 as $row){
            $ultimo_env2[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env2[0]->id;

        foreach($response2 as $row){
            for($i=0; $i<3000; $i++){
                $id_envio = $row[$i]->id;
                $id_orden = explode(" - ", $row[$i]->consignee_name);
                $id_orden = $id_orden[0];
                $seguro = $row[$i]->insurance_cost;
                $total_orden = $row[$i]->total;
                $response22[$i] = array(0 => $id_envio, 1 => $id_orden, 2 => $total_orden, 3 => $seguro);

                if($id_env == $id_envio){
                    break;
                }
            }
        }

        $response3 = $client->request(
            'GET',
            'guide/03/' . $anio,
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response3 = json_decode($response3);

        foreach($response3 as $row){
            $ultimo_env3[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env3[0]->id;

        foreach($response3 as $row){
            for($i=0; $i<3000; $i++){
                $id_envio = $row[$i]->id;
                $id_orden = explode(" - ", $row[$i]->consignee_name);
                $id_orden = $id_orden[0];
                $seguro = $row[$i]->insurance_cost;
                $total_orden = $row[$i]->total;
                $response33[$i] = array(0 => $id_envio, 1 => $id_orden, 2 => $total_orden, 3 => $seguro);

                if($id_env == $id_envio){
                    break;
                }
            }
        }
        
        $response4 = $client->request(
            'GET',
            'guide/04/' . $anio, 
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response4 = json_decode($response4);

        $response4_seguro = $client->request(
            'GET',
            'invoice/04/' . $anio, 
            ['headers' => 
                [
                    'Authorization' => "Bearer 448f95d66c4b3751a19ae8a5162f9498df781f4e46070e51df3a4cd8c0dac349"
                ]
            ]
        )->getBody()->getContents();
        
        $response4_seguro = json_decode($response4_seguro);

        foreach($response4_seguro as $key => $row){
            for($i=0; $i<count($row); $i++){
                $num_guia = $row[$i]->tracking_number;
                $array_seguro4[$num_guia] = str_replace("$", "", $row[$i]->insurance);
            }
        }

        foreach($response4 as $row){
            $ultimo_env4[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env4[0]->id;

        foreach($response4 as $row){
            for($i=0; $i<3000; $i++){
                $id_envio = $row[$i]->id;
                $numero_guia = $row[$i]->tracking_number;
                $id_orden = explode(" - ", $row[$i]->consignee_name);
                $id_orden = $id_orden[0];
                $total_orden = $row[$i]->total;
                if(array_key_exists($numero_guia, $array_seguro4)){
                    $seguro = $array_seguro4[$numero_guia];
                    if($seguro == 1.00){
                        $seguro = 100.00;
                    }else{
                        $seguro = 0.00;
                    }
                }
                $response44[$i] = array(0 => $id_envio, 1 => $id_orden, 2 => $total_orden, 3 => $seguro);

                if($id_env == $id_envio){
                    break;
                }
            }
        }
        
        $response = array_merge($response11,$response22,$response33,$response44);
        /*
        foreach($response as $row){
            $ultimo_env[] = $row[array_key_last($row)];
        }
        
        $id_env = $ultimo_env[0]->id;*/
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
        // dd($response);

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

            $total_pedidos = 0;

            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    if(($value['date_add'] >= $_REQUEST['de_fecha']) && ($value['date_add'] <= $_REQUEST['a_fecha'])){

                        foreach($arrayProdu['products']['product'] as $inPro => $valPro) {

                            foreach($ejem as $key => $row){
                                if($value['id'] == $key){
                                    if(in_array(0, $ejem[$key])){              

                                            if($valPro['id'] == $ejem[$key]['product_id']) {
                                                $num_cdad_produ = $ejem[$key]['product_quantity'];
                                                $nombre_produ = $ejem[$key]['product_name'];
                                                $precio_produ = $ejem[$key]['unit_price_tax_incl'];
                                                $array_produ[$num_cdad_produ] = $nombre_produ . ' - $' . number_format($precio_produ, 2);
                                                $total_piezas[] = $ejem[$key]['product_quantity'];
                                                $sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                                            }                          
                                        
                                    }else{

                                        foreach($ejem[$key] as $filas){
                                            
                                                if($valPro['id'] == $filas['product_id']) {
                                                    $num_cdad_produ = $filas['product_quantity'];
                                                    $nombre_produ = $filas['product_name'];
                                                    $precio_produ = $filas['unit_price_tax_incl'];
                                                    $array_produ[$num_cdad_produ] = $nombre_produ . ' - $' . number_format($precio_produ, 2);
                                                    $total_piezas[] = $filas['product_quantity'];
                                                    $sumar[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                                                }
                                        
                                        }
                                    
                                    }
                                }
                                
                            }
                        }
                        
                        foreach($response as $row){
                        
                            $id_envio = $row[0];
                            $id_orden = $row[1];
                            if(intval($value['id']) == intval($id_orden)){
                                $paqueteria = $row[2];
                                $seguro = $row[3];
                                break;
                            }else{
                                $paqueteria = 0.00;
                                $seguro = 0.00;
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
                                        'seguro_envio'   => $value['total_wrapping_tax_incl'],
                                        'pagado'         => $value['total_products_wt'],
                                        'sin_iva'        => $value['total_paid_tax_excl'],
                                        'compra'         => $sumaCompra,
                                        'paqueteria'     => $paqueteria,
                                        'seguro'         => $seguro,
                                        'comision'       => $value['payment'],
                                        // 'utilidad'       => 'pendiente',
                                        'confirmacion'   => $value['current_state'],
                                        'status'         => $status,
                                        'productos'      => $array_produ,
                        ];

                        $sumar = [];
                        $array_produ = [];
                        $total_venta[] = $value['total_paid'];
                        $sumaTotalPiezas = array_sum($total_piezas);
                        $total_sin_iva[] = $value['total_paid_tax_excl'];
                        $total_compra[] = $sumaCompra;
                        $total_envio[] = $paqueteria;
                        $total_pedidos++;

                    }
                    
                }

            }

            if(!empty($total_venta)){
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
                $sumaTotalVenta = 0.00;
                $ordenarTabla = [];
                
                $parametros = ['ordenes' => $ordenarTabla];
                $total_piezas = [];
                $total_venta = $sumaTotalVenta;
                $sumaSinIva = array();
                $sumaCompra = array();
                $sumaEnvio = array();

                $total_utilidad = [
                    'sumaSinIva'  => $sumaSinIva,
                    'sumaCompra'  => $sumaCompra,
                    'sumaEnvio'   => $sumaEnvio
                ];

                $filtro = [
                    'de_fecha' => $_REQUEST['de_fecha'],
                    'a_fecha' => $_REQUEST['a_fecha']            
                ];
            }

        }else{
            
            foreach($arrayOrder['orders']['order'] as $i => $v) {

                if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                    
                    // $suma[] = floatval($v['total_paid']);
                    $id_orden = $v['id'];
                    $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];

                }
            }
            
            $total_pedidos = 0;

            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    foreach($arrayProdu['products']['product'] as $inPro => $valPro) {

                        foreach($ejem as $key => $row){
                            if($value['id'] == $key){
                                if(in_array(0, $ejem[$key])){              

                                    if($valPro['id'] == $ejem[$key]['product_id']) {
                                        $num_cdad_produ = $ejem[$key]['product_quantity'];
                                        $nombre_produ = $ejem[$key]['product_name'];
                                        $precio_produ = $ejem[$key]['unit_price_tax_incl'];
                                        $array_produ[$num_cdad_produ] = $nombre_produ . ' - $' . number_format($precio_produ, 2);  
                                        $total_piezas[] = $ejem[$key]['product_quantity'];
                                        $sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                                    }                          
                                    
                                }else{

                                    foreach($ejem[$key] as $filas){
                                        
                                        if($valPro['id'] == $filas['product_id']) {
                                            $num_cdad_produ = $filas['product_quantity'];
                                            $nombre_produ = $filas['product_name'];
                                            $precio_produ = $filas['unit_price_tax_incl'];
                                            $array_produ[$num_cdad_produ] = $nombre_produ . ' - $' . number_format($precio_produ, 2);
                                            $total_piezas[] = $filas['product_quantity'];
                                            $sumar[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                                        }
                                    
                                    }
                                
                                }
                            }
                            
                        }
                    }

                    foreach($response as $row){
                        
                        $id_envio = $row[0];
                        $id_orden = $row[1];
                        if(intval($value['id']) == intval($id_orden)){
                            $paqueteria = $row[2];
                            $seguro = $row[3];
                            break;
                        }else{
                            $paqueteria = 0.00;
                            $seguro = 0.00;
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
                                    'seguro_envio'   => $value['total_wrapping_tax_incl'],
                                    'pagado'         => $value['total_products_wt'],
                                    'sin_iva'        => $value['total_paid_tax_excl'],
                                    'compra'         => $sumaCompra,
                                    'paqueteria'     => $paqueteria,
                                    'seguro'         => $seguro,
                                    'comision'       => $value['payment'],
                                    // 'utilidad'       => 'pendiente',
                                    'confirmacion'   => $value['current_state'],
                                    'status'         => $status,
                                    'productos'      => $array_produ,
                    ];

                    $sumar = [];
                    $array_produ = [];
                    $total_venta[] = $value['total_paid'];
                    $sumaTotalPiezas = array_sum($total_piezas);
                    $total_sin_iva[] = $value['total_paid_tax_excl'];
                    $total_compra[] = $sumaCompra;
                    $total_envio[] = $paqueteria;
                    
                }
                $total_pedidos++;

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
        // $total_utilidad = $sumaSinIva - $sumaCompra - $comision - $sumaEnvio

        return view('admin.ventas.ventas', compact('parametros','total_pedidos','total_piezas','total_venta','total_utilidad','filtro'));

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