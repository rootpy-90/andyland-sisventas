<?php
// ESTE ES MI CONTROLADOR DE BASE-VISTA
namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;


use sisVentas\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use DB;

class RegistroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
      if ($request)
      {
        $registros = DB::table('detalle_venta as dv')
        ->join('articulo as a', 'dv.idarticulo','=','a.idarticulo')
        ->select('a.nombre',DB::raw('SUM(cantidad) as total_product'),DB::raw('count(cantidad) as cont_product'))
        ->groupBy('a.nombre')
        ->orderBy('total_product', 'desc')
        ->paginate(7);
        return view('registros.registro.index',["registros"=>$registros]);
      }
    }  
}



