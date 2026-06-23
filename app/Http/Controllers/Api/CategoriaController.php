<?php

namespace sisVentas\Http\Controllers\Api;

use Illuminate\Http\Request;
use sisVentas\Http\Controllers\Controller;
use DB;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = DB::table('categoria')
            ->where('condicion', '=', '1')
            ->orderBy('idcategoria', 'desc')
            ->get();

        return response()->json($categorias);
    }
}
