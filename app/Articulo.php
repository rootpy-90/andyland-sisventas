<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table='articulo';

    protected $primaryKey='idarticulo';

    public $timestamps=false;


    protected $fillable =[
    	'idcategoria',
    	'codigo',
    	'nombre',
    	'stock',
        'precio',
    	'descripcion',
        'tiempo_entrega',
    	'imagen',
    	'estado'
    ];

    protected $guarded =[

    ];

    public function categoria()
    {
        return $this->belongsTo('sisVentas\Categoria', 'idcategoria');
    }

    public function detallesVenta()
    {
        return $this->hasMany('sisVentas\DetalleVenta', 'idarticulo');
    }

    public function detallesIngreso()
    {
        return $this->hasMany('sisVentas\DetalleIngreso', 'idarticulo');
    }
}
