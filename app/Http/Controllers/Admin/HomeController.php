<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Crew;
use DB;
Use Prestashop;

class HomeController extends Controller
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
    public function index()
    {
        
        $urlOrdes['resource'] = 'orders/?display=full';
        $xmlOrders = Prestashop::get($urlOrdes);

        $urlProduct['resource'] = 'products/?sort=[id_ASC]&display=full';
        $xmlProduct = Prestashop::get($urlProduct);

        $jsonOrders = json_encode($xmlOrders);
        $arrayOrders = json_decode($jsonOrders, true);

        $jsonProduct = json_encode($xmlProduct);
        $arrayProduct = json_decode($jsonProduct, true);

        foreach($arrayOrders['orders']['order'] as $i => $v) {
            
            if($v['current_state'] == "3"|| $v['current_state'] == "5" || $v['current_state'] == "4" || $v['current_state'] == "2") {
            
                //$suma[] = floatval($v['total_paid']);
                $ejem[] = $v['associations']['order_rows']['order_row'];
                $rangoGraf[] = date('Y-m', strtotime($v['date_add']));
            }
        }
        try {
            
            foreach($arrayProduct['products']['product'] as $inPro => $valPro) {

                foreach($ejem as $key => $row){
                    
                    if(in_array(0, $ejem[$key])){
    
                        if($valPro['id'] == $ejem[$key]['product_id']) {

                            if(is_numeric($valPro['id_default_image'])) {

                                $imagen = $valPro['id_default_image'];

                            }else {

                                $imagen = 1;
                            }

                            $producto[] = ['cantidad' => $ejem[$key]['product_quantity'],
                                            'nombre'    => $ejem[$key]['product_name'],
                                            'imagen'    => "https://himart.com.mx/api/images/products/".$ejem[$key]['product_id']."/".$imagen."/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4",
                                            'id' => $ejem[$key]['product_id'],
                                          ];
                            //$sumar[] = floatval($valPro['wholesale_price']) * floatval($ejem[$key]['product_quantity']);
                        }
                        
                        
                    }else{
                        foreach($ejem[$key] as $filas){

                            if($valPro['id'] == $filas['product_id']) {

                                if(is_numeric($valPro['id_default_image'])) {

                                    $imagen = $valPro['id_default_image'];
                                    
                                }else {
    
                                    $imagen = 1;
                                }

                                $producto2[] = ['cantidad'  => $filas['product_quantity'],
                                                'nombre'    => $filas['product_name'],
                                                'imagen'    => "https://himart.com.mx/api/images/products/".$filas['product_id']."/".$imagen."/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4", 
                                                'id'        => $filas['product_id'],
                                ];
                                //$sumar2[] = floatval($valPro['wholesale_price']) * floatval($filas['product_quantity']);
                            }
                            
                        }
                    
                    }
                }
            }
            
            //Mostrar el valor total de productos vendidos
            $ids_products = [];
            foreach($producto as $arre_product) {

                $id_products = $arre_product['id'];

                if(! in_array($id_products, $ids_products)) {

                    $ids_products[] = $id_products;
                }

            }
            $result = [];

            foreach($ids_products as $unique_id) {

                $temp = [];
                $quantity = 0;

                foreach($producto as $arre_product) {

                    $id = $arre_product['id'];

                    if($id === $unique_id) {
                        $temp[] = $arre_product;
                    }
                }

                $produ = $temp[0];

                $produ['cantidad'] = 0;

                foreach($temp as $product_temp) {

                    $produ['cantidad'] = $produ['cantidad'] + $product_temp['cantidad'];
                }

                $result[] = $produ;
            }

            //Mostrar el valor total de productos vendidos 2
            $ids_products2 = [];
            foreach($producto2 as $arre_product2) {

                $id_products2 = $arre_product2['id'];

                if(! in_array($id_products2, $ids_products2)) {

                    $ids_products2[] = $id_products2;
                }

            }
            $result2 = [];

            foreach($ids_products2 as $unique_id2) {

                $temp2 = [];
                //$quantity = 0;

                foreach($producto2 as $arre_product2) {

                    $id2 = $arre_product2['id'];

                    if($id2 === $unique_id2) {
                        $temp2[] = $arre_product2;
                    }
                }

                $produ2 = $temp2[0];

                $produ2['cantidad'] = 0;

                foreach($temp2 as $product_temp2) {

                    $produ2['cantidad'] = $produ2['cantidad'] + $product_temp2['cantidad'];
                }

                $result2[] = $produ2;
            }
            
        }catch (Exception $e) {
            
            return back()->with('Error', 'No se encontraron registros de pedidos con pago confirmado');
        }

        /*if(isset($sumar)) {

            $sumaCompra = array_merge($sumar, $sumar2);
        }else {

            $sumaCompra = $sumar2;
        }*/
        
        $productosVendidos = array_merge($result, $result2);
        arsort($productosVendidos);
        $topTen = array_slice($productosVendidos, 0, 10);

        //dd($topTen);

        $datosGraf = array_count_values($rangoGraf);  
        //$totalCompra = array_sum($sumaCompra);
        //$total = array_sum($suma);

        $parametros = [//'totalVentaOrden'     => $total,
                        //'totalCompra'        => $totalCompra,
                        'CantidadVendida'      => $topTen,
                        'rangoGra'          => $datosGraf,
                      ];
                    
        //dd($mes);

        return view('admin.layout.home', compact('parametros'));
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

    public function getBloqVac() {

        $festivos = Holiday::select('holidays.id', 'holidays.festividad', 'holidays.fecha_descanso', 'holidays.fecha_conmemorativa')
                           ->orderBy('holidays.fecha_descanso', 'asc')->get()->toArray();

        $resultado = [];
        foreach ($festivos as $key => $value) {
            $resultado[] = ['id'              => $value['id'],
                            'title'           => $value['festividad'],
                            'start'           => $value['fecha_descanso'],
                            'allDay'          => true,
                            'icon'            => 'fas fa-mug-hot',
                            'backgroundColor' => '#ff5200',
                            'eventConstraint' => ['start'  => $value['fecha_descanso'],
                                                  'allDay' => date('Y-m-d', strtotime($value['fecha_descanso'] . ' +1 day')), ]];
        }
        $hbd = Crew::select('crews.id', DB::raw("users.id AS user_id"), DB::raw("DATE_FORMAT(crews.nacimiento, '%m-%d') AS fecha"), 'users.name')
                   ->join('users', 'crews.user_id', '=', 'users.id')
                   ->get()->toArray();

        foreach ($hbd as $key => $value) {
            $fecha = date('Y').'-'.$value['fecha'];
            $resultado[] = ['id'              => $value['id'].$key.$value['user_id'],
                            'title'           => $value['name'],
                            'start'           => $fecha,
                            'allDay'          => true,
                            'icon'            => 'fas fa-birthday-cake',
                            'backgroundColor' => '#FC427B',
                            'eventConstraint' => ['start'  => $fecha,
                                                  'allDay' => date('Y-m-d', strtotime($fecha . ' +1 day')), ]];
        }
        /*-----------------------------------nuevo----------------------------------*/
            
        /*----------------------------------fin nuevo-------------------------------*/

        $ingreso = Crew::select('crews.id', DB::raw("users.id AS user_id"), DB::raw("DATE_FORMAT(crews.ingreso, '%m-%d') AS fecha"), 'crews.ingreso', 'users.name')
                       ->join('users', 'crews.user_id', '=', 'users.id')
                       ->get()->toArray();
        $lastyear = date('Y').'-12-31';
        $years = [date('Y'), date('Y', strtotime($lastyear . ' +1 year'))];
        foreach ($years as $k => $v) {
            if($k > 0) {
                $lastyear = date('Y-m-d', strtotime($lastyear . ' +1 year'));
            }

            foreach ($ingreso as $key => $value) {
                $fecha = $v.'-'.$value['fecha'];
                $datetime1 = date_create($value['ingreso']);
                $datetime2 = date_create($lastyear);
                $interval = date_diff($datetime1, $datetime2);
                $servicio = $interval->format('%y');
                if($servicio >= 1) {
                    $resultado[] = ['id'              => $value['id'].$key.$value['user_id'],
                                    'title'           => $value['name'],
                                    'start'           => $fecha,
                                    'allDay'          => true,
                                    'icon'            => 'fas fa-flag-checkered',
                                    'backgroundColor' => '#ff7675',
                                    'eventConstraint' => ['start'  => $fecha,
                                                          'allDay' => date('Y-m-d', strtotime($fecha . ' +1 day')), ]];
                }
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        die();
    }
}
