<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use App\Sku;
use App\Product;
use App\Expiration;
use Prestashop;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestaShopWebserviceException;

class ProductoController extends Controller
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
        // $image = $request->file('image');
        // $image->move('uploads', $image->getClientOriginalName());

        /*
        $urlImage['resource'] = 'images/products/10'; //pasamos los parametros por url de la apí
        $xmlImage = Prestashop::get($urlImage); //llama los parametros por GET

        $jsonImage = json_encode($xmlImage);
        $arrayImage = json_decode($jsonImage, true);

        for($i=0; $i<count($arrayImage['image']['declination']); $i++){
            foreach($arrayImage['image']['declination'] as $value) {
                echo $value['id'] . '<br>';
            }
        }*/

        // $xml = $xmlProdu->products->children();

        // dd($xmlProdu);
        // return false;
        
        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $urlCateg['resource'] = 'categories/?display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $urlOrder['resource'] = 'orders/?sort=[id_DESC]&display=full'; //pasamos los parametros por url de la apí
        $xmlOrder = Prestashop::get($urlOrder); //llama los parametros por GET

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);

        $jsonOrder = json_encode($xmlOrder);    
        $arrayOrder = json_decode($jsonOrder, true);  

        foreach($arrayOrder['orders']['order'] as $i => $v) {

            if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                
                // $suma[] = floatval($v['total_paid']);
                $id_orden = $v['id'];
                $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];

            }
        }

        if(isset($_REQUEST['filtro_produ'])){

            $cdad_piezas = [];

            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    foreach($arrayProdu['products']['product'] as $inPro => $valPro) {
                        
                        if(($value['date_add'] >= $_REQUEST['de_fecha']) && ($value['date_add'] <= $_REQUEST['a_fecha'])){

                            foreach($ejem as $key => $row){
                                if($value['id'] == $key){
                                    if(in_array(0, $ejem[$key])){              

                                        if($valPro['id'] == $ejem[$key]['product_id']) {
                                            $id_produ = $ejem[$key]['product_id'];

                                            if(!array_key_exists($ejem[$key]['product_id'], $cdad_piezas)){
                                                $cdad_piezas[$id_produ] = $ejem[$key]['product_quantity'];
                                            }else{
                                                $cdad_piezas[$id_produ] += $ejem[$key]['product_quantity'];
                                            }
                                        }                          
                                        
                                    }else{

                                        foreach($ejem[$key] as $filas){
                                            
                                            if($valPro['id'] == $filas['product_id']) {
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
                                
                            }
                            
                        }
                    }
                    
                }
                
            }

            foreach($arrayCateg["categories"]["category"] as $index => $categ) {
                
                foreach($arrayProdu['products']['product'] as $key => $value) {
    
                    foreach($arrayStock['stock_availables']['stock_available'] as $item => $valor) {
    
                        if($value['id'] == $valor['id_product'] && $value['id_category_default'] == $categ['id']) {
                            
                            if(($categ['name']['language'] == $_REQUEST['categoria'] || $_REQUEST['categoria'] == 1) && (($valor['quantity'] >= $_REQUEST['de_stock']) && ($valor['quantity'] <= $_REQUEST['a_stock'])) && (($value['price'] >= $_REQUEST['de_precio']) && ($value['price'] <= $_REQUEST['a_precio']))){
                                
                                if(is_numeric($value['id_default_image'])){

                                    $id_imagen = $value['id_default_image'];
    
                                }else{
                                    $id_imagen = 1;
                                }

                                if(array_key_exists($value['id'], $cdad_piezas)){
                                    $id_product = $value['id'];
                                    $sumaTotalPiezas = $cdad_piezas[$id_product];
                                }else{
                                    $sumaTotalPiezas = 0;
                                }

                                $tablaProdu[] = ['id'          => $value['id'],
                                                'name'         => $value['name']['language'],
                                                'total_piezas' => $sumaTotalPiezas,
                                                'id_image'     => $id_imagen,
                                                'stock'        => $valor['quantity'],
                                                'reference'    => $value['reference'],
                                                'category'     => $categ['name']['language'], 
                                                'price'        => $value['price'],
                                                'compra'       => $value['wholesale_price'],
                                                'state'        => $value['state'],
                                                'activo'       => $value['active'],
                                                'date_upd'     => $value['date_upd'],
                                ];

                            }
                        }   
                    }                              
                }
            }
            $ordenarTabla = Arr::sort($tablaProdu);

            $filtro = [
                'categoria' => $_REQUEST['categoria'],
                'de_stock' => $_REQUEST['de_stock'],
                'a_stock' => $_REQUEST['a_stock'],
                'de_precio' => $_REQUEST['de_precio'],
                'a_precio' => $_REQUEST['a_precio'],
                'de_fecha' => $_REQUEST['de_fecha'],
                'a_fecha' => $_REQUEST['a_fecha']            
            ];

        }else{

            $cdad_piezas = [];

            foreach($arrayOrder['orders']['order'] as $index => $value) {

                if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                    foreach($arrayProdu['products']['product'] as $inPro => $valPro) {
                        
                        foreach($ejem as $key => $row){
                            if($value['id'] == $key){
                                if(in_array(0, $ejem[$key])){              

                                    if($valPro['id'] == $ejem[$key]['product_id']) {
                                        $id_produ = $ejem[$key]['product_id'];
                                        if(!array_key_exists($ejem[$key]['product_id'], $cdad_piezas)){
                                            $cdad_piezas[$id_produ] = $ejem[$key]['product_quantity'];
                                        }else{
                                            $cdad_piezas[$id_produ] += $ejem[$key]['product_quantity'];
                                        }
                                        /*echo $ejem[$key]['product_id'] . '<br>';
                                        $total_piezas[] = $ejem[$key]['product_quantity'];*/
                                    }                          
                                    
                                }else{

                                    foreach($ejem[$key] as $filas){
                                        
                                        if($valPro['id'] == $filas['product_id']) {
                                            $id_produ = $filas['product_id'];
                                            if(!array_key_exists($filas['product_id'], $cdad_piezas)){
                                                $cdad_piezas[$id_produ] = $filas['product_quantity'];
                                            }else{
                                                $cdad_piezas[$id_produ] += $filas['product_quantity'];
                                            }
                                            /*echo $filas['product_id'] . '<br>';
                                            $total_piezas[] = $filas['product_quantity'];*/
                                        }
                                    
                                    }
                                
                                }
                            }
                            
                        }  
                        
                    }
                    
                }
                
            }

            foreach($arrayCateg["categories"]["category"] as $index => $categ) {
        
                foreach($arrayProdu['products']['product'] as $key => $value) {

                    foreach($arrayStock['stock_availables']['stock_available'] as $item => $valor) {
    
                        if($value['id'] == $valor['id_product'] && $value['id_category_default'] == $categ['id']) {

                            if(is_numeric($value['id_default_image'])){

                                $id_imagen = $value['id_default_image'];

                            }else{
                                $id_imagen = 1;
                            }

                            if(array_key_exists($value['id'], $cdad_piezas)){
                                $id_product = $value['id'];
                                $sumaTotalPiezas = $cdad_piezas[$id_product];
                            }else{
                                $sumaTotalPiezas = 0;
                            }

                            $tablaProdu[] = ['id'          => $value['id'],
                                            'name'         => $value['name']['language'],
                                            'total_piezas' => $sumaTotalPiezas,
                                            'id_image'     => $id_imagen,
                                            'stock'        => $valor['quantity'],
                                            'reference'    => $value['reference'],
                                            'category'     => $categ['name']['language'], 
                                            'price'        => $value['price'],
                                            'compra'       => $value['wholesale_price'],
                                            'state'        => $value['state'],
                                            'activo'       => $value['active'],
                                            'date_upd'     => $value['date_upd'],
                            ];

                        }   
                    }                              
                }
            }
            $ordenarTabla = Arr::sort($tablaProdu);

            $filtro = [
                'categoria' => 1,
                'de_stock' => 0,
                'a_stock' => 200,
                'de_precio' => 0.00,
                'a_precio' => 2000.00,
                'de_fecha' => 2020-01-01,
                'a_fecha' => date('Y-m-d')            
            ];

        }

        //pasamos los parametros a otro arreglo para poder usarlos en el Front
        $parametros = ['productos' => $ordenarTabla,];

        $urlCateg['resource'] = 'categories/?sort=[id_ASC]&display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);

        foreach($arrayCateg["categories"]["category"] as $categorias) {
            
            $tablaCategorias[] = ['id'    => $categorias['id'],
                                  'nombre'=> $categorias['name']['language'],];
        }

        $categorias = ['categorias' => $tablaCategorias];

         //dd($xmlProdu);

        return view('admin.productos.index', compact('parametros','categorias','filtro'));

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @ return \Illuminate\Http\Response
     */

    public function create()
    {
        //
        $urlCateg['resource'] = 'categories/?sort=[id_ASC]&display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);

        foreach($arrayCateg["categories"]["category"] as $categorias) {
            
            $tablaCategorias[] = ['id'    => $categorias['id'],
                                  'nombre'=> $categorias['name']['language'],];
        }

        $parametros = ['categorias' => $tablaCategorias];

        return view('admin.productos.create', compact('parametros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nombre = request('nombre');
        $referencia = request('referencia');
        $catg = request('categoria_id');
        $cantidad = request('cantidad');
        $peso = request('peso');
        $precio_compra = request('precio_compra');
        $activo = request('activo');
        $precio = request('sinIVA');
        $description_short = request('resumen');
        $description = request('descripcion');

        if(!empty($_REQUEST['IVA'])){
            $iva = 1;
        }else{
            $iva = 0;
        }

        if(empty($activo)) {
            $activo = 0;
        }
        else {
            $activo = 1;
        }

        if($referencia == 1) {
            $refer = Sku::find(1);
            $referencia = $refer->referencia + 1;
            $refer->referencia = $referencia;
            $refer->save();
        }
        else {
            $refer = Sku::find(2);
            $referencia = $refer->referencia - 1;
            $refer->referencia = $referencia;
            $refer->save();
        }
        //creamos el acceso al webservices
        $xmlSchema = Prestashop::getSchema('products');

        // dd($xmlProduP);
        // dd($arrayProduP['products']['product'][0]);
        
        $datos = ['id_manufacturer'         => 0,
                  'id_supplier'             => 0,  
                  'id_category_default'     => $catg,
                  'id_default_combination'  => 0,
                  'reference'               => $referencia,
                  'additional_delivery_times'=> 1,
                  'name'                    => $nombre,
                  'minimal_quantity'        => 1,
                  'is_virtual'              => 0,
                  'price'                   => $precio,
                  'weight'                  => $peso,
                  'wholesale_price'         => $precio_compra,
                  'description_short'       => $description_short,
                  'description'             => $description,
                  'active'                  => $activo,
                  'state'                   => 1
        ];
        
        $pstXml = Prestashop::fillSchema($xmlSchema, $datos);
        
        // dd($pstXml);  
        
        $agregar = Prestashop::add(['resource' => 'products', 'postXml' => $pstXml->asXml()]);

        $id_p = $agregar->product->id;
        //set_product_quantity(35,$id,);
        // echo $id_p[0];
        $id_produ = $id_p[0];

        $xmlSchema = Prestashop::get([
            'resource' => 'stock_availables',
            'id' => $id_produ
        ]);

        $dataXmlSchema = $xmlSchema->stock_available->children();

        $dataXmlSchema->quantity = $cantidad;
        
        $editar = Prestashop::edit(['resource' => 'stock_availables', 
                                    'id' => $id_produ, 
                                    'putXml' => $xmlSchema->asXml()
        ]);
        
        Product::create([
            'id_product' => $id_produ,
            'clabe_sat' => request('clabe_sat'),
            'unidad_medida' => request('unidad_medida'),
            'iva' => $iva,
        ]);

        if(empty($_REQUEST['num_cad'])){
            $arreglo_cantidad = request('num_cad');
            $arreglo_fecha = request('fecha_cad');
            
            for($i=0; $i<count($arreglo_cantidad); $i++){
                Expiration::create([
                    'id_product' => $id_produ,
                    'quantity' => $arreglo_cantidad[$i],
                    'expiration_date' => $arreglo_fecha[$i],
                ]);
            }
        }

        return redirect('admin/productos');
       
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
        $urlProdu['resource'] = 'products/' . $id . '?display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos
        
        if(is_numeric($arrayProdu['product']['id_default_image'])){

            $id_imagen = $arrayProdu['product']['id_default_image'];

        }else{
            $id_imagen = 1;
        }

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);

        foreach($arrayStock['stock_availables']['stock_available'] as $valor) {
        
            if($id == $valor['id_product']) {
                $cantidad = $valor['quantity'];
            }

        }

        $tablaProdu[] = ['id'                     => $arrayProdu['product']['id'],
                        'name'                    => $arrayProdu['product']['name']['language'],
                        'id_image'                => $id_imagen,
                        'stock'                   => $cantidad,
                        'reference'               => $arrayProdu['product']['reference'],
                        'category'                => $arrayProdu['product']['id_category_default'], 
                        'price'                   => $arrayProdu['product']['price'],
                        'peso'                    => $arrayProdu['product']['weight'],
                        'precio_compra'           => $arrayProdu['product']['wholesale_price'],
                        'descripcion_corta'       => $arrayProdu['product']['description_short']['language'],
                        'descripcion'             => $arrayProdu['product']['description']['language'],
                        'state'                   => $arrayProdu['product']['state'],
                        'activo'                  => $arrayProdu['product']['active'],
                        'date_upd'                => $arrayProdu['product']['date_upd'],
                        ];

        $parametros = ['producto' => $tablaProdu];

        $urlCateg['resource'] = 'categories/?sort=[id_ASC]&display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);

        foreach($arrayCateg["categories"]["category"] as $categorias) {
            
            $tablaCategorias[] = ['id'    => $categorias['id'],
                                  'nombre'=> $categorias['name']['language'],];
        }

        $categorias = ['categorias' => $tablaCategorias];

        $producto = Product::where('id_product', $id)->first();

        if($producto){

        }else{
            $producto = [
                'id_product' => $id,
                'clabe_sat' => '',
                'unidad_medida' => '',
                'iva' => 0
            ];
        }

        $caducidad = Expiration::where('id_product', $id)->get();

        return view('admin.productos.edit', compact('categorias','parametros','producto','caducidad'));

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
        try{
            $nombre = request('nombre');
            $catg = request('categoria_id');
            $cantidad = request('cantidad');
            $peso = request('peso');
            $precio_compra = request('precio_compra');
            $activo = request('activo');
            $precio = request('sinIVA');
            $description_short = request('resumen');
            $description = request('descripcion');

            if(!empty($_REQUEST['IVA'])){
                $iva = 1;
            }else{
                $iva = 0;
            }
    
            if(empty($activo)) {
                $activo = 0;
            }
            else {
                $activo = 1;
            }
            
            $xmlProdu = Prestashop::get([
                'resource' => 'products',
                'id' => $id
            ]);
    
            $dataXmlProdu = $xmlProdu->children()->children();

            unset($dataXmlProdu->manufacturer_name);
            unset($dataXmlProdu->quantity);
            unset($dataXmlProdu->id_shop_default);
            unset($dataXmlProdu->id_default_image);
            unset($dataXmlProdu->associations);
            unset($dataXmlProdu->id_default_combination);
            unset($dataXmlProdu->position_in_category);
            unset($dataXmlProdu->type);
            unset($dataXmlProdu->pack_stock_type);
            unset($dataXmlProdu->date_add);
            unset($dataXmlProdu->date_upd);
            
            $dataXmlProdu->id_category_default = $catg;
            $dataXmlProdu->name = $nombre;
            $dataXmlProdu->price = $precio;
            $dataXmlProdu->weight = $peso;
            $dataXmlProdu->wholesale_price = $precio_compra;
            $dataXmlProdu->description_short = $description_short;
            $dataXmlProdu->description = $description;
            $dataXmlProdu->active = $activo;
            
            Prestashop::edit(['resource' => 'products', 
                            'id' => $id, 
                            'putXml' => $xmlProdu->asXml()
            ]);

            $xmlSchema = Prestashop::get([
                'resource' => 'stock_availables',
                'id' => $id
            ]);
    
            $dataXmlSchema = $xmlSchema->stock_available->children();
    
            $dataXmlSchema->quantity = $cantidad;
            
            Prestashop::edit(['resource' => 'stock_availables', 
                            'id' => $id, 
                            'putXml' => $xmlSchema->asXml()
            ]);
            
            $producto = Product::where('id_product', $id)->first();
            $producto->clabe_sat = request('clabe_sat');
            $producto->unidad_medida = request('unidad_medida');
            $producto->iva = $iva;
            $producto->save();
            
            if(empty($_REQUEST['num_cad'])){
                $arreglo_cantidad = request('num_cad');
                $arreglo_fecha = request('fecha_cad');
                
                for($i=0; $i<count($arreglo_cantidad); $i++){
                    Expiration::create([
                        'id_product' => $id,
                        'quantity' => $arreglo_cantidad[$i],
                        'expiration_date' => $arreglo_fecha[$i],
                    ]);
                }
            }

            return redirect()->back();

        }catch(PrestaShopWebserviceException $e) {
            echo 'Error' . $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $xmlProdu = Prestashop::get([
            'resource' => 'products',
            'id' => $id
        ]);

        $dataXmlProdu = $xmlProdu->children()->children();

        unset($dataXmlProdu->manufacturer_name);
        unset($dataXmlProdu->quantity);
        unset($dataXmlProdu->id_shop_default);
        unset($dataXmlProdu->id_default_image);
        unset($dataXmlProdu->associations);
        unset($dataXmlProdu->id_default_combination);
        unset($dataXmlProdu->position_in_category);
        unset($dataXmlProdu->type);
        unset($dataXmlProdu->pack_stock_type);
        unset($dataXmlProdu->date_add);
        unset($dataXmlProdu->date_upd);
        
        $dataXmlProdu->state = 0;
        
        Prestashop::edit(['resource' => 'products', 
                        'id' => $id, 
                        'putXml' => $xmlProdu->asXml()
        ]);

        return redirect()->back();
    }
}
