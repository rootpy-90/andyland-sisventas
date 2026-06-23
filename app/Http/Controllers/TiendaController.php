<?php

namespace sisVentas\Http\Controllers;

use sisVentas\Venta;
use sisVentas\DetalleVenta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class TiendaController extends Controller
{
    public function __construct()
    {
        // Solo checkout y pedido requieren login; navegar es público
        $this->middleware('auth')->only(['checkout', 'store', 'completarPerfil']);
    }

    public function index(Request $request)
    {
        $query    = trim($request->get('searchText', ''));
        $idcat    = $request->get('idcat');
        $categorias = DB::table('categoria')->where('condicion', '1')->get();

        $articulos = DB::table('articulo as a')
            ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
            ->select(
                'a.idarticulo', 'a.nombre', 'a.codigo', 'a.stock',
                'c.nombre as categoria', 'a.descripcion', 'a.imagen',
                'a.tiempo_entrega', 'a.idcategoria', 'a.precio'
            )
            ->where('a.estado', 'Activo');

        if ($idcat && $idcat !== 'todos') {
            $articulos->where('a.idcategoria', $idcat);
        }
        if ($query) {
            $articulos->where('a.nombre', 'LIKE', "%{$query}%");
        }

        $orden = $request->get('orden', 'default');
        switch ($orden) {
            case 'precio_asc':  $articulos->orderBy('a.precio', 'asc');  break;
            case 'precio_desc': $articulos->orderBy('a.precio', 'desc'); break;
            case 'nombre_asc':  $articulos->orderBy('a.nombre', 'asc');  break;
            case 'stock_desc':  $articulos->orderBy('a.stock', 'desc');  break;
            default:            $articulos->orderBy('a.idarticulo', 'desc'); break;
        }

        $articulos = $articulos->paginate(12);

        return view('tienda.index', [
            'articulos'       => $articulos,
            'searchText'      => $query,
            'categorias'      => $categorias,
            'categoriaActual' => $idcat,
        ]);
    }

    public function show($id)
    {
        $articulo = DB::table('articulo as a')
            ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
            ->select(
                'a.idarticulo', 'a.nombre', 'a.codigo', 'a.stock',
                'c.nombre as categoria', 'a.descripcion', 'a.imagen',
                'a.tiempo_entrega', 'a.precio'
            )
            ->where('a.idarticulo', $id)
            ->first();

        if (!$articulo) abort(404);

        return view('tienda.show', ['articulo' => $articulo]);
    }

    public function comprobantePago($id)
    {
        $venta = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select('v.*', 'p.nombre as cliente_nombre', 'p.apellido as cliente_apellido',
                     'p.num_documento', 'p.telefono', 'p.email as cliente_email')
            ->where('v.idventa', $id)
            ->where('v.idcliente', auth()->user()->idpersona)
            ->first();

        if (!$venta) abort(404);

        $detalles = DB::table('detalle_venta as dv')
            ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
            ->select('a.nombre as articulo', 'a.imagen', 'dv.cantidad', 'dv.precio_venta', 'dv.descuento')
            ->where('dv.idventa', $id)
            ->get();

        return view('tienda.comprobante_pago', compact('venta', 'detalles'));
    }

    public function checkout()
    {
        if (auth()->user()->idrol == 1) {
            return redirect('home');
        }

        $fechas = DB::table('fechas_entrega')
            ->where('activo', 1)
            ->where('fecha', '>=', date('Y-m-d'))
            ->orderBy('fecha', 'asc')
            ->get();

        // Mapa id → tiempo_entrega para mostrar en el checkout
        $tiemposEntrega = DB::table('articulo')
            ->select('idarticulo', 'nombre', 'tiempo_entrega', 'precio')
            ->where('estado', 'Activo')
            ->get()
            ->keyBy('idarticulo');

        return view('tienda.checkout', compact('fechas', 'tiemposEntrega'));
    }

    public function completarPerfil(Request $request)
    {
        DB::table('persona')
            ->where('idpersona', auth()->user()->idpersona)
            ->update([
                'num_documento'  => $request->get('num_documento'),
                'telefono'       => $request->get('telefono'),
                'direccion'      => $request->get('direccion'),
                'tipo_documento' => 'CI',
            ]);

        return redirect()->route('tienda.checkout')
            ->with('status', '¡Datos guardados! Ya podés confirmar tu pedido.');
    }

    public function store(Request $request)
    {
        $cartJson = $request->get('cart_json', '[]');
        $items    = json_decode($cartJson, true);

        if (empty($items)) {
            return redirect('tienda')->with('error', 'El carrito está vacío.');
        }

        $fechaEntrega  = $request->get('fecha_entrega') ?: null;
        $hayFechas = DB::table('fechas_entrega')
            ->where('activo', 1)
            ->where('fecha', '>=', date('Y-m-d'))
            ->exists();

        if ($hayFechas && !$fechaEntrega) {
            return redirect('tienda/checkout')->with('error', 'Debés seleccionar una fecha de entrega.');
        }

        // Validar stock disponible antes de procesar
        foreach ($items as $item) {
            $id  = (int) ($item['id'] ?? 0);
            $qty = (int) ($item['qty'] ?? 1);
            $stockDisponible = DB::table('articulo')->where('idarticulo', $id)->value('stock');
            if ($stockDisponible !== null && $stockDisponible < $qty) {
                $nombre = DB::table('articulo')->where('idarticulo', $id)->value('nombre');
                return redirect('tienda/checkout')
                    ->with('error', "Stock insuficiente para \"$nombre\". Disponible: $stockDisponible unidad(es).");
            }
        }

        try {
            DB::beginTransaction();

            $tipoDistrib = $request->get('tipo_distribucion', 'Delivery');

            $venta = new Venta;
            $venta->idcliente         = auth()->user()->idpersona;
            $venta->tipo_comprobante  = $request->get('tipo_facturacion', 'Ticket');
            $venta->serie_comprobante = 'WEB';
            $venta->num_comprobante   = time();
            $venta->total_venta       = 0;
            $venta->fecha_hora        = Carbon::now('America/Asuncion')->toDateTimeString();
            $venta->fecha_entrega     = $fechaEntrega;
            $venta->metodo_pago       = $request->get('metodo_pago', 'Efectivo en tienda');
            $venta->tipo_distribucion = $tipoDistrib;
            $venta->direccion_envio   = $tipoDistrib === 'Delivery'
                ? ($request->get('direccion_envio') ?: auth()->user()->persona->direccion)
                : 'Retiro en tienda';
            $venta->hora_entrega      = $request->get('hora_entrega', 'A coordinar');
            $venta->num_transaccion   = $request->get('num_transaccion') ?: null;
            $venta->tipo_facturacion  = $request->get('tipo_facturacion', 'Ticket');
            $venta->impuesto          = '5';
            $venta->estado            = 'P';
            $venta->save();

            // Guardar comprobante de pago si fue adjuntado
            if ($request->hasFile('comprobante_archivo')) {
                $file = $request->file('comprobante_archivo');
                $ext  = strtolower($file->getClientOriginalExtension());
                if (in_array($ext, ['jpg','jpeg','png','pdf'])) {
                    $nombre = 'comp_' . $venta->idventa . '_' . time() . '.' . $ext;
                    $file->move(public_path('comprobantes'), $nombre);
                    DB::table('venta')->where('idventa', $venta->idventa)
                        ->update(['comprobante_pago' => $nombre]);
                }
            }

            $total = 0;

            foreach ($items as $item) {
                $id  = (int) ($item['id'] ?? 0);
                $qty = (int) ($item['qty'] ?? 1);

                // Precio real desde BD (nunca confiar en el cliente)
                $precioReal = DB::table('articulo')
                    ->where('idarticulo', $id)
                    ->value('precio');

                if ($qty < 1) continue;

                $detalle = new DetalleVenta;
                $detalle->idventa     = $venta->idventa;
                $detalle->idarticulo  = $id;
                $detalle->cantidad    = $qty;
                $detalle->descuento   = 0;
                $detalle->precio_venta = $precioReal;
                $detalle->save();

                // Descontar stock del artículo
                DB::table('articulo')
                    ->where('idarticulo', $id)
                    ->decrement('stock', $qty);

                $total += $precioReal * $qty;
            }

            $venta->total_venta = $total;
            $venta->update();

            DB::commit();

            return redirect()->route('comprobante.pago', $venta->idventa)
                ->with('order_placed', true);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('tienda/checkout')
                ->with('error', 'Error al procesar el pedido. Intentá de nuevo.');
        }
    }
}
