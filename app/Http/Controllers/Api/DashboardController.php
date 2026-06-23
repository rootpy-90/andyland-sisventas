<?php

namespace sisVentas\Http\Controllers\Api;

use Illuminate\Http\Request;
use sisVentas\Http\Controllers\Controller;
use DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $pedidosPendientes = DB::table('venta')->where('estado', '=', 'P')->count();
        $totalArticulos    = DB::table('articulo')->where('estado', '=', 'Activo')->count();
        $stockBajo         = DB::table('articulo')->where('stock', '<', 5)->count();
        $totalVentas       = DB::table('venta')->where('estado', '=', 'A')->sum('total_venta');

        return response()->json([
            'pedidos_pendientes' => $pedidosPendientes,
            'total_articulos'    => $totalArticulos,
            'stock_bajo'         => $stockBajo,
            'total_ventas'       => $totalVentas,
        ]);
    }

    public function productosMasVendidos()
    {
        $productos = DB::table('detalle_venta as dv')
            ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
            ->select('a.nombre', DB::raw('SUM(dv.cantidad) as total_vendido'))
            ->groupBy('a.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    public function ventasPorMes()
    {
        $ventas = DB::table('venta')
            ->select(
                DB::raw('YEAR(fecha_hora) as anio'),
                DB::raw('MONTH(fecha_hora) as mes'),
                DB::raw('SUM(total_venta) as total')
            )
            ->where('estado', '=', 'A')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->limit(12)
            ->get();

        return response()->json($ventas);
    }
}
