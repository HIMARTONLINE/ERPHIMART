<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Prestashop;

class ClientesController extends Controller
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
        //
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
                                            //'id_orden'   => $k,
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
        $parametros = ['parametros'     => $tablaClientes,];
        //dd($parametros);
        return view('admin.clientes.index', compact('parametros'));
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
}
