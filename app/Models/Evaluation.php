<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model {
    protected $fillable = ['evaluador_id', 'user_id', 'apromedio', 'bpromedio', 'acomentario', 'bcomentario', 'rcomentario'];
}