@extends('layouts.admin')
@section('page_title', 'Detalle del Pedido')
@section('page_subtitle', 'Pedido #{{ $venta->num_comprobante }}')
@section('box_title', 'Detalle del Pedido')
@section('contenido')

<style>
  .info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
  .info-card { background:#fff; border:1px solid #fff1f2; border-radius:10px; padding:14px 16px; }
  .info-card .label-txt { font-size:10px; font-weight:700; color:#be185d; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; }
  .info-card .value-txt { font-size:14px; color:#333; font-weight:600; }
  .info-card .value-txt.muted { color:#aaa; font-weight:400; font-style:italic; }

  .sec-title { font-size:13px; font-weight:800; color:#be185d; text-transform:uppercase; letter-spacing:.8px; margin:20px 0 10px; border-left:3px solid #be185d; padding-left:10px; }

  .dtable { width:100%; border-collapse:collapse; }
  .dtable thead th { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; padding:10px 12px; font-size:12px; font-weight:700; text-align:left; }
  .dtable tbody td { padding:10px 12px; border-bottom:1px solid #fff1f2; font-size:13px; color:#444; }
  .dtable tfoot td { padding:10px 12px; font-weight:700; background:#fff9fb; }

  .badge-e { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
  .badge-P { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; }
  .badge-A { background:#d5f5e3; color:#1e8449; }
  .badge-C { background:#fadbd8; color:#922b21; }

  .comp-img { max-width:100%; max-height:400px; border-radius:8px; border:2px solid #fff1f2; display:block; margin-bottom:12px; }
  .back-btn { display:inline-flex; align-items:center; gap:6px; background:#f5f5f5; color:#555; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:600; text-decoration:none; margin-bottom:20px; border:1px solid #ddd; }
  .back-btn:hover { background:#eee; color:#333; text-decoration:none; }
</style>

<a href="{{ url('ventas/venta') }}" class="back-btn"><i class="fa fa-arrow-left"></i> Volver al listado</a>

{{-- Encabezado estado --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
  <div>
    <span style="font-size:22px; font-weight:800; color:#be185d;">#{{ $venta->num_comprobante }}</span>
    <span style="margin-left:10px;">
      @if($venta->estado=='A')
        <span class="badge-e badge-A"><i class="fa fa-check"></i> Aprobado</span>
      @elseif($venta->estado=='P')
        <span class="badge-e badge-P"><i class="fa fa-clock-o"></i> Pendiente</span>
      @else
        <span class="badge-e badge-C"><i class="fa fa-times"></i> Cancelado</span>
      @endif
    </span>
  </div>
  <div style="display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ url('ventas/venta/comprobante/'.$venta->idventa) }}" target="_blank"
       style="background:#1a252f; color:#fff; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none;">
       <i class="fa fa-print"></i> Imprimir
    </a>
    @if($venta->estado=='P')
      <a href="{{ URL::action('VentaController@cambiarEstado',$venta->idventa) }}"
         style="background:linear-gradient(135deg,#27ae60,#1e8449); color:#fff; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none;">
         <i class="fa fa-check"></i> Aprobar pedido
      </a>
    @endif
  </div>
</div>

{{-- Info del pedido --}}
<p class="sec-title"><i class="fa fa-info-circle"></i> Datos del pedido</p>
<div class="info-grid">
  <div class="info-card">
    <div class="label-txt">Cliente</div>
    <div class="value-txt">{{ $venta->nombre }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Fecha y hora</div>
    <div class="value-txt">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Tipo distribución</div>
    <div class="value-txt">{{ $venta->tipo_distribucion ?? '—' }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Dirección de envío</div>
    <div class="value-txt {{ $venta->direccion_envio ? '' : 'muted' }}">{{ $venta->direccion_envio ?? 'Sin dirección' }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Fecha de entrega</div>
    <div class="value-txt {{ $venta->fecha_entrega ? '' : 'muted' }}">
      {{ $venta->fecha_entrega ? \Carbon\Carbon::parse($venta->fecha_entrega)->format('d/m/Y') : 'A coordinar' }}
    </div>
  </div>
  <div class="info-card">
    <div class="label-txt">Hora de entrega</div>
    <div class="value-txt {{ $venta->hora_entrega ? '' : 'muted' }}">{{ $venta->hora_entrega ?? 'A coordinar' }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Método de pago</div>
    <div class="value-txt">{{ $venta->metodo_pago ?? 'Efectivo' }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">N° Transacción</div>
    <div class="value-txt {{ $venta->num_transaccion ? '' : 'muted' }}">{{ $venta->num_transaccion ?? 'N/A' }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Tipo comprobante</div>
    <div class="value-txt">{{ $venta->tipo_comprobante }} {{ $venta->serie_comprobante }}-{{ $venta->num_comprobante }}</div>
  </div>
  <div class="info-card">
    <div class="label-txt">Impuesto</div>
    <div class="value-txt">{{ $venta->impuesto }}%</div>
  </div>
  <div class="info-card" style="border-color:#be185d;">
    <div class="label-txt">Total</div>
    <div class="value-txt" style="color:#be185d; font-size:18px;">{{ number_format($venta->total_venta,0,',','.') }} Gs.</div>
  </div>
</div>

{{-- Detalle de productos --}}
<p class="sec-title"><i class="fa fa-list"></i> Productos del pedido</p>
<div class="table-responsive" style="border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(233,30,140,0.07);">
  <table class="dtable">
    <thead>
      <tr>
        <th>Artículo</th>
        <th>Tiempo entrega</th>
        <th style="text-align:right;">Precio unit.</th>
        <th style="text-align:center;">Cant.</th>
        <th style="text-align:right;">Descuento</th>
        <th style="text-align:right;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($detalles as $det)
      <tr>
        <td><b>{{ $det->articulo }}</b></td>
        <td>{{ $det->tiempo_entrega ?? '—' }}</td>
        <td style="text-align:right;">{{ number_format($det->precio_venta,0,',','.') }} Gs.</td>
        <td style="text-align:center;">{{ $det->cantidad }}</td>
        <td style="text-align:right;">{{ number_format($det->descuento,0,',','.') }} Gs.</td>
        <td style="text-align:right; font-weight:700; color:#be185d;">
          {{ number_format($det->cantidad * $det->precio_venta - $det->descuento,0,',','.') }} Gs.
        </td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" style="text-align:right; color:#888;">TOTAL</td>
        <td style="text-align:right; color:#be185d; font-size:16px;">
          {{ number_format($venta->total_venta,0,',','.') }} Gs.
        </td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- Comprobante adjunto por el cliente --}}
<p class="sec-title"><i class="fa fa-upload"></i> Comprobante adjunto por el cliente</p>
@php $comprobante = DB::table('venta')->where('idventa',$venta->idventa)->value('comprobante_pago'); @endphp
<div style="background:#fff; border:1px solid #fff1f2; border-radius:10px; padding:20px;">
  @if($comprobante)
    @php $ext = strtolower(pathinfo($comprobante, PATHINFO_EXTENSION)); @endphp
    @if(in_array($ext, ['jpg','jpeg','png']))
      <img src="{{ asset('comprobantes/'.$comprobante) }}" class="comp-img">
    @endif
    <a href="{{ asset('comprobantes/'.$comprobante) }}" target="_blank"
       style="background:#27ae60; color:#fff; border-radius:8px; padding:9px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
       <i class="fa fa-download"></i> Descargar / Ver comprobante
    </a>
  @else
    <p style="color:#aaa; font-style:italic; margin:0;">
      <i class="fa fa-clock-o"></i> El cliente aún no adjuntó su comprobante de pago.
    </p>
  @endif
</div>

@endsection
