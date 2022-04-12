<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Permision extends Model {
	protected $fillable = ['rol', 'permisos',];
}