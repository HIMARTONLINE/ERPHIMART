<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {
	protected $fillable = ['producto_id', 'imagenes', 'precio_modificacion', 'stock', 'atributos', 'portada'];
}