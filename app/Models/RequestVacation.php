<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestVacation extends Model {
    protected $fillable = ['crew_id', 'autorizacion', 'dias_solicitados', 'pendientes', 'fecha_ingreso'];
}