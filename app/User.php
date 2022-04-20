<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Crew;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'permision_id', 'area_id', 'serial', 'clave', 'foto', 'name', 'email', 'password', 'idioma',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function role(){
        return $this->belongsTo('App\Role');
    }
    
    public function Admin(){
        if($this->role->tipo_usuario == 'Administrador'){
            return true;
        }
        return false;
    }
    
    public function Colaborador(){
        if($this->role->tipo_usuario == 'Colaborador'){
            return true;
        }
        return false;
    }
    
    public function crew() {
        return $this->hasOne(Crew::class,'user_id','id');
      }
    
}
