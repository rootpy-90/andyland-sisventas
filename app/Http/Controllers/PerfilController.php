<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use DB;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user    = auth()->user();
        $persona = DB::table('persona')
            ->where('idpersona', $user->idpersona)
            ->whereNull('deleted_at')
            ->first();

        // Si el usuario no tiene persona vinculada o está eliminada, crearla con datos básicos
        if (!$persona) {
            $idpersona = DB::table('persona')->insertGetId([
                'tipo_persona'    => 'Natural',
                'nombre'          => $user->name,
                'tipo_documento'  => 'DNI',
                'num_documento'   => '0000000',
                'direccion'       => '',
                'telefono'        => '',
                'email'           => $user->email,
                'deleted_at'      => null,
            ]);

            DB::table('users')->where('id', $user->id)
                ->update(['idpersona' => $idpersona]);

            $persona = DB::table('persona')->where('idpersona', $idpersona)->first();
        }

        $categoria = $this->calcularCategoria($user->idpersona ?? $persona->idpersona);
        $pedidos   = DB::table('venta')
            ->where('idcliente', $persona->idpersona)
            ->orderBy('idventa', 'desc')
            ->limit(5)
            ->get();

        return view('tienda.perfil', compact('persona', 'pedidos', 'categoria'));
    }

    public function misCompras(Request $request)
    {
        $idpersona = auth()->user()->idpersona;
        $estado    = $request->get('estado');

        // Verificar que el usuario no esté eliminado
        $personaValida = DB::table('persona')
            ->where('idpersona', $idpersona)
            ->whereNull('deleted_at')
            ->exists();

        if (!$personaValida) {
            abort(403, 'Cuenta desactivada');
        }

        $query = DB::table('venta')
            ->where('idcliente', $idpersona)
            ->orderBy('idventa', 'desc');

        if ($estado) {
            $query->where('estado', $estado);
        }

        $pedidos = $query->paginate(8);

        // Cargar detalles de todos los pedidos en UNA sola query (evita N+1)
        $pedidoIds = $pedidos->pluck('idventa')->toArray();
        $detalles = DB::table('detalle_venta as dv')
            ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
            ->select('dv.idventa', 'a.nombre as articulo', 'a.imagen', 'dv.cantidad', 'dv.precio_venta')
            ->whereIn('dv.idventa', $pedidoIds)
            ->get()
            ->groupBy('idventa');

        // Asignar detalles a cada pedido
        foreach ($pedidos as $pedido) {
            $pedido->detalles = $detalles->get($pedido->idventa, collect());
        }

        // Conteos por estado
        $totales = [
            'todos' => DB::table('venta')->where('idcliente', $idpersona)->count(),
            'P'     => DB::table('venta')->where('idcliente', $idpersona)->where('estado','P')->count(),
            'A'     => DB::table('venta')->where('idcliente', $idpersona)->where('estado','A')->count(),
            'C'     => DB::table('venta')->where('idcliente', $idpersona)->where('estado','C')->count(),
        ];

        return view('tienda.mis_compras', compact('pedidos', 'totales'));
    }

    public function cancelarCompra($id)
    {
        $venta = DB::table('venta')
            ->where('idventa', $id)
            ->where('idcliente', auth()->user()->idpersona)
            ->first();

        if (!$venta) {
            return redirect()->back()->with('error', 'Pedido no encontrado.');
        }

        if ($venta->estado !== 'P') {
            return redirect()->back()->with('error', 'Solo podés cancelar pedidos que estén pendientes.');
        }

        DB::table('venta')->where('idventa', $id)->update(['estado' => 'C']);

        return redirect()->route('mis.compras')->with('status', 'Pedido #'.$venta->num_comprobante.' cancelado correctamente.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'nullable|string|max:100',
            'telefono'  => 'required|string|max:15',
            'direccion' => 'required|string|max:200',
            'ciudad'    => 'required|string|max:100',
            'barrio'    => 'nullable|string|max:100',
            'pais'      => 'nullable|string|max:100',
            'referencia'=> 'nullable|string|max:500',
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'ciudad.required'    => 'La ciudad es obligatoria.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $idpersona = auth()->user()->idpersona;
        $nombreCompleto = trim($request->nombre . ' ' . ($request->apellido ?? ''));

        DB::table('persona')->where('idpersona', $idpersona)->update([
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido,
            'telefono'  => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad'    => $request->ciudad,
            'barrio'    => $request->barrio,
            'pais'      => $request->pais ?? 'Paraguay',
            'referencia'=> $request->referencia,
        ]);

        DB::table('users')->where('id', auth()->user()->id)->update([
            'name' => $nombreCompleto,
        ]);

        return redirect()->route('perfil')->with('status', '¡Perfil actualizado correctamente!');
    }

    public function cambiarPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_actual' => 'required',
            'password'        => 'required|string|min:6|confirmed',
        ], [
            'password_actual.required' => 'Ingresá tu contraseña actual.',
            'password.min'             => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'       => 'Las contraseñas nuevas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'password');
        }

        if (!Hash::check($request->password_actual, auth()->user()->password)) {
            return redirect()->back()
                ->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])
                ->with('tab', 'password');
        }

        DB::table('users')->where('id', auth()->user()->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('perfil')->with('status', '¡Contraseña actualizada correctamente!');
    }

    public function actualizarEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users,email,' . auth()->user()->id,
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo debe ser válido.',
            'email.unique'   => 'Este correo ya está registrado.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'email');
        }

        DB::table('users')->where('id', auth()->user()->id)->update([
            'email' => $request->email,
        ]);

        DB::table('persona')->where('idpersona', auth()->user()->idpersona)->update([
            'email' => $request->email,
        ]);

        return redirect()->route('perfil')->with('status', '¡Correo electrónico actualizado correctamente!');
    }

    public function exportarDatos()
    {
        $user = auth()->user();
        $persona = DB::table('persona')
            ->where('idpersona', $user->idpersona)
            ->whereNull('deleted_at')
            ->first();

        if (!$persona) {
            return redirect()->route('perfil')->with('error', 'No se encontraron datos para exportar.');
        }

        // Obtener historial de pedidos
        $pedidos = DB::table('venta as v')
            ->select('v.idventa', 'v.fecha_hora', 'v.num_comprobante', 'v.total_venta', 'v.estado', 'v.metodo_pago')
            ->where('v.idcliente', $persona->idpersona)
            ->orderBy('v.fecha_hora', 'desc')
            ->get();

        // Obtener detalles de cada pedido
        foreach ($pedidos as $pedido) {
            $pedido->detalles = DB::table('detalle_venta as dv')
                ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
                ->select('a.nombre as articulo', 'dv.cantidad', 'dv.precio_venta', 'dv.descuento')
                ->where('dv.idventa', $pedido->idventa)
                ->get();
        }

        // Calcular estadísticas
        $totalPedidos = $pedidos->count();
        $totalGastado = $pedidos->where('estado', 'A')->sum('total_venta');
        $categoria = $this->calcularCategoria($persona->idpersona);

        // Construir estructura de datos
        $datos = [
            'exportado_el' => now()->toIso8601String(),
            'usuario' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'fecha_registro' => $user->created_at,
                'rol' => $user->idrol == 1 ? 'Administrador' : 'Cliente',
            ],
            'datos_personales' => [
                'nombre' => $persona->nombre,
                'apellido' => $persona->apellido ?? '',
                'tipo_documento' => $persona->tipo_documento,
                'num_documento' => $persona->num_documento,
                'telefono' => $persona->telefono,
                'email' => $persona->email,
                'direccion' => $persona->direccion,
                'ciudad' => $persona->ciudad,
                'barrio' => $persona->barrio,
                'pais' => $persona->pais,
                'referencia' => $persona->referencia,
            ],
            'categoria_cliente' => [
                'label' => $categoria['label'],
                'color' => $categoria['color'],
            ],
            'estadisticas' => [
                'total_pedidos' => $totalPedidos,
                'total_gastado' => (float) $totalGastado,
                'promedio_por_pedido' => $totalPedidos > 0 ? (float) ($totalGastado / $totalPedidos) : 0,
            ],
            'historial_pedidos' => $pedidos->map(function($p) {
                return [
                    'numero_comprobante' => $p->num_comprobante,
                    'fecha' => $p->fecha_hora,
                    'estado' => $p->estado == 'P' ? 'Pendiente' : ($p->estado == 'A' ? 'Aceptado' : 'Cancelado'),
                    'metodo_pago' => $p->metodo_pago ?? 'Efectivo',
                    'total' => (float) $p->total_venta,
                    'articulos' => $p->detalles->map(function($d) {
                        return [
                            'nombre' => $d->articulo,
                            'cantidad' => (int) $d->cantidad,
                            'precio_unitario' => (float) $d->precio_venta,
                            'descuento' => (float) $d->descuento,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];

        // Generar nombre de archivo
        $fecha = date('Y-m-d');
        $nombreArchivo = "mis_datos_{$fecha}.json";

        // Retornar como descarga
        return response()->json($datos, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"')
            ->header('Content-Type', 'application/json');
    }

    public function subirComprobante(Request $request, $id)
    {
        $venta = DB::table('venta')
            ->where('idventa', $id)
            ->where('idcliente', auth()->user()->idpersona)
            ->first();

        if (!$venta) {
            return redirect()->back()->with('error', 'Pedido no encontrado.');
        }

        if (!Input::hasFile('comprobante_archivo')) {
            return redirect()->back()->with('error', 'Seleccioná un archivo para subir.');
        }

        $file = Input::file('comprobante_archivo');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
            return redirect()->back()->with('error', 'Solo se permiten archivos JPG, PNG o PDF.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'El archivo no puede superar los 5 MB.');
        }

        // Eliminar archivo anterior si existe
        if ($venta->comprobante_pago) {
            $anterior = public_path('comprobantes/' . $venta->comprobante_pago);
            if (file_exists($anterior)) unlink($anterior);
        }

        $nombre = 'comp_' . $id . '_' . time() . '.' . $ext;
        $file->move(public_path('comprobantes'), $nombre);

        DB::table('venta')->where('idventa', $id)->update([
            'comprobante_pago' => $nombre,
        ]);

        return redirect()->back()->with('status', '¡Comprobante subido correctamente!');
    }

    public function eliminarCuenta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_confirmar' => 'required',
        ], [
            'password_confirmar.required' => 'Ingresá tu contraseña para confirmar.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('tab', 'eliminar');
        }

        // Verificar contraseña antes de eliminar
        if (!Hash::check($request->password_confirmar, auth()->user()->password)) {
            return redirect()->back()
                ->withErrors(['password_confirmar' => 'La contraseña no es correcta.'])
                ->with('tab', 'eliminar');
        }

        $idpersona = auth()->user()->idpersona;
        $iduser    = auth()->user()->id;

        // Cerrar sesión antes de eliminar
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Soft delete: marcar como eliminado (no borra datos físicos)
        $now = now();
        DB::table('persona')->where('idpersona', $idpersona)->update(['deleted_at' => $now]);
        DB::table('users')->where('id', $iduser)->update(['deleted_at' => $now]);

        return redirect('/tienda')
            ->with('status', 'Tu cuenta fue desactivada. Podés recuperarla contactándonos dentro de los 30 días.');
    }

    private function calcularCategoria($idpersona)
    {
        $totalPedidos = DB::table('venta')
            ->where('idcliente', $idpersona)
            ->where('estado', '!=', 'C')
            ->count();

        $totalGastado = DB::table('venta')
            ->where('idcliente', $idpersona)
            ->where('estado', 'A')
            ->sum('total_venta');

        if ($totalPedidos === 0) {
            return ['label' => 'Cliente Nueva',    'color' => '#aaa',    'icon' => 'fa-star-o'];
        } elseif ($totalGastado >= 500000 || $totalPedidos >= 10) {
            return ['label' => 'Cliente VIP',       'color' => '#f39c12', 'icon' => 'fa-star'];
        } elseif ($totalPedidos >= 4) {
            return ['label' => 'Cliente Frecuente', 'color' => '#27ae60', 'icon' => 'fa-heart'];
        } else {
            return ['label' => 'Cliente Regular',   'color' => '#e91e8c', 'icon' => 'fa-shopping-bag'];
        }
    }
}
