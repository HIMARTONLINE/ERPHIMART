<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savepassword extends Model
{
    // Table contrasenia
    protected $table = "contrasenias";

    protected $fillable = [
        'empresa',
        'servicio',
        'estado',
        'enlace',
        'usuario',
        'clave'
    ];
}

/*
CREATE TABLE contrasenias (
	id int   NOT NULL AUTO_INCREMENT,
    empresa  VARCHAR(255),
    servicio VARCHAR(255),
    estado   BOOLEAN,
    enlace   VARCHAR(255),
    usuario  VARCHAR(255),
    clave    VARCHAR(255),
    PRIMARY  KEY(id)
); */
