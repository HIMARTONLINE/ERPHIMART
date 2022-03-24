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
                    $totalPedidos = array_count_values($numeroPedido);
                }
            }
        }

        foreach($arrayClientes['customers']['customer'] as $in => $val) {

            foreach($totalPedidos as $k => $v) {

                if($k == $val['id']) {
                    
                    $tablaClientes[] = ['id'         => $val['id'],
                                        //'id_orden'   => $k,
                                        'firstname'  => $val['firstname'],
                                        'lastname'   => $val['lastname'],
                                        'email'      => $val['email'],
                                        'birthday'   => $val['birthday'],
                                        'quantity'   => $v,
                                    ];
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
