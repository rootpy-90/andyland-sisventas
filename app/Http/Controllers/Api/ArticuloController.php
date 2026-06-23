<?php

namespace sisVentas\Http\Controllers\Api;

use Illuminate\Http\Request;
use sisVentas\Http\Controllers\Controller;
use sisVentas\Articulo;
use DB;

class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->get('searchText', ''));

        $articulos = DB::table('articulo as a')
            ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
            ->select('a.idarticulo', 'a.nombre', 'a.codigo', 'a.stock', 'c.nombre as categoria', 'a.descripcion', 'a.imagen', 'a.estado', 'a.tiempo_entrega')
            ->where('a.estado', '=', 'Activo')
            ->where(function ($q) use ($query) {
                $q->where('a.nombre', 'LIKE', '%' . $query . '%')
                  ->orWhere('a.codigo', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('a.idarticulo', 'desc')
            ->get();

        return response()->json($articulos);
    }

    public function show($id)
    {
        $articulo = DB::table('articulo as a')
            ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
            ->select('a.idarticulo', 'a.nombre', 'a.codigo', 'a.stock', 'c.nombre as categoria', 'a.descripcion', 'a.imagen', 'a.estado', 'a.tiempo_entrega')
            ->where('a.idarticulo', '=', $id)
            ->first();

        if (!$articulo) {
            return response()->json(['error' => 'Artículo no encontrado'], 404);
        }

        return response()->json($articulo);
    }

    public function stockBajo()
    {
        $articulos = DB::table('articulo as a')
            ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
            ->select('a.idarticulo', 'a.nombre', 'a.codigo', 'a.stock', 'c.nombre as categoria')
            ->where('a.stock', '<', 5)
            ->where('a.estado', '=', 'Activo')
            ->orderBy('a.stock', 'asc')
            ->get();

        return response()->json($articulos);
    }
}
