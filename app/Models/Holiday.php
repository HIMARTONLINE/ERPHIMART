<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model {
    protected $fillable = ['festividad', 'fecha_descanso', 'fecha_conmemorativa'];
}