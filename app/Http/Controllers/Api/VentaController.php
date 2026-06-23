<?php

namespace sisVentas\Http\Controllers\Api;

use Illuminate\Http\Request;
use sisVentas\Http\Controllers\Controller;
use DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->get('searchText', ''));
        $estado = $request->get('estado');

        $ventas = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select(
                'v.idventa',
                'v.fecha_hora',
                'p.nombre as cliente',
                'v.tipo_comprobante',
                'v.serie_comprobante',
                'v.num_comprobante',
                'v.impuesto',
                'v.estado',
                'v.total_venta'
            )
            ->when($estado, function ($q) use ($estado) {
                return $q->where('v.estado', '=', $estado);
            })
            ->where(function ($q) use ($query) {
                $q->where('v.num_comprobante', 'LIKE', '%' . $query . '%')
                  ->orWhere('p.nombre', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('v.idventa', 'desc')
            ->groupBy(
                'v.idventa', 'v.fecha_hora', 'p.nombre',
                'v.tipo_comprobante', 'v.serie_comprobante',
                'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta'
            )
            ->get();

        return response()->json($ventas);
    }

    public function show($id)
    {
        $venta = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select(
                'v.idventa', 'v.fecha_hora', 'p.nombre as cliente',
                'v.tipo_comprobante', 'v.serie_comprobante',
                'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta'
            )
            ->where('v.idventa', '=', $id)
            ->groupBy(
                'v.idventa', 'v.fecha_hora', 'p.nombre',
                'v.tipo_comprobante', 'v.serie_comprobante',
                'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta'
            )
            ->first();

        if (!$venta) {
            return response()->json(['error' => 'Venta no encontrada'], 404);
        }

        $detalles = DB::table('detalle_venta as d')
            ->join('articulo as a', 'd.idarticulo', '=', 'a.idarticulo')
            ->select('a.nombre as articulo', 'd.cantidad', 'd.descuento', 'd.precio_venta', 'a.tiempo_entrega')
            ->where('d.idventa', '=', $id)
            ->get();

        return response()->json([
            'venta'    => $venta,
            'detalles' => $detalles,
        ]);
    }
}
