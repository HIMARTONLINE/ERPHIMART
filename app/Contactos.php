<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contactos extends Model
{
    public $fillable = ['id_usuario','nombre','parentesco','telefono'];

}
