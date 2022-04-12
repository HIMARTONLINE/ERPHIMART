<?php

namespace App\Models;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model {
    protected $fillable = ['user_id', 'area_id', 'nombres', 'apellidos', 'foto', 'genero', 'nacimiento', 'direccion', 'municipio', 'estado', 'movil', 'correo', 'contactos', 'ingreso', 'nss', 'curp', 'rfc', 'infonavit', 'cuenta', 'activo'];



    public function user()
    {
    return $this->belongsTo(User::class);
    }

}