<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    protected $table='detalle_ingreso';

    protected $primaryKey='iddetalle_ingreso';

    public $timestamps=false;


    protected $fillable =[
    	'idingreso',
    	'idarticulo',
    	'cantidad',
    	'precio_compra',
    	'precio_venta'
    ];

    protected $guarded =[

    ];

    public function ingreso()
    {
        return $this->belongsTo('sisVentas\Ingreso', 'idingreso');
    }

    public function articulo()
    {
        return $this->belongsTo('sisVentas\Articulo', 'idarticulo');
    }
}
