<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index()
    {
        // Caja del día actual abierta
        $cajaAbierta = DB::table('caja')
            ->where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->first();

        // Historial de cajas anteriores (cerradas)
        $historial = DB::table('caja')
            ->where('estado', 'cerrada')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Si hay caja abierta: cargar movimientos y ventas del día
        $movimientos    = collect();
        $ventasDelDia   = collect();
        $totalesDia     = ['efectivo' => 0, 'transferencia' => 0, 'tarjeta' => 0, 'total' => 0];

        if ($cajaAbierta) {
            $movimientos = DB::table('arqueo_caja')
                ->where('caja_id', $cajaAbierta->id)
                ->orderBy('id', 'asc')
                ->get();

            $ventasDelDia = DB::table('venta as v')
                ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
                ->select('v.idventa', 'v.num_comprobante', 'v.total_venta', 'v.metodo_pago',
                         'v.fecha_hora', 'v.estado', 'p.nombre as cliente')
                ->whereDate('v.fecha_hora', $cajaAbierta->fecha_apertura)
                ->whereIn('v.estado', ['P', 'A'])
                ->orderBy('v.idventa', 'desc')
                ->get();

            foreach ($ventasDelDia as $v) {
                $m = strtolower($v->metodo_pago ?? 'efectivo');
                if (str_contains($m, 'transfer')) {
                    $totalesDia['transferencia'] += $v->total_venta;
                } elseif (str_contains($m, 'tarjeta') || str_contains($m, 'visa') || str_contains($m, 'master')) {
                    $totalesDia['tarjeta'] += $v->total_venta;
                } else {
                    $totalesDia['efectivo'] += $v->total_venta;
                }
                $totalesDia['total'] += $v->total_venta;
            }
        }

        return view('admin.caja.index', compact(
            'cajaAbierta', 'historial', 'movimientos', 'ventasDelDia', 'totalesDia'
        ));
    }

    public function abrir(Request $request)
    {
        // Verificar que no haya caja abierta
        $existe = DB::table('caja')->where('estado', 'abierta')->exists();
        if ($existe) {
            return redirect()->back()->with('error', 'Ya hay una caja abierta. Cerrala primero.');
        }

        $validator = Validator::make($request->all(), [
            'monto_inicial' => 'required|numeric|min:0',
        ], ['monto_inicial.required' => 'El monto inicial es obligatorio.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('caja')->insert([
            'fecha_apertura' => date('Y-m-d'),
            'hora_apertura'  => date('H:i:s'),
            'monto_inicial'  => $request->monto_inicial,
            'observacion'    => $request->observacion,
            'estado'         => 'abierta',
        ]);

        return redirect()->back()->with('status', 'Caja abierta correctamente con ' . number_format($request->monto_inicial, 0, ',', '.') . ' Gs.');
    }

    public function cerrar(Request $request, $id)
    {
        $caja = DB::table('caja')->where('id', $id)->where('estado', 'abierta')->first();
        if (!$caja) {
            return redirect()->back()->with('error', 'Caja no encontrada o ya cerrada.');
        }

        $validator = Validator::make($request->all(), [
            'monto_final' => 'required|numeric|min:0',
        ], ['monto_final.required' => 'El monto final es obligatorio.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('caja')->where('id', $id)->update([
            'hora_cierre'     => date('H:i:s'),
            'fecha_cierre'    => date('Y-m-d H:i:s'),
            'monto_final'     => $request->monto_final,
            'observacion_cierre' => $request->observacion_cierre,
            'estado'          => 'cerrada',
        ]);

        return redirect()->back()->with('status', 'Caja cerrada correctamente.');
    }

    public function addMovimiento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id'     => 'required|integer',
            'tipo'        => 'required|in:ingreso,egreso',
            'descripcion' => 'required|string|max:200',
            'monto'       => 'required|numeric|min:1',
            'metodo'      => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('arqueo_caja')->insert([
            'caja_id'     => $request->caja_id,
            'tipo'        => $request->tipo,
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'metodo'      => $request->metodo ?? 'Efectivo',
            'created_at'  => now(),
        ]);

        $tipo = $request->tipo === 'ingreso' ? 'Ingreso' : 'Egreso';
        return redirect()->back()->with('status', "$tipo registrado correctamente.");
    }

    public function deleteMovimiento($id)
    {
        DB::table('arqueo_caja')->where('id', $id)->delete();
        return redirect()->back()->with('status', 'Movimiento eliminado.');
    }

    public function imprimir($id)
    {
        $caja = DB::table('caja')->where('id', $id)->first();
        if (!$caja) abort(404);

        $movimientos = DB::table('arqueo_caja')
            ->where('caja_id', $id)
            ->orderBy('id', 'asc')
            ->get();

        $ventasDelDia = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select('v.idventa', 'v.num_comprobante', 'v.total_venta', 'v.metodo_pago',
                     'v.fecha_hora', 'v.estado', 'p.nombre as cliente')
            ->whereDate('v.fecha_hora', $caja->fecha_apertura)
            ->whereIn('v.estado', ['P', 'A'])
            ->orderBy('v.idventa', 'asc')
            ->get();

        $totales = ['efectivo' => 0, 'transferencia' => 0, 'tarjeta' => 0, 'total' => 0];
        foreach ($ventasDelDia as $v) {
            $m = strtolower($v->metodo_pago ?? 'efectivo');
            if (str_contains($m, 'transfer'))     $totales['transferencia'] += $v->total_venta;
            elseif (str_contains($m, 'tarjeta') || str_contains($m, 'visa')) $totales['tarjeta'] += $v->total_venta;
            else                                   $totales['efectivo']     += $v->total_venta;
            $totales['total'] += $v->total_venta;
        }

        $ingresosArqueo = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresosArqueo  = $movimientos->where('tipo', 'egreso')->sum('monto');

        return view('admin.caja.imprimir', compact(
            'caja', 'movimientos', 'ventasDelDia', 'totales', 'ingresosArqueo', 'egresosArqueo'
        ));
    }
}
