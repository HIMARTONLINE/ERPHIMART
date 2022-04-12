<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model {
    protected $fillable = ['stock_id', 'cantidad', 'fecha'];
}