<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	protected $fillable = ['producto', 'sku', 'codigo', 'costo', 'precio', 'precio_iva', 'satunidad_id', 'satclave_id', 'activo'];
}