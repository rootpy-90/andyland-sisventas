<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rol = DB::table('roles')->where('idrol', auth()->user()->idrol)->first();
        if (!$rol || !$rol->es_admin) {
            return redirect()->route('tienda');
        }

        $nPendientes  = DB::table('venta')->where('estado', 'P')->count();
        $nAprobados   = DB::table('venta')->where('estado', 'A')->count();
        $nArticulos   = DB::table('articulo')->where('estado', 'Activo')->count();
        $nStockBajo   = DB::table('articulo')->where('stock', '<', 5)->count();
        $nClientes    = DB::table('persona')->where('tipo_persona', 'Cliente')->count();
        $totalVentas  = DB::table('venta')->where('estado', 'A')->sum('total_venta');
        $pedidosMes   = DB::table('venta')
            ->whereMonth('fecha_hora', date('m'))
            ->whereYear('fecha_hora', date('Y'))
            ->count();

        $ventasMes    = DB::table('venta')
            ->whereMonth('fecha_hora', date('m'))
            ->whereYear('fecha_hora', date('Y'))
            ->where('estado', 'A')
            ->sum('total_venta');

        $ventasHoy    = DB::table('venta')
            ->whereDate('fecha_hora', date('Y-m-d'))
            ->count();

        $ultimosPendientes = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select('v.idventa', 'v.num_comprobante', 'v.total_venta', 'v.fecha_hora',
                     'v.metodo_pago', 'v.comprobante_pago', 'v.tipo_distribucion', 'p.nombre as cliente')
            ->where('v.estado', 'P')
            ->orderBy('v.idventa', 'desc')
            ->limit(8)
            ->get();

        $articulosStockBajo = DB::table('articulo')
            ->select('nombre', 'stock')
            ->where('stock', '<', 5)
            ->where('estado', 'Activo')
            ->orderBy('stock', 'asc')
            ->limit(6)
            ->get();

        // Top 5 productos más vendidos (para el gráfico inline)
        $topProductos = DB::table('detalle_venta as dv')
            ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
            ->select('a.nombre', DB::raw('SUM(dv.cantidad) as total_vendido'))
            ->groupBy('a.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(6)
            ->get();

        // Ventas por mes — últimos 6 meses
        $ventasPorMes = DB::table('venta')
            ->select(
                DB::raw('YEAR(fecha_hora) as anio'),
                DB::raw('MONTH(fecha_hora) as mes'),
                DB::raw('SUM(total_venta) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('estado', 'A')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->limit(6)
            ->get();

        return view('home', compact(
            'nPendientes', 'nAprobados', 'nArticulos', 'nStockBajo',
            'nClientes', 'totalVentas', 'pedidosMes', 'ventasMes', 'ventasHoy',
            'ultimosPendientes', 'articulosStockBajo', 'topProductos', 'ventasPorMes'
        ));
    }
}
