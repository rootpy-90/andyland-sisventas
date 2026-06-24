<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
     protected $table='persona';

    protected $primaryKey='idpersona';

    public $timestamps=false;


    protected $fillable =[
        'tipo_persona',
        'nombre',
        'apellido',
        'tipo_documento',
        'num_documento',
        'direccion',
        'telefono',
        'email',
        'pais',
        'ciudad',
        'barrio',
        'referencia',
    ];

    protected $guarded =[

    ];

    public function user()
    {
        return $this->hasOne('sisVentas\User', 'idpersona');
    }

    public function ventas()
    {
        return $this->hasMany('sisVentas\Venta', 'idcliente');
    }

    public function ingresos()
    {
        return $this->hasMany('sisVentas\Ingreso', 'idproveedor');
    }
}
