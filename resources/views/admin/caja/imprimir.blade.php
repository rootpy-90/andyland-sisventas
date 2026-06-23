<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Arqueo de Caja — {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Segoe UI', Arial, sans-serif; font-size:13px; color:#222; background:#f0f0f0; }

    .no-print {
      background:#fff; border-bottom:2px solid #e91e8c; padding:12px 24px;
      display:flex; align-items:center; gap:12px;
    }
    .no-print .np-title { font-weight:800; font-size:14px; color:#880e4f; flex:1; }
    .btn-np {
      display:inline-flex; align-items:center; gap:6px;
      padding:8px 18px; border-radius:8px; font-size:13px;
      font-weight:700; cursor:pointer; border:none; text-decoration:none; transition:opacity .18s;
    }
    .btn-np:hover { opacity:.85; }
    .btn-print  { background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; }
    .btn-volver { background:#f5f5f5; color:#555; }

    .wrap { max-width:780px; margin:28px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.12); }

    /* Header */
    .arq-header { background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; padding:24px 32px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; }
    .arq-header .brand { font-size:22px; font-weight:900; }
    .arq-header .brand-sub { font-size:12px; color:rgba(255,255,255,.75); margin-top:2px; }
    .arq-header .doc-info { text-align:right; }
    .arq-header .doc-info p { margin:0; font-size:13px; color:rgba(255,255,255,.8); }
    .arq-header .doc-info .doc-titulo { font-size:18px; font-weight:900; color:#fff; }

    /* Estado */
    .arq-estado { padding:10px 32px; border-bottom:1px solid #fce4ec; background:#fff9fb; font-size:13px; font-weight:700; }
    .arq-estado.abierta { color:#1e8449; }
    .arq-estado.cerrada { color:#922b21; }

    /* Sección */
    .section { padding:20px 32px; border-bottom:1px solid #fce4ec; }
    .section h4 { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#e91e8c; margin-bottom:14px; padding-bottom:6px; border-bottom:1px solid #fce4ec; }

    /* Grid datos */
    .data-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
    .data-cell { }
    .data-cell .dc-lbl { font-size:11px; color:#aaa; font-weight:600; }
    .data-cell .dc-val { font-size:14px; font-weight:700; color:#2c3e50; margin-top:2px; }

    /* Tablas */
    table { width:100%; border-collapse:collapse; }
    thead th { background:#1a0a12; color:#fff; padding:9px 12px; font-size:12px; font-weight:700; text-align:left; }
    tbody td { padding:9px 12px; border-bottom:1px solid #fce4ec; font-size:12px; }
    tfoot td { padding:9px 12px; font-weight:800; background:#fff9fb; border-top:2px solid #fce4ec; }

    /* Resumen financiero */
    .resumen-box { background:#fff9fb; border:1px solid #fce4ec; border-radius:10px; padding:16px 20px; }
    .res-row { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #fce4ec; font-size:13px; }
    .res-row:last-child { border:none; }
    .res-row .rl { color:#888; }
    .res-row .rv { font-weight:700; color:#2c3e50; }
    .res-total { display:flex; justify-content:space-between; align-items:center; padding:10px 0 4px; margin-top:6px; border-top:2px solid #e91e8c; }
    .res-total .rl { font-size:14px; font-weight:800; color:#2c3e50; }
    .res-total .rv { font-size:20px; font-weight:900; color:#e91e8c; }

    /* Firma */
    .firmas { display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px; padding:24px 32px; }
    .firma-box { text-align:center; }
    .firma-linea { border-top:1px solid #333; padding-top:6px; font-size:11px; color:#666; margin-top:44px; }

    /* Footer */
    .arq-footer { background:#1a0a12; color:rgba(255,255,255,.55); text-align:center; padding:14px; font-size:11px; }
    .arq-footer strong { color:#e91e8c; }

    @media print {
      body { background:#fff; }
      .no-print { display:none !important; }
      .wrap { margin:0; border-radius:0; box-shadow:none; }
      thead th { -webkit-print-color-adjust:exact; print-color-adjust:exact; background:#1a0a12 !important; }
      .arq-header { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    }
  </style>
</head>
<body>

<div class="no-print">
  <span class="np-title"><i class="fa fa-print" style="color:#e91e8c;"></i> Arqueo de Caja — {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }}</span>
  <button onclick="window.print()" class="btn-np btn-print"><i class="fa fa-print"></i> Imprimir / PDF</button>
  <a href="{{ url('admin/caja') }}" class="btn-np btn-volver"><i class="fa fa-arrow-left"></i> Volver</a>
</div>

<div class="wrap">

  {{-- Header --}}
  <div class="arq-header">
    <div>
      <div class="brand">AndylandPy.</div>
      <div class="brand-sub">Regalos y souvenirs personalizados</div>
    </div>
    <div class="doc-info">
      <p class="doc-titulo">Arqueo de Caja</p>
      <p>Fecha: {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }}</p>
      <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
  </div>

  {{-- Estado --}}
  <div class="arq-estado {{ $caja->estado }}">
    <i class="fa fa-{{ $caja->estado === 'abierta' ? 'unlock' : 'lock' }}"></i>
    Caja {{ strtoupper($caja->estado) }}
  </div>

  {{-- Datos de apertura/cierre --}}
  <div class="section">
    <h4>Datos de la caja</h4>
    <div class="data-grid">
      <div class="data-cell">
        <p class="dc-lbl">Fecha apertura</p>
        <p class="dc-val">{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }} {{ $caja->hora_apertura }}</p>
      </div>
      <div class="data-cell">
        <p class="dc-lbl">Monto inicial</p>
        <p class="dc-val">{{ number_format($caja->monto_inicial,0,',','.') }} Gs.</p>
      </div>
      @if($caja->observacion)
      <div class="data-cell">
        <p class="dc-lbl">Observación apertura</p>
        <p class="dc-val" style="font-size:12px;">{{ $caja->observacion }}</p>
      </div>
      @endif
      @if($caja->estado === 'cerrada')
      <div class="data-cell">
        <p class="dc-lbl">Fecha cierre</p>
        <p class="dc-val">{{ \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') }}</p>
      </div>
      <div class="data-cell">
        <p class="dc-lbl">Monto final declarado</p>
        <p class="dc-val" style="color:#e91e8c;">{{ number_format($caja->monto_final ?? 0,0,',','.') }} Gs.</p>
      </div>
      @if($caja->observacion_cierre)
      <div class="data-cell">
        <p class="dc-lbl">Observación cierre</p>
        <p class="dc-val" style="font-size:12px;">{{ $caja->observacion_cierre }}</p>
      </div>
      @endif
      @endif
    </div>
  </div>

  {{-- Ventas del día --}}
  <div class="section">
    <h4>Ventas del día ({{ $ventasDelDia->count() }} pedidos)</h4>
    @if($ventasDelDia->isEmpty())
      <p style="color:#aaa; font-style:italic;">Sin ventas registradas.</p>
    @else
    <table>
      <thead><tr><th>Nro.</th><th>Cliente</th><th>Hora</th><th>Método</th><th style="text-align:right;">Total</th></tr></thead>
      <tbody>
        @foreach($ventasDelDia as $v)
        <tr>
          <td style="font-weight:700; color:#e91e8c;">#{{ $v->num_comprobante }}</td>
          <td>{{ $v->cliente }}</td>
          <td style="color:#aaa;">{{ \Carbon\Carbon::parse($v->fecha_hora)->format('H:i') }}</td>
          <td style="color:#888; font-size:11px;">{{ $v->metodo_pago ?? 'Efectivo' }}</td>
          <td style="text-align:right; font-weight:700;">{{ number_format($v->total_venta,0,',','.') }} Gs.</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr><td colspan="4" style="text-align:right; color:#888;">TOTAL VENTAS:</td>
        <td style="text-align:right; color:#e91e8c;">{{ number_format($totales['total'],0,',','.') }} Gs.</td></tr>
      </tfoot>
    </table>
    @endif
  </div>

  {{-- Movimientos manuales --}}
  @if($movimientos->count())
  <div class="section">
    <h4>Movimientos manuales del arqueo</h4>
    <table>
      <thead><tr><th>Hora</th><th>Tipo</th><th>Descripción</th><th>Método</th><th style="text-align:right;">Monto</th></tr></thead>
      <tbody>
        @foreach($movimientos as $m)
        <tr>
          <td style="color:#aaa;">{{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}</td>
          <td style="font-weight:700; color:{{ $m->tipo==='ingreso' ? '#27ae60' : '#e74c3c' }};">{{ ucfirst($m->tipo) }}</td>
          <td>{{ $m->descripcion }}</td>
          <td style="color:#888; font-size:11px;">{{ $m->metodo }}</td>
          <td style="text-align:right; font-weight:700; color:{{ $m->tipo==='ingreso' ? '#27ae60' : '#e74c3c' }};">
            {{ $m->tipo==='ingreso' ? '+':'-' }}{{ number_format($m->monto,0,',','.') }} Gs.
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  {{-- Resumen financiero --}}
  <div class="section">
    <h4>Resumen financiero del día</h4>
    <div class="resumen-box">
      <div class="res-row"><span class="rl">Monto inicial</span><span class="rv">{{ number_format($caja->monto_inicial,0,',','.') }} Gs.</span></div>
      <div class="res-row"><span class="rl">Ventas en efectivo</span><span class="rv">+{{ number_format($totales['efectivo'],0,',','.') }} Gs.</span></div>
      <div class="res-row"><span class="rl">Ventas por transferencia</span><span class="rv">+{{ number_format($totales['transferencia'],0,',','.') }} Gs.</span></div>
      <div class="res-row"><span class="rl">Ventas por tarjeta</span><span class="rv">+{{ number_format($totales['tarjeta'],0,',','.') }} Gs.</span></div>
      @if($ingresosArqueo > 0)<div class="res-row"><span class="rl">Ingresos manuales</span><span class="rv" style="color:#27ae60;">+{{ number_format($ingresosArqueo,0,',','.') }} Gs.</span></div>@endif
      @if($egresosArqueo > 0)<div class="res-row"><span class="rl">Egresos manuales</span><span class="rv" style="color:#e74c3c;">−{{ number_format($egresosArqueo,0,',','.') }} Gs.</span></div>@endif
      <div class="res-total">
        <span class="rl">TOTAL EN CAJA ESPERADO</span>
        <span class="rv">{{ number_format($caja->monto_inicial + $totales['efectivo'] + $ingresosArqueo - $egresosArqueo, 0, ',', '.') }} Gs.</span>
      </div>
      @if($caja->estado === 'cerrada' && $caja->monto_final !== null)
      @php $diff = $caja->monto_final - ($caja->monto_inicial + $totales['efectivo'] + $ingresosArqueo - $egresosArqueo); @endphp
      <div class="res-row" style="margin-top:8px;">
        <span class="rl">Monto final declarado</span>
        <span class="rv">{{ number_format($caja->monto_final,0,',','.') }} Gs.</span>
      </div>
      <div class="res-row">
        <span class="rl">Diferencia</span>
        <span class="rv" style="color:{{ $diff == 0 ? '#27ae60' : ($diff > 0 ? '#2980b9' : '#e74c3c') }};">
          {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff,0,',','.') }} Gs.
        </span>
      </div>
      @endif
    </div>
  </div>

  {{-- Firmas --}}
  <div class="firmas">
    <div class="firma-box"><div class="firma-linea">Cajero/a responsable</div></div>
    <div class="firma-box"><div class="firma-linea">Supervisor/a</div></div>
    <div class="firma-box"><div class="firma-linea">Gerencia</div></div>
  </div>

  <div class="arq-footer">
    <strong>AndylandPy.</strong> — Regalos y souvenirs personalizados · Paraguay
  </div>

</div>
</body>
</html>
