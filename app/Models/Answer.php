<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {
    protected $fillable = ['evaluation_id', 'tabla', 'tabla_id', 'concepto', 'valor'];
}