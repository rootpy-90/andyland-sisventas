<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class FechaEntregaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        $fechas = DB::table('fechas_entrega')
            ->orderBy('fecha', 'asc')
            ->get();

        // Contar pedidos asignados a cada fecha (optimizado: UNA sola query con GROUP BY)
        $conteos = DB::table('venta')
            ->select('fecha_entrega', DB::raw('COUNT(*) as total_pedidos'))
            ->whereIn('estado', ['P', 'A'])
            ->whereIn('fecha_entrega', $fechas->pluck('fecha'))
            ->groupBy('fecha_entrega')
            ->pluck('total_pedidos', 'fecha_entrega');

        foreach ($fechas as $f) {
            $f->total_pedidos = $conteos->get($f->fecha, 0);
        }

        $totalActivas = $fechas->where('activo', 1)->where('fecha', '>=', date('Y-m-d'))->count();
        $totalPedidos = DB::table('venta')->whereNotNull('fecha_entrega')->whereIn('estado', ['P','A'])->count();
        $proximaFecha = $fechas->where('activo', 1)->where('fecha', '>=', date('Y-m-d'))->first();

        return view('admin.fechas_entrega.index', compact('fechas', 'totalActivas', 'totalPedidos', 'proximaFecha'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha'       => 'required|date',
            'descripcion' => 'nullable|string|max:150',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date'     => 'El formato de fecha no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->fecha < date('Y-m-d')) {
            return redirect()->back()->with('error', 'La fecha debe ser hoy o posterior.')->withInput();
        }

        $existe = DB::table('fechas_entrega')->where('fecha', $request->fecha)->exists();
        if ($existe) {
            return redirect()->back()->with('error', 'Esa fecha ya está cargada.')->withInput();
        }

        DB::table('fechas_entrega')->insert([
            'fecha'       => $request->fecha,
            'descripcion' => $request->descripcion,
            'activo'      => 1,
        ]);

        return redirect()->back()->with('status', 'Fecha agregada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fecha'       => 'required|date',
            'descripcion' => 'nullable|string|max:150',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date'     => 'El formato de fecha no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verificar que no exista otra fecha igual (distinta al registro actual)
        $existe = DB::table('fechas_entrega')
            ->where('fecha', $request->fecha)
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Ya existe otra fecha cargada para ese día.');
        }

        DB::table('fechas_entrega')->where('id', $id)->update([
            'fecha'       => $request->fecha,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->back()->with('status', 'Fecha actualizada correctamente.');
    }

    public function toggleActivo($id)
    {
        $fecha = DB::table('fechas_entrega')->where('id', $id)->first();
        DB::table('fechas_entrega')->where('id', $id)
            ->update(['activo' => $fecha->activo ? 0 : 1]);

        return redirect()->back()->with('status', 'Estado actualizado.');
    }

    public function destroy($id)
    {
        DB::table('fechas_entrega')->where('id', $id)->delete();
        return redirect()->back()->with('status', 'Fecha eliminada.');
    }

    public function informe()
    {
        $fechas = DB::table('fechas_entrega')
            ->orderBy('fecha', 'asc')
            ->get();

        // Cargar pedidos de todas las fechas en UNA sola query (evita N+1)
        $fechaList = $fechas->pluck('fecha')->toArray();
        $pedidos = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select('v.fecha_entrega', 'v.idventa', 'v.num_comprobante', 'v.total_venta',
                     'v.hora_entrega', 'v.tipo_distribucion', 'v.estado',
                     'p.nombre as cliente', 'p.telefono')
            ->whereIn('v.fecha_entrega', $fechaList)
            ->whereIn('v.estado', ['P', 'A'])
            ->orderBy('v.hora_entrega', 'asc')
            ->get()
            ->groupBy('fecha_entrega');

        foreach ($fechas as $f) {
            $f->pedidos = $pedidos->get($f->fecha, collect());
        }

        $totalConFecha  = DB::table('venta')->whereNotNull('fecha_entrega')->whereIn('estado',['P','A'])->count();
        $totalSinFecha  = DB::table('venta')->whereNull('fecha_entrega')->whereIn('estado',['P','A'])->count();

        return view('admin.fechas_entrega.informe', compact('fechas', 'totalConFecha', 'totalSinFecha'));
    }
}
