<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use sisVentas\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use sisVentas\Http\Requests\VentaFormRequest;
use sisVentas\Venta;
use sisVentas\DetalleVenta;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class VentaController extends Controller
{
    public function __construct()
  	{
        $this->middleware('auth');
  	}
  	public function index(Request $request)
		{
			$query  = trim($request->get('searchText', ''));
			$estado = $request->get('estado', '');
			$hoy    = date('Y-m-d');

			$ventas = DB::table('venta as v')
				->join('persona as p','v.idcliente','=','p.idpersona')
				->select(
					'v.idventa','v.fecha_hora','p.nombre',
					'v.tipo_comprobante','v.serie_comprobante','v.num_comprobante',
					'v.impuesto','v.estado','v.total_venta',
					'v.metodo_pago','v.tipo_distribucion','v.fecha_entrega',
					'v.hora_entrega','v.comprobante_pago'
				)
				->when($estado, function($q) use ($estado) {
					return $q->where('v.estado', $estado);
				})
				->where(function($q) use ($query) {
					$q->where('v.num_comprobante','LIKE','%'.$query.'%')
					  ->orWhere('p.nombre','LIKE','%'.$query.'%');
				})
				->orderBy('v.idventa','desc')
				->paginate(10);

			// Pedidos con entrega hoy (para alerta)
			$entregasHoy = DB::table('venta as v')
				->join('persona as p','v.idcliente','=','p.idpersona')
				->select('v.idventa','v.num_comprobante','v.total_venta','v.hora_entrega','v.tipo_distribucion','p.nombre as cliente')
				->whereDate('v.fecha_entrega', $hoy)
				->whereIn('v.estado', ['P','A'])
				->orderBy('v.hora_entrega','asc')
				->get();

			return view('ventas.venta.index', [
				'ventas'       => $ventas,
				'searchText'   => $query,
				'entregasHoy'  => $entregasHoy,
			]);
		}
  	public function create()
  	{
  		$personas=DB::table('persona')->where('tipo_persona','=','Cliente')->get();
  		$articulos=DB::table('articulo as art')
  			->join('detalle_ingreso as di','art.idarticulo','=','di.idarticulo')
  			->select(DB::raw('CONCAT(art.codigo, " ",art.nombre) AS articulo'), 'art.idarticulo','art.stock',DB::raw('avg(di.precio_venta) as precio_promedio'))
  			->where('art.estado','=','Activo')
  			->where('art.estado','>','0')
  			->groupBy('articulo','art.idarticulo','art.stock')
  			->get();
  		return view("ventas.venta.create",["personas"=>$personas,"articulos"=>$articulos]);
  	}
  	public function store(VentaFormRequest $request)
  	{
  		try{
  			DB::beginTransaction();
  			$venta=new Venta;
  			$venta->idcliente=$request->get('idcliente');
  			$venta->tipo_comprobante=$request->get('tipo_comprobante');
  			$venta->serie_comprobante=$request->get('serie_comprobante');
  			$venta->num_comprobante=$request->get('num_comprobante');
  			$venta->total_venta=$request->get('total_venta');

  			$mytime = Carbon::now('America/Asuncion');
  			$venta->fecha_hora=$mytime->toDateTimeString();
  			$venta->impuesto='5';
  			$venta->estado='A';
  			$venta->save();
			
  			$idarticulo=$request->get('idarticulo');
  			$cantidad=$request->get('cantidad');
  			$descuento=$request->get('descuento');
  			$precio_venta=$request->get('precio_venta');

  			$cont = 0;

  			while($cont < count($idarticulo)){
  				$detalle = new DetalleVenta();
  				$detalle->idventa=$venta->idventa;
  				$detalle->idarticulo=$idarticulo[$cont];
  				$detalle->cantidad=$cantidad[$cont];
  				$detalle->descuento=$descuento[$cont];
  				$detalle->precio_venta=$precio_venta[$cont];
  				$detalle->save();
  				$cont=$cont+1;
  		  }

		  DB::commit();
		
		}catch(Exception $e)
   		{
   			DB::rollBack();
   		}

   		return Redirect::to('ventas/venta');
  	} 
  	public function show($id)
  	{
  		$venta=DB::table('venta as v')
  			->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
  			->select(
                'v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante',
                'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta',
                'v.metodo_pago', 'v.tipo_distribucion', 'v.fecha_entrega', 'v.hora_entrega',
                'v.direccion_envio', 'v.num_transaccion', 'v.tipo_facturacion', 'v.comprobante_pago'
            )
  			->where('v.idventa','=',$id)
  			->first();

			$detalles=DB::table('detalle_venta as d')
			->join('articulo as a', 'd.idarticulo','=','a.idarticulo')
			->select('a.nombre as articulo','d.cantidad','d.descuento','d.precio_venta', 'a.tiempo_entrega') // Agregamos tiempo_entrega
			->where('d.idventa','=',$id)
			->get();





  		return view("ventas.venta.show",["venta"=>$venta,"detalles"=>$detalles]);
  	}

	public function comprobante($id)
		{
			$venta = DB::table('venta as v')
				->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
				->select(
					'v.idventa', 'v.fecha_hora', 'v.fecha_entrega', 'v.hora_entrega',
					'p.nombre', 'p.apellido', 'p.direccion', 'p.num_documento', 'p.telefono', 'p.ciudad',
					'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante',
					'v.total_venta', 'v.estado', 'v.metodo_pago', 'v.tipo_distribucion',
					'v.direccion_envio', 'v.tipo_facturacion'
				)
				->where('v.idventa', '=', $id)
				->first();

			$detalles = DB::table('detalle_venta as d')
				->join('articulo as a', 'd.idarticulo', '=', 'a.idarticulo')
				->select('a.codigo', 'a.nombre as articulo', 'd.cantidad', 'd.precio_venta', 'd.descuento')
				->where('d.idventa', '=', $id)
				->get();

			return view('ventas.venta.comprobante', ['venta' => $venta, 'detalles' => $detalles]);
		}


	public function cambiarEstado($id)
		{
			try {
				DB::beginTransaction();

				$venta = Venta::findOrFail($id);
				
				// Solo cambiamos a Aprobado si está Pendiente
				if ($venta->estado == 'P') {
					$venta->estado = 'A'; // 'A' de Aprobado
					$venta->update();
					
					DB::commit();
					return Redirect::to('ventas/venta')->with('msj', 'Pedido aprobado correctamente.');
				} else {
					return Redirect::to('ventas/venta')->with('error', 'El pedido no se puede aprobar.');
				}

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::to('ventas/venta')->with('error', 'Error: ' . $e->getMessage());
			}
		}

    public function reporte(Request $request)
    {
        $desde  = $request->get('desde', date('Y-m-01'));
        $hasta  = $request->get('hasta', date('Y-m-d'));
        $estado = $request->get('estado', '');

        $query = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select(
                'v.idventa', 'v.fecha_hora', 'v.num_comprobante', 'v.serie_comprobante',
                'p.nombre', 'v.tipo_comprobante', 'v.metodo_pago',
                'v.total_venta', 'v.estado'
            )
            ->whereDate('v.fecha_hora', '>=', $desde)
            ->whereDate('v.fecha_hora', '<=', $hasta);

        if ($estado) {
            $query->where('v.estado', $estado);
        }

        $ventas = $query->orderBy('v.fecha_hora', 'desc')->get();

        $sumaTotal = $ventas->whereNotIn('estado', ['C'])->sum('total_venta');

        $totales = [
            'cantidad'   => $ventas->count(),
            'suma'       => $sumaTotal,
            'aprobadas'  => $ventas->where('estado', 'A')->count(),
            'pendientes' => $ventas->where('estado', 'P')->count(),
            'canceladas' => $ventas->where('estado', 'C')->count(),
            'promedio'   => $ventas->whereNotIn('estado', ['C'])->count() > 0
                            ? $sumaTotal / $ventas->whereNotIn('estado', ['C'])->count()
                            : 0,
        ];

        // Detalle de cada venta
        foreach ($ventas as $v) {
            $v->detalles = DB::table('detalle_venta as dv')
                ->join('articulo as a', 'dv.idarticulo', '=', 'a.idarticulo')
                ->select('a.codigo', 'a.nombre as articulo', 'dv.cantidad', 'dv.precio_venta', 'dv.descuento')
                ->where('dv.idventa', $v->idventa)
                ->get();
        }

        // Desglose por método de pago
        $porMetodo = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->select(
                DB::raw('COALESCE(v.metodo_pago, "Efectivo en tienda") as metodo'),
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(v.total_venta) as total')
            )
            ->whereDate('v.fecha_hora', '>=', $desde)
            ->whereDate('v.fecha_hora', '<=', $hasta)
            ->where('v.estado', '!=', 'C')
            ->when($estado, fn($q) => $q->where('v.estado', $estado))
            ->groupBy('metodo')
            ->orderBy('total', 'desc')
            ->get();

        // Ventas por día para el gráfico
        $porDia = DB::table('venta')
            ->select(DB::raw('DATE(fecha_hora) as dia'), DB::raw('SUM(total_venta) as total'), DB::raw('COUNT(*) as cantidad'))
            ->whereDate('fecha_hora', '>=', $desde)
            ->whereDate('fecha_hora', '<=', $hasta)
            ->where('estado', '!=', 'C')
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->get();

        return view('ventas.venta.reporte', compact(
            'ventas', 'totales', 'desde', 'hasta', 'estado', 'porMetodo', 'porDia'
        ));
    }

  	public function destroy($id)
		{
			try {
				DB::beginTransaction();

				// 1. Buscamos la venta
				$venta = DB::table('venta')->where('idventa', $id)->first();

				// 2. IMPORTANTE: Solo devolvemos stock si la venta aún está Pendiente ('P')
				// Esto evita que si anulas una venta dos veces, se sume stock doble.
				if ($venta->estado == 'P') {
					
					// 3. Obtenemos los artículos y cantidades de esa venta
					$detalles = DB::table('detalle_venta')
						->where('idventa', '=', $id)
						->get();

					// 4. Devolvemos el stock a cada artículo
					foreach ($detalles as $det) {
						DB::table('articulo')
							->where('idarticulo', $det->idarticulo)
							->increment('stock', $det->cantidad);
					}

					// 5. Cambiamos el estado a 'C' (Cancelado)
					DB::table('venta')
						->where('idventa', $id)
						->update(['estado' => 'C']);
					
					DB::commit();
					return Redirect::to('ventas/venta')->with('msj', 'Venta anulada y stock recuperado.');

				} else {
					DB::rollBack();
					return Redirect::to('ventas/venta')->with('error', 'La venta ya estaba anulada o procesada.');
				}

			} catch (\Exception $e) {
				DB::rollBack();
				return Redirect::to('ventas/venta')->with('error', 'Ocurrió un error al anular: ' . $e->getMessage());
			}
		}


}
