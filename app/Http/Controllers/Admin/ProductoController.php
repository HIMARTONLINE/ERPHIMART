<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
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
    public function index()
    {

        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $urlCateg['resource'] = 'categories/?display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);

        foreach($arrayCateg["categories"]["category"] as $index => $categ) {
        
            foreach($arrayProdu['products']['product'] as $key => $value) {

                foreach($arrayStock['stock_availables']['stock_available'] as $item => $valor) {

                    if($value['id'] == $valor['id_product'] && $value['id_category_default'] == $categ['id']) {

                        $tablaProdu[] = ['id'         => $value['id'],
                                        'name'        => $value['name']['language'],
                                        'stock'       => $valor['quantity'],
                                        'reference'   => $value['reference'],
                                        'category'    => $categ['name']['language'], 
                                        'price'       => $value['price'],
                                        'state'       => $value['state'],
                                        'activo'      => $value['active'],
                                        'date_upd'    => $value['date_upd'],
                                        ];
                    }   
                }                              
            }
        }
        $ordenarTabla = Arr::sort($tablaProdu);
        //pasamos los parametros a otro arreglo para poder usarlos en el Front
        $parametros = ['productos' => $ordenarTabla,];

         //dd($xmlProdu);

        return view('admin.productos.index', compact('parametros'));

        
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
        $referencia = request('codigo');
        $catg = request('categoria_id');
        $cantidad = request('cantidad');
        $activo = request('activo');
        $precio = request('sinIVA');
        $description_short = request('resumen');
        $description = request('descripcion');


        if($activo == null) {
            $activo = 0;
        }
        else {
            $activo = 1;
        }

        //creamos el acceso al webservices
        $xmlSchema = Prestashop::getSchema('products');
        
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
        
        Product::create([
            'id_product' => $id_produ,
            'clabe_sat' => request('clabe_sat'),
            'unidad_medida' => request('unidad_medida'),
        ]);
        
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
        /*try{

            $urlProdu['resource'] = 'products/'+$id+'?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
            $xmlProdu = Prestashop::get($urlProdu);
            

        }catch(PrestaShopWebserviceException $e) {
            echo 'Error' . $e->getMessage();
        }*/
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
