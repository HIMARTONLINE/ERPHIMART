<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Configuration;
//use App\Models\Permision;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $configuracion = [];

    public function __construct() {
    	
        $this->getConfiguracion();
    }

    public function getConfiguracion() {
        $data = Configuration::all();
        foreach ($data as $key => $value) {
            $this->configuracion[$value->campo] = $value->valor;
        }
    }
}
