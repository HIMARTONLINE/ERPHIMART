<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use GuzzleHttp\Client;
use Prestashop;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestaShopWebserviceException;

class VentasExport implements FromCollection, WithHeadings
{
    public function __construct($de_fecha, $a_fecha)
    {
        $this->de_fecha = $de_fecha;
        $this->a_fecha = $a_fecha;

    }
    public function headings(): array
    {
        return [
            'Fecha',
            'Orden',
            'Referencia',
            'Total',
            'Descuento',
            'Envío',
            'Seguro de envío',
            'Pagado',
            'Sin IVA',
            'Compra',
            'Paquetería',
            'Seguro',
            'Comisión',
            'Utilidad'
        ];

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
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


        $urlOrder['resource'] = 'orders/?sort=[id_DESC]&display=full'; //pasamos los parametros por url de la apí
        $xmlOrder = Prestashop::get($urlOrder); //llama los parametros por GET

        $urlProdu['resource'] = 'products/?sort=[id_ASC]&display=full'; //pasamos los parametros por url de la apí
        $xmlProdu = Prestashop::get($urlProdu); //llama los parametros por GET

        $jsonOrder = json_encode($xmlOrder);    //codificamos el xml de la api en json
        $arrayOrder = json_decode($jsonOrder, true);  //decodificamos el json anterior para poder manipularlos

        $jsonProdu = json_encode($xmlProdu);    //codificamos el xml de la api en json
        $arrayProdu = json_decode($jsonProdu, true);  //decodificamos el json anterior para poder manipularlos

        foreach($arrayOrder['orders']['order'] as $i => $v) {

            if($v['current_state'] == 3 || $v['current_state'] == 5 || $v['current_state'] == 4 || $v['current_state'] == 2) {
                
                // $suma[] = floatval($v['total_paid']);
                $id_orden = $v['id'];
                $ejem[$id_orden] = $v['associations']['order_rows']['order_row'];

            }
        }

        foreach($arrayOrder['orders']['order'] as $index => $value) {

            if($value['current_state'] == 3 || $value['current_state'] == 5 || $value['current_state'] == 4 || $value['current_state'] == 2) {

                if(($value['date_add'] >= $this->de_fecha) && ($value['date_add'] <= $this->a_fecha)){

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

                    // $confirmacion = Order::where('id_order', $value['id'])->first();      

                    $sumaCompra = array_sum($sumar);

                    if($value['payment'] == 'Conekta Prestashop' || $value['payment'] == 'Conekta tarjetas de crédito'){
                        $calculo1 = ($value['total_products_wt']*2.9)/100 + 2.5;
                        $calculo2 = ($value['total_products_wt']*2.9)/100 + 2.5 * 0.16;
                        $comision = $calculo1 + $calculo2;
                    }else if($value['payment'] == 'PayPal'){
                        $calculo1 = ($value['total_products_wt']*3.95)/100 + 4.0;
                        $calculo2 = ($value['total_products_wt']*3.95)/100 + 4.0 * 0.16;
                        $comision = $calculo1 + $calculo2;
                    }else if($value['payment'] == 'Kueski Pay'){
                        $calculo1 = ($value['total_products_wt']*5.5)/100;
                        $calculo2 = ($value['total_products_wt']*5.5)/100 * 0.16;
                        $comision = $calculo1 + $calculo2;
                    }else if($value['payment'] == 'Mercado Pago'){
                        $calculo1 = ($value['total_products_wt']*3.49)/100 + 4.64;
                        $calculo2 = ($value['total_products_wt']*3.49)/100 + 4.64 * 0.16;
                        $comision = $calculo1 + $calculo2;
                    }else{
                    }

                    $utilidad = $value['total_paid_tax_excl'] - $sumaCompra - $comision - $paqueteria - $seguro;
                    

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
                                    'comision'       => $comision,
                                    'utilidad'       => number_format($utilidad, 2),
                                    // 'confirmacion'   => $value['current_state'],
                                    // 'status'         => $status,
                                    // 'productos'      => $array_produ,
                    ];

                    $sumar = [];
                    $array_produ = [];

                }
                
            }

        }

        return collect($tablaProdu);

    }
}
