<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductToCategory extends Model {
	protected $fillable = ['producto_id', 'categoria_id'];
}