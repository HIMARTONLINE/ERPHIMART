<?php

namespace App\Exports;

use App\Product;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Prestashop;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestaShopWebserviceException;

class ProductsExport implements FromCollection, WithHeadings
{
    public function __construct($categoria, $de_stock, $a_stock, $de_precio, $a_precio, $de_fecha, $a_fecha)
    {
        $this->categoria = $categoria;
        $this->de_stock = $de_stock;
        $this->a_stock = $a_stock;
        $this->de_precio = $de_precio;
        $this->a_precio = $a_precio;
        $this->de_fecha = $de_fecha;
        $this->a_fecha = $a_fecha;

    }
    public function headings(): array
    {
        return [
            'Nombre',
            'Stock',
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

                        if(($categ['name']['language'] == $this->categoria || $this->categoria == 1) && (($valor['quantity'] >= $this->de_stock) && ($valor['quantity'] <= $this->a_stock)) && (($value['price'] >= $this->de_precio) && ($value['price'] <= $this->a_precio)) && (($value['date_upd'] >= $this->de_fecha) && ($value['date_upd'] <= $this->a_fecha))){
                            
                            $producto = Product::where('id_product', $valor['id_product'])->first();

                            if($producto){
                                if($producto->iva == 1){
                                    $iva = (number_format($value['price'], 2) * .16) + number_format($value['price'], 2);
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

                            $tablaProdu[] = ['name'              => $value['name']['language'],
                                            'stock'              => $valor['quantity'],
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
