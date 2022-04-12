<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {
    protected $fillable = ['nombre', 'icono', 'tipo', 'enlace', 'menu_id', 'orden'];
}
