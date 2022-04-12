<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model {
    protected $fillable = ['id_orden', 'contacto_destino', 'tracking', 'fecha', 'precio'];
}