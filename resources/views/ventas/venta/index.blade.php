@extends('layouts.admin')
@section('page_title', 'Pedidos')
@section('page_subtitle', 'Gestión de ventas y pedidos')
@section('box_title', 'Listado de Pedidos')
@section('contenido')

<style>
  /* ---- Alerta entregas hoy ---- */
  .hoy-banner {
    background:linear-gradient(135deg,#fff8e1,#fef3cd);
    border:1px solid #f9e79f; border-left:4px solid #f39c12;
    border-radius:12px; padding:14px 18px; margin-bottom:20px;
    display:flex; align-items:flex-start; gap:14px; flex-wrap:wrap;
  }
  .hoy-banner .hb-icon { font-size:24px; color:#f39c12; flex-shrink:0; margin-top:1px; }
  .hoy-banner .hb-title { font-size:13px; font-weight:800; color:#7d6608; margin:0 0 6px; }
  .hoy-banner .hb-chips { display:flex; flex-wrap:wrap; gap:6px; }
  .hoy-chip {
    background:#fff; border:1px solid #f9e79f; border-radius:20px;
    padding:4px 12px; font-size:11px; font-weight:700; color:#7d6608;
    display:inline-flex; align-items:center; gap:5px; text-decoration:none;
  }
  .hoy-chip:hover { background:#fef9e7; color:#7d6608; }

  /* ---- Barra filtros ---- */
  .filtros-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; align-items:center; }
  .filtro-pill {
    padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700;
    border:2px solid #fecdd3; color:#9d174d; background:transparent;
    cursor:pointer; text-decoration:none; transition:all .18s;
  }
  .filtro-pill:hover { background:#fff1f2; color:#7f1d3e; }
  .filtro-pill.active { background:#be185d; border-color:#be185d; color:#fff; }
  .filtro-pill .pill-count { opacity:.75; font-size:10px; }
  .filtro-pill.active .pill-count { opacity:.9; }

  /* Search inline */
  .search-inline { display:flex; gap:0; border:2px solid #fecdd3; border-radius:20px; overflow:hidden; background:#fff; }
  .search-inline input { border:none; outline:none; padding:6px 14px; font-size:12px; min-width:160px; background:transparent; }
  .search-inline button { background:#be185d; color:#fff; border:none; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer; }
  .search-inline button:hover { background:#9d174d; }

  /* ---- Tabla ---- */
  .vtable { width:100%; border-collapse:collapse; background:#fff; }
  .vtable thead th {
    background:linear-gradient(135deg,#be185d,#9d174d); color:#fff;
    padding:11px 10px; font-size:11px; font-weight:700; text-align:left; white-space:nowrap;
  }
  .vtable tbody td { padding:10px 10px; border-bottom:1px solid #fff1f2; font-size:12px; color:#444; vertical-align:middle; }
  .vtable tbody tr:hover { background:#fff9fb; }

  /* Fila urgente (entrega hoy) */
  .vtable tbody tr.row-urgente td:first-child { border-left:3px solid #f39c12; }
  .vtable tbody tr.row-urgente { background:#fffdf2; }
  .vtable tbody tr.row-urgente:hover { background:#fef9e7; }

  /* Fila con comprobante pendiente */
  .vtable tbody tr.row-comp-listo { }

  /* Badges */
  .badge-e { padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }
  .badge-P { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; }
  .badge-A { background:#d5f5e3; color:#1e8449; }
  .badge-C { background:#fadbd8; color:#922b21; }
  .badge-hoy { background:#f39c12; color:#fff; border-radius:20px; padding:2px 8px; font-size:10px; font-weight:700; }
  .badge-manana { background:#fef9e7; color:#b7770d; border-radius:20px; padding:2px 8px; font-size:10px; font-weight:700; }
  .badge-comp-ok { background:#d5f5e3; color:#1e8449; border-radius:20px; padding:3px 9px; font-size:11px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
  .badge-comp-ok:hover { background:#c3f0d0; }
  .badge-comp-no { background:#f5f5f5; color:#ccc; border-radius:20px; padding:3px 9px; font-size:11px; }
  .badge-comp-pendiente { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; border-radius:20px; padding:3px 9px; font-size:11px; font-weight:700; animation:pulse 1.6s ease-in-out infinite; }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

  /* Botones */
  .btn-a { border:none; border-radius:6px; padding:4px 9px; font-size:11px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:3px; transition:opacity .18s; white-space:nowrap; }
  .btn-a:hover { opacity:.82; text-decoration:none; }
  .ba-ver     { background:#2980b9; color:#fff; }
  .ba-aprobar { background:linear-gradient(135deg,#27ae60,#1e8449); color:#fff; }
  .ba-anular  { background:#e74c3c; color:#fff; }
  .ba-ticket  { background:#1a252f; color:#fff; }

  /* ---- Modal cancelación ---- */
  .modal-content { border-radius:12px; overflow:hidden; border:none; }
  .modal-header { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; padding:16px 20px; }
  .modal-header .modal-title { font-weight:800; font-size:15px; }
  .modal-header .close { color:#fff; opacity:.8; font-size:20px; }
  .modal-header .close:hover { opacity:1; }
  .modal-body { padding:20px; font-size:14px; color:#555; }
  .modal-footer { border-top:1px solid #fff1f2; padding:12px 20px; }
  .btn-cancel-confirm { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:8px; padding:8px 20px; font-size:13px; font-weight:800; cursor:pointer; }
  .btn-cancel-close { background:#f5f5f5; color:#555; border:none; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; }

  /* ---- Tiempo relativo ---- */
  .tiempo-rel { font-size:10px; color:#bbb; margin-top:2px; }
</style>

{{-- Mensajes flash --}}
@if(session('msj'))
  <div style="background:#d5f5e3; border-left:4px solid #27ae60; border-radius:8px; padding:11px 16px; margin-bottom:16px; font-size:13px; color:#1e8449; font-weight:600;">
    <i class="fa fa-check-circle"></i> {{ session('msj') }}
  </div>
@endif
@if(session('error'))
  <div style="background:#fadbd8; border-left:4px solid #e74c3c; border-radius:8px; padding:11px 16px; margin-bottom:16px; font-size:13px; color:#922b21; font-weight:600;">
    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
  </div>
@endif

{{-- Alerta entregas hoy --}}
@if($entregasHoy->count())
<div class="hoy-banner">
  <div class="hb-icon"><i class="fa fa-truck"></i></div>
  <div style="flex:1;">
    <p class="hb-title"><i class="fa fa-clock-o"></i> {{ $entregasHoy->count() }} pedido(s) con entrega programada para HOY</p>
    <div class="hb-chips">
      @foreach($entregasHoy as $eh)
      <a href="{{ URL::action('VentaController@show', $eh->idventa) }}" class="hoy-chip">
        <i class="fa fa-{{ $eh->tipo_distribucion==='Delivery' ? 'truck' : 'store' }}"></i>
        #{{ $eh->num_comprobante }} — {{ $eh->cliente }}
        @if($eh->hora_entrega && $eh->hora_entrega !== 'A coordinar')
          · {{ $eh->hora_entrega }}
        @endif
      </a>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Filtros + búsqueda --}}
@php
  $estadoFiltro = request('estado', '');
  $conteos = [
    '' => DB::table('venta')->count(),
    'P' => DB::table('venta')->where('estado','P')->count(),
    'A' => DB::table('venta')->where('estado','A')->count(),
    'C' => DB::table('venta')->where('estado','C')->count(),
  ];
  $hoy      = date('Y-m-d');
  $manana   = date('Y-m-d', strtotime('+1 day'));
@endphp

<div class="filtros-bar">
  <a href="{{ url('ventas/venta') }}"
     class="filtro-pill {{ $estadoFiltro==='' ? 'active':'' }}">
    Todos <span class="pill-count">({{ $conteos[''] }})</span>
  </a>
  <a href="{{ url('ventas/venta?estado=P') }}"
     class="filtro-pill {{ $estadoFiltro==='P' ? 'active':'' }}">
    <i class="fa fa-clock-o"></i> Pendientes <span class="pill-count">({{ $conteos['P'] }})</span>
  </a>
  <a href="{{ url('ventas/venta?estado=A') }}"
     class="filtro-pill {{ $estadoFiltro==='A' ? 'active':'' }}">
    <i class="fa fa-check"></i> Aprobados <span class="pill-count">({{ $conteos['A'] }})</span>
  </a>
  <a href="{{ url('ventas/venta?estado=C') }}"
     class="filtro-pill {{ $estadoFiltro==='C' ? 'active':'' }}">
    <i class="fa fa-times"></i> Cancelados <span class="pill-count">({{ $conteos['C'] }})</span>
  </a>

  <div style="margin-left:auto;">
    <form method="GET" action="{{ url('ventas/venta') }}" style="margin:0;">
      @if($estadoFiltro)
        <input type="hidden" name="estado" value="{{ $estadoFiltro }}">
      @endif
      <div class="search-inline">
        <input type="text" name="searchText" placeholder="Buscar por cliente o Nro..." value="{{ $searchText }}">
        <button type="submit"><i class="fa fa-search"></i></button>
      </div>
    </form>
  </div>
</div>

{{-- Tabla --}}
<div class="table-responsive" style="border-radius:12px; overflow:hidden; box-shadow:0 2px 14px rgba(233,30,140,0.08);">
  <table class="vtable">
    <thead>
      <tr>
        <th>Nro.</th>
        <th>Fecha pedido</th>
        <th>Cliente</th>
        <th>Entrega</th>
        <th>Método pago</th>
        <th>Comprobante</th>
        <th style="text-align:right;">Total</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ventas as $ven)
      @php
        $esHoy    = $ven->fecha_entrega === $hoy;
        $esManana = $ven->fecha_entrega === $manana;
        $cf       = $ven->fecha_entrega ? \Carbon\Carbon::parse($ven->fecha_entrega) : null;
        $dias     = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $compPendiente = $ven->comprobante_pago && $ven->estado === 'P';
      @endphp
      <tr class="{{ $esHoy ? 'row-urgente' : '' }}">
        <td>
          <b style="color:#be185d; font-size:13px;">#{{ $ven->num_comprobante }}</b>
        </td>
        <td>
          <span>{{ \Carbon\Carbon::parse($ven->fecha_hora)->format('d/m/y') }}</span><br>
          <span class="tiempo-rel">{{ \Carbon\Carbon::parse($ven->fecha_hora)->diffForHumans() }}</span>
        </td>
        <td>
          <b style="font-size:12px;">{{ $ven->nombre }}</b>
          @if($ven->tipo_distribucion)
          <br><small style="color:#aaa;">
            <i class="fa fa-{{ $ven->tipo_distribucion==='Delivery' ? 'truck' : 'store' }}"></i>
            {{ $ven->tipo_distribucion }}
          </small>
          @endif
        </td>
        <td>
          @if($cf)
            <div style="font-size:12px; font-weight:700; color:{{ $esHoy ? '#f39c12' : '#2c3e50' }};">
              {{ $dias[$cf->dayOfWeek] }} {{ $cf->format('d/m') }}
            </div>
            @if($esHoy)
              <span class="badge-hoy"><i class="fa fa-star"></i> HOY</span>
            @elseif($esManana)
              <span class="badge-manana"><i class="fa fa-clock-o"></i> Mañana</span>
            @endif
            @if($ven->hora_entrega && $ven->hora_entrega !== 'A coordinar')
              <br><small style="color:#aaa; font-size:10px;">{{ $ven->hora_entrega }}</small>
            @endif
          @else
            <span style="color:#ddd; font-size:11px;">A coordinar</span>
          @endif
        </td>
        <td style="font-size:11px; color:#666;">
          <i class="fa fa-{{ str_contains($ven->metodo_pago ?? '', 'Transferencia') ? 'bank' : (str_contains($ven->metodo_pago ?? '', 'Tarjeta') ? 'credit-card' : 'money') }}"></i>
          {{ $ven->metodo_pago ?? 'Efectivo' }}
        </td>
        <td>
          @if($ven->comprobante_pago)
            <a href="{{ asset('comprobantes/'.$ven->comprobante_pago) }}" target="_blank"
               class="{{ $compPendiente ? 'badge-comp-pendiente' : 'badge-comp-ok' }}">
              <i class="fa fa-{{ $compPendiente ? 'exclamation-circle' : 'check-circle' }}"></i>
              {{ $compPendiente ? '¡Verificar!' : 'Ver' }}
            </a>
          @else
            <span class="badge-comp-no"><i class="fa fa-minus"></i></span>
          @endif
        </td>
        <td style="text-align:right;">
          <b style="color:#be185d; font-size:13px;">{{ number_format($ven->total_venta,0,',','.') }} Gs.</b>
        </td>
        <td>
          @if($ven->estado=='A')
            <span class="badge-e badge-A"><i class="fa fa-check"></i> Aprobado</span>
          @elseif($ven->estado=='P')
            <span class="badge-e badge-P"><i class="fa fa-clock-o"></i> Pendiente</span>
          @else
            <span class="badge-e badge-C"><i class="fa fa-times"></i> Cancelado</span>
          @endif
        </td>
        <td style="white-space:nowrap;">
          <a href="{{ URL::action('VentaController@show',$ven->idventa) }}" class="btn-a ba-ver" title="Ver detalle">
            <i class="fa fa-eye"></i>
          </a>
          @if($ven->estado=='P')
            <a href="{{ URL::action('VentaController@cambiarEstado',$ven->idventa) }}" class="btn-a ba-aprobar" title="Aprobar pedido">
              <i class="fa fa-check"></i> Aprobar
            </a>
          @endif
          @if($ven->estado!='C')
            <button class="btn-a ba-anular" title="Cancelar pedido"
              data-toggle="modal" data-target="#modal-delete-{{ $ven->idventa }}"
              data-num="{{ $ven->num_comprobante }}" data-cliente="{{ $ven->nombre }}">
              <i class="fa fa-ban"></i>
            </button>
          @endif
          <a href="{{ url('ventas/venta/comprobante/'.$ven->idventa) }}" target="_blank" class="btn-a ba-ticket" title="Imprimir comprobante">
            <i class="fa fa-print"></i>
          </a>
        </td>
      </tr>

      {{-- Modal cancelación --}}
      <div class="modal fade" id="modal-delete-{{ $ven->idventa }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" style="margin-top:120px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
              <h4 class="modal-title"><i class="fa fa-ban"></i> Cancelar pedido</h4>
            </div>
            <div class="modal-body">
              <p>¿Cancelar el pedido <strong style="color:#be185d;">#{{ $ven->num_comprobante }}</strong> de <strong>{{ $ven->nombre }}</strong>?</p>
              <p style="font-size:12px; color:#e74c3c; background:#fadbd8; border-radius:6px; padding:8px 12px; margin:0;">
                <i class="fa fa-info-circle"></i> Se devolverá el stock de los artículos al almacén.
              </p>
            </div>
            <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end;">
              <button type="button" class="btn-cancel-close" data-dismiss="modal">No, volver</button>
              {!! Form::open(['action' => ['VentaController@destroy', $ven->idventa], 'method' => 'delete', 'style' => 'display:inline;']) !!}
                <button type="submit" class="btn-cancel-confirm"><i class="fa fa-ban"></i> Sí, cancelar</button>
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>

      @empty
      <tr>
        <td colspan="9" style="text-align:center; padding:36px; color:#fda4af;">
          <i class="fa fa-inbox" style="font-size:36px; display:block; margin-bottom:10px;"></i>
          <p style="margin:0; font-weight:700; color:#9d174d;">No hay pedidos con ese filtro.</p>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
  <span style="font-size:12px; color:#aaa;">
    Mostrando {{ $ventas->firstItem() ?? 0 }}–{{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} pedidos
  </span>
  {{ $ventas->appends(request()->query())->render() }}
</div>

@endsection
