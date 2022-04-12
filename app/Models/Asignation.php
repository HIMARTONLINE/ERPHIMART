<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignation extends Model {
    protected $fillable = ['actividad_id', 'user_id'];
}