<?php

namespace App\Exports;

use App\Product;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Prestashop;


class ProductsExport implements FromCollection, WithHeadings
{
    public function __construct($categoria, $de_stock, $a_stock, $venta, $de_precio, $a_precio, $de_fecha, $a_fecha)
    {
        $this->categoria = $categoria;
        $this->de_stock = $de_stock;
        $this->venta = $venta;
        $this->a_stock = $a_stock;
        $this->de_precio = $de_precio;
        $this->a_precio = $a_precio;
        $this->de_fecha = $de_fecha;
        $this->a_fecha = $a_fecha;

    }
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Stock',
            'Total Venta',
            'SKU',
            'Categoría',
            'Precio sin IVA',
            'Precio con IVA',
            'Precio de compra',
            'Clave SAT',
            'Unidad Medida'
        ];

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $urlCateg['resource'] = 'categories/?display=[id,name]';
        $xmlCateg = Prestashop::get($urlCateg);

        $urlOrder['resource'] = 'orders/?sort=[id_DESC]&display=full';
        $xmlOrder = Prestashop::get($urlOrder);

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);

        $jsonCateg = json_encode($xmlCateg);
        $arrayCateg = json_decode($jsonCateg, true);
        
        $jsonOrder = json_encode($xmlOrder);    
        $arrayOrder = json_decode($jsonOrder, true); 

        $cdad_piezas = [];
        
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

                        if(array_key_exists($value['id'], $cdad_piezas)) {
                            $id_product = $value['id'];
                            $sumaTotalPiezas = $cdad_piezas[$id_product];
                        }else {
                            $sumaTotalPiezas = 0;
                        }

                        if(($categ['name']['language'] == $this->categoria || $this->categoria == 1) && 
                           (($valor['quantity'] >= $this->de_stock) && ($valor['quantity'] <= $this->a_stock))
                           && (($value['price'] >= $this->de_precio) && ($value['price'] <= $this->a_precio))
                           && (($value['date_upd'] >= $this->de_fecha) && ($value['date_upd'] <= $this->a_fecha))){
                            
                            $producto = Product::where('id_product', $valor['id_product'])->first();
                            
                            if($producto){
                                if($producto->iva == 1){
                                    $iva = (number_format($value['price'], 2) * 0.16) + number_format($value['price'], 2);
                                }else{
                                    $iva = 0;
                                }
                                $clabe_sat = $producto->clabe_sat;
                                $unidad_medida = $producto->unidad_medida;
                            }else{
                                $iva = 0;
                                $clabe_sat = '';
                                $unidad_medida = '';
                            }

                            $tablaProdu[] = ['id'                => $value['id'],
                                            'name'               => $value['name']['language'],
                                            'stock'              => $valor['quantity'],
                                            'venta'              => $sumaTotalPiezas,
                                            'reference'          => $value['reference'],
                                            'category'           => $categ['name']['language'], 
                                            'price'              => number_format($value['price'], 2),
                                            'precio_iva'         => $iva,
                                            'wholesale_price'    => number_format($value['wholesale_price'], 2),
                                            'clabe_sat'          => $clabe_sat,
                                            'unidad_medida'      => $unidad_medida,
                            ];

                        }
                        
                    }   
                }                              
            }
        }
        $ordenarTabla = Arr::sort($tablaProdu);
        
        return collect($ordenarTabla);

    }
}
