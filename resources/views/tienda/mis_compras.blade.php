@extends('layouts.tienda')
@section('title', 'Mis Compras')
@section('content')

<style>
  .page-title { font-size:22px; font-weight:900; color:#9d174d; margin:0 0 22px; }
  .page-title i { color:#9d174d; margin-right:8px; }

  /* Filtros */
  .filtros { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:22px; }
  .filtro-btn {
    padding:7px 18px; border-radius:20px; font-size:13px; font-weight:700;
    border:2px solid #fecdd3; color:#7f1d3e; background:transparent;
    cursor:pointer; text-decoration:none; transition:all 0.18s;
  }
  .filtro-btn:hover { background:#fff1f2; color:#9d174d; }
  .filtro-btn.active { background:#9d174d; border-color:#9d174d; color:#fff; }

  /* Cards de pedido */
  .pedido-card {
    background:#fff; border-radius:14px;
    box-shadow:0 3px 14px rgba(157,23,77,0.08);
    border:1px solid #fff1f2; margin-bottom:18px; overflow:hidden;
  }
  .pedido-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 20px; background:#fff9fb; border-bottom:1px solid #fff1f2;
    flex-wrap:wrap; gap:10px;
  }
  .pedido-head .num { font-size:14px; font-weight:800; color:#2c3e50; }
  .pedido-head .fecha { font-size:12px; color:#aaa; margin-top:2px; }
  .pedido-head .right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .pedido-total { font-size:18px; font-weight:900; color:#9d174d; }

  .badge-e {
    padding:4px 13px; border-radius:20px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:5px;
  }
  .badge-P { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; }
  .badge-A { background:#d5f5e3; color:#1e8449; }
  .badge-C { background:#fadbd8; color:#922b21; }

  /* Items del pedido */
  .pedido-items { padding:16px 20px; }
  .item-row {
    display:flex; align-items:center; gap:12px; padding:10px 0;
    border-bottom:1px solid #fdf0f6;
  }
  .item-row:last-child { border:none; }
  .item-img { width:50px; height:50px; object-fit:contain; border-radius:8px; background:#f9f9f9; border:1px solid #fff1f2; flex-shrink:0; }
  .item-info { flex:1; }
  .item-info .iname { font-size:13px; font-weight:700; color:#2c3e50; }
  .item-info .imeta { font-size:12px; color:#aaa; margin-top:2px; }
  .item-subtotal { font-size:13px; font-weight:800; color:#9d174d; flex-shrink:0; }

  /* Footer del pedido */
  .pedido-foot {
    padding:12px 20px; border-top:1px solid #fff1f2; background:#fafafa;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
  }
  .pedido-foot .meta-item { font-size:12px; color:#888; display:flex; align-items:center; gap:5px; }
  .pedido-foot .meta-item i { color:#9d174d; }
  .btn-cancelar {
    background:#fadbd8; color:#922b21; border:none; border-radius:8px;
    padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer; transition:background 0.15s;
  }
  .btn-cancelar:hover { background:#f5b7b1; }

  /* Empty */
  .empty-state { text-align:center; padding:60px 20px; }
  .empty-state i { font-size:56px; color:#fecdd3; display:block; margin-bottom:14px; }
  .empty-state p { font-size:16px; font-weight:700; color:#7f1d3e; }
</style>

<p class="page-title"><i class="fa fa-shopping-bag"></i> Mis Compras</p>

{{-- Alertas --}}
@if(session('status'))
  <div style="background:#fff1f2; border-left:4px solid #9d174d; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:14px; color:#9d174d; font-weight:600;">
    <i class="fa fa-check-circle"></i> {{ session('status') }}
  </div>
@endif
@if(session('error'))
  <div style="background:#fadbd8; border-left:4px solid #e74c3c; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:14px; color:#922b21;">
    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
  </div>
@endif

{{-- Filtros --}}
<div class="filtros">
  <a href="{{ url('tienda/mis-compras') }}"          class="filtro-btn {{ !request('estado') ? 'active' : '' }}">Todos ({{ $totales['todos'] }})</a>
  <a href="{{ url('tienda/mis-compras?estado=P') }}" class="filtro-btn {{ request('estado')=='P' ? 'active' : '' }}">Pendientes ({{ $totales['P'] }})</a>
  <a href="{{ url('tienda/mis-compras?estado=A') }}" class="filtro-btn {{ request('estado')=='A' ? 'active' : '' }}">Aprobados ({{ $totales['A'] }})</a>
  <a href="{{ url('tienda/mis-compras?estado=C') }}" class="filtro-btn {{ request('estado')=='C' ? 'active' : '' }}">Cancelados ({{ $totales['C'] }})</a>
</div>

{{-- Lista de pedidos --}}
@forelse($pedidos as $pedido)
<div class="pedido-card">

  {{-- Header --}}
  <div class="pedido-head">
    <div>
      <p class="num"><i class="fa fa-hashtag"></i> Pedido #{{ $pedido->num_comprobante }}</p>
      <p class="fecha"><i class="fa fa-calendar"></i> {{ $pedido->fecha_hora }}</p>
    </div>
    <div class="right">
      @if($pedido->estado=='P')
        <span class="badge-e badge-P"><i class="fa fa-clock-o"></i> Pendiente</span>
      @elseif($pedido->estado=='A')
        <span class="badge-e badge-A"><i class="fa fa-check"></i> Aprobado</span>
      @else
        <span class="badge-e badge-C"><i class="fa fa-times"></i> Cancelado</span>
      @endif
      <span class="pedido-total">{{ number_format($pedido->total_venta, 0, ',', '.') }} Gs.</span>
    </div>
  </div>

  {{-- Items --}}
  <div class="pedido-items">
    @foreach($pedido->detalles as $det)
    <div class="item-row">
      <img class="item-img"
        src="{{ asset('imagenes/articulos/'.rawurlencode($det->imagen ?? '')) }}"
        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Crect width=\'50\' height=\'50\' fill=\'%23fff9fb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'20\' text-anchor=\'middle\' dominant-baseline=\'central\' fill=\'%23f48fb1\'%3E%3F%3C/text%3E%3C/svg%3E'"
        alt="{{ $det->articulo }}">
      <div class="item-info">
        <p class="iname">{{ $det->articulo }}</p>
        <p class="imeta">{{ $det->cantidad }} unid. × {{ number_format($det->precio_venta, 0, ',', '.') }} Gs.</p>
      </div>
      <span class="item-subtotal">{{ number_format($det->cantidad * $det->precio_venta, 0, ',', '.') }} Gs.</span>
    </div>
    @endforeach
  </div>

  {{-- Footer --}}
  <div class="pedido-foot">
    <div style="display:flex; gap:16px; flex-wrap:wrap;">
      <span class="meta-item"><i class="fa fa-credit-card"></i> {{ $pedido->metodo_pago }}</span>
      @if($pedido->fecha_entrega)
        @php
          $dias  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
          $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
          $cf    = \Carbon\Carbon::parse($pedido->fecha_entrega);
          $fe    = $dias[$cf->dayOfWeek].' '.$cf->day.' '.$meses[$cf->month-1];
        @endphp
        <span class="meta-item"><i class="fa fa-truck"></i> Entrega: {{ $fe }}</span>
      @endif
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
      {{-- Subir comprobante --}}
      @if(in_array($pedido->estado, ['P','A']))
        @if($pedido->comprobante_pago)
          <a href="{{ asset('comprobantes/'.$pedido->comprobante_pago) }}" target="_blank"
            style="background:#d5f5e3; color:#1e8449; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
            <i class="fa fa-check-circle"></i> Ver comprobante
          </a>
        @endif
        <button onclick="toggleUpload({{ $pedido->idventa }})"
          style="background:#fff1f2; color:#7f1d3e; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer;">
          <i class="fa fa-upload"></i> {{ $pedido->comprobante_pago ? 'Reemplazar' : 'Adjuntar comprobante' }}
        </button>
      @endif

      {{-- Cancelar pedido --}}
      @if($pedido->estado == 'P')
        <form method="POST" action="{{ url('tienda/compra/'.$pedido->idventa.'/cancelar') }}"
          onsubmit="return confirm('¿Cancelar este pedido?')">
          {{ csrf_field() }}
          <button type="submit" class="btn-cancelar">
            <i class="fa fa-times"></i> Cancelar
          </button>
        </form>
      @endif
    </div>

    {{-- Panel de subida (oculto por defecto) --}}
    @if(in_array($pedido->estado, ['P','A']))
    <div id="upload-panel-{{ $pedido->idventa }}" style="display:none; width:100%; margin-top:12px; border-top:1px solid #fff1f2; padding-top:12px;">
      <form method="POST" action="{{ route('compra.comprobante', $pedido->idventa) }}" enctype="multipart/form-data"
        style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        {{ csrf_field() }}
        <input type="file" name="comprobante_archivo" accept=".jpg,.jpeg,.png,.pdf" required
          style="font-size:13px; flex:1; min-width:200px;">
        <button type="submit"
          style="background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff; border:none; border-radius:8px; padding:8px 18px; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap;">
          <i class="fa fa-upload"></i> Subir
        </button>
      </form>
      <p style="font-size:11px; color:#bbb; margin:6px 0 0;">JPG, PNG o PDF · Máximo 5 MB</p>
    </div>
    @endif
  </div>

</div>
@empty
<div class="empty-state">
  <i class="fa fa-shopping-basket"></i>
  <p>No tenés pedidos {{ request('estado') ? 'con ese estado' : 'todavía' }}.</p>
  <a href="{{ url('tienda') }}" style="color:#9d174d; font-weight:700; font-size:14px;">
    <i class="fa fa-arrow-left"></i> Ir a la tienda
  </a>
</div>
@endforelse

{{-- Paginación --}}
<div style="margin-top:20px; text-align:center;">
  {{ $pedidos->appends(request()->query())->render() }}
</div>

@push('scripts')
<script>
function toggleUpload(id) {
  const panel = document.getElementById('upload-panel-' + id);
  panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush
@endsection
