<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestPermisions extends Model {
    protected $fillable = ['crew_id', 'autorizacion', 'dia_hora', 'dias_solicitados', 'horas_solicitadas', 'motivo', 'fecha_ingreso', 'hora_ingreso', 'created_at'];
}