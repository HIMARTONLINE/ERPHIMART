<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $fillable = ['id_product','clabe_sat','unidad_medida','merma'];

}
