<?php

namespace sisVentas;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table='users';
    protected $primaryKey='id';    


    protected $fillable = [
        'name', 'email', 'password', 'idrol', 'idpersona'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function persona()
    {
        // Relacionamos idpersona de la tabla users con idpersona de la tabla persona
        return $this->belongsTo('sisVentas\Persona', 'idpersona');
    }
    
}
