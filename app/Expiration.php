<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expiration extends Model
{
    public $fillable = ['id_product','quantity','expiration_date'];

}
