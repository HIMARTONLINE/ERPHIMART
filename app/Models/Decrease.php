<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Decrease extends Model {
	protected $fillable = ['id', 'cantidad', 'motivo', 'recuperable', 'stock_id'];
}