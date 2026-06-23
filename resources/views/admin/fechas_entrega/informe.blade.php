<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Informe de Fechas de Entrega — AndylandPy</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Segoe UI', Arial, sans-serif; font-size:13px; color:#222; background:#f0f0f0; }

    /* Barra de acciones */
    .no-print { background:#fff; border-bottom:2px solid #9d174d; padding:12px 28px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .no-print .np-title { font-weight:800; font-size:14px; color:#9d174d; flex:1; }
    .btn-np { display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; border:none; text-decoration:none; }
    .btn-print  { background:linear-gradient(135deg,#9d174d,#7f1d3e); color:#fff; }
    .btn-volver { background:#f5f5f5; color:#555; }

    /* Documento */
    .doc { max-width:820px; margin:28px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.12); }

    /* Header */
    .doc-header { background:linear-gradient(135deg,#9d174d,#7f1d3e); color:#fff; padding:24px 32px; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:14px; }
    .doc-header .brand { font-size:22px; font-weight:900; }
    .doc-header .brand-sub { font-size:12px; color:rgba(255,255,255,.7); margin-top:2px; }
    .doc-header .doc-info { text-align:right; }
    .doc-header .doc-titulo { font-size:17px; font-weight:900; }
    .doc-header .doc-sub { font-size:12px; color:rgba(255,255,255,.7); margin-top:3px; }

    /* Resumen */
    .resumen { display:grid; grid-template-columns:repeat(3,1fr); border-bottom:2px solid #fecdd3; }
    .res-cell { padding:16px 20px; text-align:center; border-right:1px solid #fecdd3; }
    .res-cell:last-child { border:none; }
    .res-num { font-size:28px; font-weight:900; color:#9d174d; }
    .res-lbl { font-size:11px; color:#aaa; font-weight:600; text-transform:uppercase; margin-top:2px; }

    /* Sección fecha */
    .fecha-section { border-bottom:2px solid #fecdd3; }
    .fecha-head { background:#fef2f8; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #fecdd3; }
    .fecha-head .fh-fecha { font-size:15px; font-weight:800; color:#9d174d; }
    .fecha-head .fh-dia   { font-size:12px; color:#888; margin-top:2px; }
    .fh-badges { display:flex; gap:8px; align-items:center; }
    .b-activa   { background:#d5f5e3; color:#1e8449; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .b-inactiva { background:#f5f5f5; color:#aaa; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .b-cant { background:#9d174d; color:#fff; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .b-sin { color:#ccc; font-size:12px; font-style:italic; }

    /* Tabla pedidos */
    table { width:100%; border-collapse:collapse; }
    thead th { background:#1e293b; color:#fff; padding:8px 14px; font-size:11px; font-weight:700; text-align:left; }
    tbody td { padding:9px 14px; border-bottom:1px solid #fef2f8; font-size:12px; color:#444; }
    tbody tr:last-child td { border:none; }
    .estado-P { background:#fef9e7; color:#b7770d; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }
    .estado-A { background:#d5f5e3; color:#1e8449; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }

    /* Sin pedidos */
    .sin-pedidos { padding:14px 20px; color:#ccc; font-style:italic; font-size:13px; text-align:center; }

    /* Firmas */
    .firmas { display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px; padding:24px 32px; }
    .firma-box { text-align:center; }
    .firma-linea { border-top:1px solid #333; padding-top:6px; font-size:11px; color:#666; margin-top:44px; }

    /* Footer */
    .doc-footer { background:#1e293b; color:rgba(255,255,255,.5); text-align:center; padding:14px; font-size:11px; }
    .doc-footer strong { color:#9d174d; }

    @media print {
      body { background:#fff; }
      .no-print { display:none !important; }
      .doc { margin:0; border-radius:0; box-shadow:none; }
      .doc-header { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
      thead th { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    }
  </style>
</head>
<body>

<div class="no-print">
  <span class="np-title"><i class="fa fa-calendar-check-o"></i> Informe de Fechas de Entrega</span>
  <button onclick="window.print()" class="btn-np btn-print"><i class="fa fa-print"></i> Imprimir / PDF</button>
  <a href="{{ url('admin/fechas-entrega') }}" class="btn-np btn-volver"><i class="fa fa-arrow-left"></i> Volver</a>
</div>

<div class="doc">

  {{-- Header --}}
  <div class="doc-header">
    <div>
      <div class="brand">AndylandPy.</div>
      <div class="brand-sub">Regalos y souvenirs personalizados · San Lorenzo, Paraguay</div>
    </div>
    <div class="doc-info">
      <div class="doc-titulo"><i class="fa fa-calendar-check-o"></i> Informe de Fechas de Entrega</div>
      <div class="doc-sub">Módulo de Estimación de Fecha de Entrega</div>
      <div class="doc-sub" style="margin-top:4px;">Generado: {{ now()->format('d/m/Y H:i') }} hs</div>
    </div>
  </div>

  {{-- Resumen --}}
  <div class="resumen">
    <div class="res-cell">
      <div class="res-num">{{ $fechas->count() }}</div>
      <div class="res-lbl">Fechas cargadas</div>
    </div>
    <div class="res-cell">
      <div class="res-num" style="color:#27ae60;">{{ $totalConFecha }}</div>
      <div class="res-lbl">Pedidos con fecha asignada</div>
    </div>
    <div class="res-cell">
      <div class="res-num" style="color:#888;">{{ $totalSinFecha }}</div>
      <div class="res-lbl">Pedidos sin fecha</div>
    </div>
  </div>

  {{-- Fechas y sus pedidos --}}
  @foreach($fechas as $f)
  @php
    $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $cf    = \Carbon\Carbon::parse($f->fecha);
    $diaStr = $dias[$cf->dayOfWeek].', '.$cf->day.' de '.$meses[$cf->month-1].' de '.$cf->year;
  @endphp
  <div class="fecha-section">
    <div class="fecha-head">
      <div>
        <div class="fh-fecha">{{ $cf->format('d/m/Y') }}</div>
        <div class="fh-dia">{{ $diaStr }}{{ $f->descripcion ? ' · '.$f->descripcion : '' }}</div>
      </div>
      <div class="fh-badges">
        @if($f->activo) <span class="b-activa"><i class="fa fa-check"></i> Activa</span>
        @else <span class="b-inactiva">Inactiva</span> @endif
        @if($f->pedidos->count() > 0)
          <span class="b-cant"><i class="fa fa-shopping-bag"></i> {{ $f->pedidos->count() }} pedido(s)</span>
        @endif
      </div>
    </div>

    @if($f->pedidos->isEmpty())
      <div class="sin-pedidos"><i class="fa fa-inbox"></i> Sin pedidos asignados a esta fecha.</div>
    @else
    <table>
      <thead>
        <tr>
          <th>Nro. Pedido</th>
          <th>Cliente</th>
          <th>Teléfono</th>
          <th>Horario</th>
          <th>Tipo entrega</th>
          <th style="text-align:right;">Total</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @foreach($f->pedidos as $p)
        <tr>
          <td style="font-weight:700; color:#9d174d;">#{{ $p->num_comprobante }}</td>
          <td><b>{{ $p->cliente }}</b></td>
          <td>{{ $p->telefono ?? '—' }}</td>
          <td style="color:#888; white-space:nowrap;">{{ $p->hora_entrega ?? 'A coordinar' }}</td>
          <td><i class="fa fa-{{ $p->tipo_distribucion === 'Delivery' ? 'truck' : 'store' }}"></i> {{ $p->tipo_distribucion ?? '—' }}</td>
          <td style="text-align:right; font-weight:700; color:#9d174d;">{{ number_format($p->total_venta,0,',','.') }} Gs.</td>
          <td>
            @if($p->estado==='A') <span class="estado-A"><i class="fa fa-check"></i> Aprobado</span>
            @else <span class="estado-P"><i class="fa fa-clock-o"></i> Pendiente</span>
            @endif
          </td>
        </tr>
        @endforeach
        <tr style="background:#fef2f8;">
          <td colspan="5" style="text-align:right; font-weight:700; color:#888; padding:8px 14px;">SUBTOTAL:</td>
          <td style="text-align:right; font-weight:900; color:#9d174d; padding:8px 14px;">
            {{ number_format($f->pedidos->sum('total_venta'),0,',','.') }} Gs.
          </td>
          <td></td>
        </tr>
      </tbody>
    </table>
    @endif
  </div>
  @endforeach

  {{-- Firmas --}}
  <div class="firmas">
    <div class="firma-box"><div class="firma-linea">Responsable de entregas</div></div>
    <div class="firma-box"><div class="firma-linea">Supervisor/a</div></div>
    <div class="firma-box"><div class="firma-linea">Gerencia</div></div>
  </div>

  <div class="doc-footer">
    <strong>AndylandPy.</strong> — Regalos y souvenirs personalizados · Paraguay
  </div>

</div>
</body>
</html>
