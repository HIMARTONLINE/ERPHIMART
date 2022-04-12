<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model {
	public $timestamps = false;
    protected $fillable = ['users_id', 'registro', 'estatus',];
}