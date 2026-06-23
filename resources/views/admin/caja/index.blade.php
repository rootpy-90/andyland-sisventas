@extends('layouts.admin')
@section('page_title', 'Caja')
@section('page_subtitle', 'Apertura, arqueo y cierre de caja')
@section('box_title', 'Módulo de Caja')
@section('contenido')

<style>
  /* ---- Estado caja ---- */
  .caja-estado {
    border-radius:14px; padding:20px 24px; margin-bottom:22px;
    display:flex; align-items:center; gap:18px; flex-wrap:wrap;
    box-shadow:0 3px 14px rgba(0,0,0,0.08);
  }
  .caja-abierta  { background:linear-gradient(135deg,#d5f5e3,#a9dfbf); border:1px solid #82c8a0; }
  .caja-cerrada  { background:linear-gradient(135deg,#fadbd8,#f5b7b1); border:1px solid #e59090; }
  .caja-estado .ce-icon { font-size:40px; }
  .caja-abierta .ce-icon  { color:#1e8449; }
  .caja-cerrada .ce-icon  { color:#922b21; }
  .caja-estado .ce-title { font-size:18px; font-weight:900; }
  .caja-abierta .ce-title { color:#1a5276; }
  .caja-cerrada .ce-title { color:#922b21; }
  .caja-estado .ce-sub { font-size:13px; color:#666; margin-top:3px; }

  /* ---- Grids ---- */
  .dos-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:20px; }
  @media(max-width:800px){ .dos-col{ grid-template-columns:1fr; } }
  .tres-col { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px; }
  @media(max-width:700px){ .tres-col{ grid-template-columns:1fr 1fr; } }

  /* ---- Panel ---- */
  .caja-panel { background:#fff; border:1px solid #fff1f2; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(233,30,140,0.06); }
  .cp-head { padding:13px 18px; border-bottom:1px solid #fff1f2; display:flex; align-items:center; justify-content:space-between; }
  .cp-head h4 { margin:0; font-size:13px; font-weight:800; color:#2c3e50; display:flex; align-items:center; gap:7px; }
  .cp-head h4 i { color:#be185d; }
  .cp-body { padding:18px; }

  /* ---- Cards métricas ---- */
  .metric-card { background:#fff; border:1px solid #fff1f2; border-radius:12px; padding:16px 18px; text-align:center; box-shadow:0 2px 8px rgba(233,30,140,0.05); }
  .metric-card .mc-num { font-size:22px; font-weight:900; line-height:1.1; }
  .metric-card .mc-lbl { font-size:10px; color:#aaa; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }

  /* ---- Form ---- */
  .cj-form .fg { display:flex; flex-direction:column; margin-bottom:14px; }
  .cj-form label { font-size:11px; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
  .cj-form input, .cj-form textarea, .cj-form select {
    border:2px solid #fecdd3; border-radius:8px; padding:9px 12px;
    font-size:13px; outline:none; transition:border-color .15s; width:100%;
  }
  .cj-form input:focus, .cj-form textarea:focus, .cj-form select:focus { border-color:#be185d; }
  .btn-abrir  { background:linear-gradient(135deg,#27ae60,#1e8449); color:#fff; border:none; border-radius:8px; padding:10px 22px; font-size:13px; font-weight:800; cursor:pointer; }
  .btn-cerrar { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:8px; padding:10px 22px; font-size:13px; font-weight:800; cursor:pointer; }
  .btn-add-mov { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border:none; border-radius:8px; padding:9px 18px; font-size:13px; font-weight:800; cursor:pointer; }

  /* ---- Tabla movimientos ---- */
  .mov-table { width:100%; border-collapse:collapse; font-size:12px; }
  .mov-table thead th { background:linear-gradient(135deg,#1e293b,#334155); color:#fff; padding:9px 10px; text-align:left; white-space:nowrap; }
  .mov-table tbody td { padding:9px 10px; border-bottom:1px solid #fff1f2; vertical-align:middle; }
  .mov-table tbody tr:hover { background:#fff9fb; }
  .badge-ing { background:#d5f5e3; color:#1e8449; padding:2px 9px; border-radius:20px; font-size:10px; font-weight:700; }
  .badge-egr { background:#fadbd8; color:#922b21; padding:2px 9px; border-radius:20px; font-size:10px; font-weight:700; }
  .btn-del-mov { background:#fadbd8; color:#922b21; border:none; border-radius:5px; padding:3px 8px; font-size:11px; cursor:pointer; }
  .btn-del-mov:hover { background:#f5b7b1; }

  /* ---- Tabla ventas del día ---- */
  .vta-table { width:100%; border-collapse:collapse; font-size:12px; }
  .vta-table thead th { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; padding:9px 10px; text-align:left; white-space:nowrap; }
  .vta-table tbody td { padding:9px 10px; border-bottom:1px solid #fff1f2; }
  .vta-table tfoot td { padding:9px 10px; font-weight:800; background:#fff9fb; border-top:2px solid #fff1f2; }

  /* ---- Historial ---- */
  .hist-row { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:1px solid #fff1f2; font-size:12px; gap:10px; flex-wrap:wrap; }
  .hist-row:last-child { border:none; }
</style>

{{-- Alertas --}}
@if(session('status'))
  <div style="background:#d5f5e3; border-left:4px solid #27ae60; border-radius:8px; padding:11px 16px; margin-bottom:18px; font-size:13px; color:#1e8449; font-weight:600;">
    <i class="fa fa-check-circle"></i> {{ session('status') }}
  </div>
@endif
@if(session('error') || $errors->any())
  <div style="background:#fadbd8; border-left:4px solid #e74c3c; border-radius:8px; padding:11px 16px; margin-bottom:18px; font-size:13px; color:#922b21; font-weight:600;">
    <i class="fa fa-exclamation-circle"></i> {{ session('error') ?? $errors->first() }}
  </div>
@endif

{{-- Estado actual de la caja --}}
@if($cajaAbierta)
<div class="caja-estado caja-abierta">
  <span class="ce-icon"><i class="fa fa-unlock"></i></span>
  <div style="flex:1;">
    <div class="ce-title"><i class="fa fa-check-circle"></i> Caja abierta</div>
    <div class="ce-sub">
      Abierta el {{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d/m/Y') }}
      a las {{ $cajaAbierta->hora_apertura }}
      · Monto inicial: <strong>{{ number_format($cajaAbierta->monto_inicial,0,',','.') }} Gs.</strong>
    </div>
  </div>
  <a href="{{ url('admin/caja/imprimir/'.$cajaAbierta->id) }}" target="_blank"
     style="background:#1e8449; color:#fff; border-radius:8px; padding:8px 16px; font-size:12px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
    <i class="fa fa-print"></i> Imprimir arqueo
  </a>
</div>

{{-- Métricas del día --}}
<div class="tres-col">
  <div class="metric-card">
    <div class="mc-num" style="color:#be185d;">{{ number_format($totalesDia['total'],0,',','.') }} Gs.</div>
    <div class="mc-lbl">Total ventas del día</div>
  </div>
  <div class="metric-card">
    <div class="mc-num" style="color:#27ae60;">{{ number_format($totalesDia['efectivo'],0,',','.') }} Gs.</div>
    <div class="mc-lbl">En efectivo</div>
  </div>
  <div class="metric-card">
    <div class="mc-num" style="color:#2980b9;">{{ number_format($totalesDia['transferencia'] + $totalesDia['tarjeta'],0,',','.') }} Gs.</div>
    <div class="mc-lbl">Transferencias / Tarjeta</div>
  </div>
</div>

<div class="dos-col">

  {{-- Ventas del día --}}
  <div class="caja-panel">
    <div class="cp-head">
      <h4><i class="fa fa-shopping-bag"></i> Ventas del día ({{ $ventasDelDia->count() }})</h4>
    </div>
    <div style="overflow-x:auto;">
      @if($ventasDelDia->isEmpty())
        <p style="text-align:center; color:#aaa; padding:24px; font-size:13px;">Sin ventas registradas hoy.</p>
      @else
      <table class="vta-table">
        <thead><tr><th>Nro.</th><th>Cliente</th><th>Método</th><th style="text-align:right;">Total</th></tr></thead>
        <tbody>
          @foreach($ventasDelDia as $v)
          <tr>
            <td style="font-weight:700; color:#be185d;">#{{ $v->num_comprobante }}</td>
            <td>{{ $v->cliente }}</td>
            <td style="font-size:11px; color:#888;">{{ $v->metodo_pago ?? 'Efectivo' }}</td>
            <td style="text-align:right; font-weight:700;">{{ number_format($v->total_venta,0,',','.') }} Gs.</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right; color:#888;">TOTAL:</td>
            <td style="text-align:right; color:#be185d;">{{ number_format($totalesDia['total'],0,',','.') }} Gs.</td>
          </tr>
        </tfoot>
      </table>
      @endif
    </div>
  </div>

  {{-- Arqueo manual + cierre --}}
  <div>
    {{-- Agregar movimiento --}}
    <div class="caja-panel" style="margin-bottom:16px;">
      <div class="cp-head">
        <h4><i class="fa fa-plus-circle"></i> Registrar movimiento manual</h4>
      </div>
      <div class="cp-body">
        <form method="POST" action="{{ url('admin/caja/movimiento') }}" class="cj-form">
          {{ csrf_field() }}
          <input type="hidden" name="caja_id" value="{{ $cajaAbierta->id }}">
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div class="fg">
              <label>Tipo</label>
              <select name="tipo" required>
                <option value="ingreso">Ingreso</option>
                <option value="egreso">Egreso</option>
              </select>
            </div>
            <div class="fg">
              <label>Método</label>
              <select name="metodo">
                <option>Efectivo</option>
                <option>Transferencia</option>
                <option>Tarjeta</option>
              </select>
            </div>
          </div>
          <div class="fg">
            <label>Descripción <span style="color:#be185d;">*</span></label>
            <input type="text" name="descripcion" required placeholder="Ej: Pago de proveedor, retiro de efectivo...">
          </div>
          <div class="fg">
            <label>Monto (Gs.) <span style="color:#be185d;">*</span></label>
            <input type="number" name="monto" required min="1" placeholder="0">
          </div>
          <button type="submit" class="btn-add-mov"><i class="fa fa-plus"></i> Registrar</button>
        </form>
      </div>
    </div>

    {{-- Cerrar caja --}}
    <div class="caja-panel" style="border-color:#f5b7b1;">
      <div class="cp-head" style="background:#fff5f5;">
        <h4><i class="fa fa-lock" style="color:#e74c3c;"></i> <span style="color:#c0392b;">Cerrar caja</span></h4>
      </div>
      <div class="cp-body">
        <form method="POST" action="{{ url('admin/caja/cerrar/'.$cajaAbierta->id) }}" class="cj-form"
          onsubmit="return confirm('¿Confirmar el cierre de caja?')">
          {{ csrf_field() }}
          <div class="fg">
            <label>Monto final en caja (Gs.) <span style="color:#be185d;">*</span></label>
            <input type="number" name="monto_final" required min="0" placeholder="Conteo físico del efectivo">
          </div>
          <div class="fg">
            <label>Observación de cierre</label>
            <textarea name="observacion_cierre" rows="2" placeholder="Diferencias, novedades..."></textarea>
          </div>
          <button type="submit" class="btn-cerrar"><i class="fa fa-lock"></i> Cerrar caja</button>
        </form>
      </div>
    </div>
  </div>

</div>

{{-- Movimientos del arqueo --}}
@if($movimientos->count())
<div class="caja-panel" style="margin-bottom:20px;">
  <div class="cp-head">
    <h4><i class="fa fa-list"></i> Movimientos manuales registrados</h4>
    @php
      $totalIng = $movimientos->where('tipo','ingreso')->sum('monto');
      $totalEgr = $movimientos->where('tipo','egreso')->sum('monto');
    @endphp
    <div style="font-size:12px; color:#888;">
      Ingresos: <strong style="color:#27ae60;">+{{ number_format($totalIng,0,',','.') }} Gs.</strong>
      &nbsp;·&nbsp;
      Egresos: <strong style="color:#e74c3c;">−{{ number_format($totalEgr,0,',','.') }} Gs.</strong>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="mov-table">
      <thead><tr><th>Hora</th><th>Tipo</th><th>Descripción</th><th>Método</th><th style="text-align:right;">Monto</th><th></th></tr></thead>
      <tbody>
        @foreach($movimientos as $m)
        <tr>
          <td style="color:#aaa; white-space:nowrap;">{{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}</td>
          <td>
            @if($m->tipo === 'ingreso') <span class="badge-ing"><i class="fa fa-arrow-up"></i> Ingreso</span>
            @else <span class="badge-egr"><i class="fa fa-arrow-down"></i> Egreso</span>
            @endif
          </td>
          <td>{{ $m->descripcion }}</td>
          <td style="color:#888; font-size:11px;">{{ $m->metodo }}</td>
          <td style="text-align:right; font-weight:700; color:{{ $m->tipo==='ingreso' ? '#27ae60' : '#e74c3c' }};">
            {{ $m->tipo === 'ingreso' ? '+' : '−' }}{{ number_format($m->monto,0,',','.') }} Gs.
          </td>
          <td>
            <form method="POST" action="{{ url('admin/caja/movimiento/'.$m->id) }}" style="display:inline;"
              onsubmit="return confirm('¿Eliminar este movimiento?')">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <button type="submit" class="btn-del-mov"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@else
{{-- CAJA CERRADA — mostrar formulario de apertura --}}
<div class="caja-estado caja-cerrada" style="margin-bottom:22px;">
  <span class="ce-icon"><i class="fa fa-lock"></i></span>
  <div>
    <div class="ce-title">Caja cerrada</div>
    <div class="ce-sub">No hay caja abierta actualmente. Abrí una nueva caja para comenzar la jornada.</div>
  </div>
</div>

<div style="max-width:520px;">
  <div class="caja-panel">
    <div class="cp-head">
      <h4><i class="fa fa-unlock"></i> Abrir nueva caja</h4>
    </div>
    <div class="cp-body">
      <form method="POST" action="{{ url('admin/caja/abrir') }}" class="cj-form">
        {{ csrf_field() }}
        <div class="fg">
          <label>Monto inicial en caja (Gs.) <span style="color:#be185d;">*</span></label>
          <input type="number" name="monto_inicial" required min="0" placeholder="Ej: 500000" value="{{ old('monto_inicial') }}">
          <span style="font-size:11px; color:#bbb; margin-top:3px;">Efectivo con el que iniciás la jornada.</span>
        </div>
        <div class="fg">
          <label>Observación (opcional)</label>
          <input type="text" name="observacion" placeholder="Novedades del turno..." value="{{ old('observacion') }}">
        </div>
        <button type="submit" class="btn-abrir"><i class="fa fa-unlock"></i> Abrir caja del día</button>
      </form>
    </div>
  </div>
</div>
@endif

{{-- Historial de cajas anteriores --}}
@if($historial->count())
<div class="caja-panel" style="margin-top:20px;">
  <div class="cp-head">
    <h4><i class="fa fa-history"></i> Historial de cajas anteriores</h4>
  </div>
  <div class="cp-body" style="padding:8px 18px;">
    @foreach($historial as $h)
    <div class="hist-row">
      <div>
        <b style="font-size:13px;">{{ \Carbon\Carbon::parse($h->fecha_apertura)->format('d/m/Y') }}</b>
        <span style="color:#aaa; font-size:11px; margin-left:8px;">{{ $h->hora_apertura }} → {{ $h->hora_cierre }}</span>
      </div>
      <div style="font-size:12px; color:#888;">
        Inicial: <b>{{ number_format($h->monto_inicial,0,',','.') }} Gs.</b>
        · Final: <b>{{ number_format($h->monto_final ?? 0,0,',','.') }} Gs.</b>
      </div>
      <a href="{{ url('admin/caja/imprimir/'.$h->id) }}" target="_blank"
         style="background:#1a252f; color:#fff; border-radius:6px; padding:5px 12px; font-size:11px; font-weight:700; text-decoration:none;">
        <i class="fa fa-print"></i> Imprimir
      </a>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
