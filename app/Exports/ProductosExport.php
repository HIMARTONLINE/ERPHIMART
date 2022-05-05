<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Expiration;
use Prestashop;

class ProductosExport implements FromCollection, WithHeadings
{
    public function __construct()
    {
        //

    }
    public function headings(): array
    {
        return [
            'ID',
            'Referencia',
            'Nombre',
            'Precio de venta',
            'Precio de compra',
            'Stock',
            'Fecha de expiración',
            'Fecha de inicio'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $urlProdu['resource'] = 'products/?sort=[id_DESC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $urlStock['resource'] = 'stock_availables/?display=full';
        $xmlStock = Prestashop::get($urlStock);

        $urlOrder['resource'] = 'orders/?sort=[id_DESC]&display=full'; //pasamos los parametros por url de la apí
        $xmlOrder = Prestashop::get($urlOrder); //llama los parametros por GET

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos

        $jsonStock = json_encode($xmlStock);
        $arrayStock = json_decode($jsonStock, true);

        $jsonOrder = json_encode($xmlOrder);    //codificamos el xml de la api en json
        $arrayOrder = json_decode($jsonOrder, true);  //decodificamos el json anterior para poder manipularlos

        $fechas_expiracion = Expiration::select('id_product','expiration_date')->get();

        foreach($fechas_expiracion as $row){
            $date1 = new \DateTime($row->expiration_date);
            $date2 = new \DateTime();
            $diff = $date1->diff($date2);
            
            if($diff->days <= 90){
                $products_exp[] = $row->id_product .'&'. $row->expiration_date; 
            }
        }

        foreach($arrayProdu['products']['product'] as $key => $value) {
    
            foreach($arrayStock['stock_availables']['stock_available'] as $item => $valor) {

                if($value['id'] == $valor['id_product']) {

                    foreach($products_exp as $row){
                        $id_p = $value['id'];
                        $data_exp = explode("&", $row);

                        if($id_p == $data_exp[0]){
                            if($value['id_tax_rules_group'] == 1){
                                $precio_p = $value['price'];
                                $precio_p = $precio_p * 0.16;
                            }else if($value['id_tax_rules_group'] == 2){
                                $precio_p = $value['price'];
                                $precio_p = $precio_p * 0.8;
                            }else{
                                $precio_p = $value['price'];
                            }
                            // $prueba[] = $id_p;
                            $array_produ[$id_p] = [
                                'id' => $value['id'],
                                'id_img' => $value['id_default_image'],
                                'referencia' => $value['reference'],
                                'nombre' => $value['name']['language'],
                                'precio' => $precio_p,
                                'compra' => $value['wholesale_price'],
                                'stock' => $valor['quantity'],
                                'expiracion' => $data_exp[1],
                                'fecha' => $value['date_add'],
                            ];
                        }
                    }
                    
                }   
            }                       
        }

        //dd($tablaClientes);
        $productos = $array_produ;

        return collect($productos);

    }
}
