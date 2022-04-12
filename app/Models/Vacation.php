<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model {
    protected $fillable = ['crew_id', 'servicio', 'correspondientes', 'pendientes', 'tomados'];
}