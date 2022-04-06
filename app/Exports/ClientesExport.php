<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Prestashop;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use Protechstudio\PrestashopWebService\PrestaShopWebserviceException;

class ClientesExport implements FromCollection, WithHeadings
{
    public function __construct()
    {
        //

    }
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellido',
            'Correo electrónico',
            'Fecha de cumpleaños',
            'No. de Pedidos',
            'Total cantidad pagado'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $urlClientes['resource'] = '/customers/?display=full';
        $xmlClientes = Prestashop::get($urlClientes);

        $urlOrdenes['resource'] = '/orders/?display=full';
        $xmlOrdenes = Prestashop::get($urlOrdenes);

        $jsonClientes = json_encode($xmlClientes);
        $arrayClientes = json_decode($jsonClientes, true);

        $jsonOrdenes = json_encode($xmlOrdenes);
        $arrayOrdenes = json_decode($jsonOrdenes, true);

        foreach($arrayClientes['customers']['customer'] as $index => $value) {

            foreach($arrayOrdenes['orders']['order'] as $key => $valor) {
                
                if($value['id'] == $valor['id_customer'] && $valor['current_state'] != "6") {
                    
                    $numeroPedido[] = $valor['id_customer'];
                    $suma[] = ['id'   => $valor['id_customer'], 'total_paid'  => $valor['total_paid'],];
                    $totalPedidos = array_count_values($numeroPedido);
                    
                }
            }
        }
        
        $ids_products = [];
        foreach($suma as $arre_product) {
            $id_product = $arre_product['id'];
            if(! in_array($id_product, $ids_products)) {
                $ids_products[] = $id_product;
            }
        }
        $result = [];
        foreach($ids_products as $unique_id) {
            $temp = [];
            $quantity = 0;
            foreach($suma as $arre_product) {
                $id = $arre_product['id'];

                if($id === $unique_id) {
                    $temp[] = $arre_product;
                }
            }

            $product = $temp[0];

            $product['total_paid'] = 0;
            foreach($temp as $product_temp) {
                $product['total_paid'] = $product['total_paid'] + $product_temp['total_paid'];
            }

            $result[] = $product;
        }
        
        foreach($arrayClientes['customers']['customer'] as $in => $val) {

            foreach($result as $idProduct) {

                foreach($totalPedidos as $k => $v) {

                    if($k == $val['id'] && $k == $idProduct['id']) {
                        
                        $tablaClientes[] = ['id'         => $val['id'],
                                            'firstname'  => $val['firstname'],
                                            'lastname'   => $val['lastname'],
                                            'email'      => $val['email'],
                                            'birthday'   => $val['birthday'],
                                            'quantity'   => $v,
                                            'total_paid' => $idProduct['total_paid'],
                                        ];
                    }   
                    
                }
            }
        }

        //dd($tablaClientes);
        $clientes = ['parametros' => $tablaClientes];

        return collect($clientes);

    }
}
