<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model {
    protected $fillable = ['clave_sat', 'c.u', 'unidad', 'sku'];

    
}