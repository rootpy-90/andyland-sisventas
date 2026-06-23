<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ticket #{{ $venta->num_comprobante }} — AndylandPy</title>
  <style>
    /* ===== RESET ===== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ===== BASE ===== */
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 13px;
      color: #222;
      background: #f0f0f0;
    }

    /* ===== ACCIONES (no se imprimen) ===== */
    .no-print {
      background: #fff;
      border-bottom: 2px solid #e91e8c;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .no-print .np-title { font-weight: 800; font-size: 14px; color: #880e4f; flex: 1; }
    .btn-np {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 18px; border-radius: 8px; font-size: 13px;
      font-weight: 700; cursor: pointer; border: none; text-decoration: none;
      transition: opacity 0.18s;
    }
    .btn-np:hover { opacity: 0.85; text-decoration: none; }
    .btn-print  { background: linear-gradient(135deg, #e91e8c, #ad1457); color: #fff; }
    .btn-volver { background: #f5f5f5; color: #555; }

    /* ===== TICKET WRAPPER ===== */
    .ticket-wrap {
      max-width: 780px;
      margin: 28px auto;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.12);
    }

    /* ===== HEADER ===== */
    .ticket-header {
      background: linear-gradient(135deg, #e91e8c, #ad1457);
      color: #fff;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      flex-wrap: wrap;
    }
    .th-brand { display: flex; align-items: center; gap: 14px; }
    .th-brand img {
      width: 64px; height: 64px;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.5);
      object-fit: cover;
      background: #fff;
    }
    .th-brand .brand-name { font-size: 24px; font-weight: 900; letter-spacing: -0.5px; }
    .th-brand .brand-sub  { font-size: 12px; color: rgba(255,255,255,0.75); margin-top: 2px; }

    .th-num { text-align: right; }
    .th-num .comp-type { font-size: 13px; color: rgba(255,255,255,0.8); margin-bottom: 4px; }
    .th-num .comp-nro  { font-size: 28px; font-weight: 900; letter-spacing: 1px; }

    /* ===== ESTADO BADGE ===== */
    .estado-wrap { padding: 10px 32px; border-bottom: 1px solid #fce4ec; background: #fff9fb; display: flex; align-items: center; gap: 10px; }
    .estado-badge { padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; }
    .estado-A { background: #d5f5e3; color: #1e8449; }
    .estado-P { background: #fef9e7; color: #b7770d; border: 1px solid #f9e79f; }
    .estado-C { background: #fadbd8; color: #922b21; }

    /* ===== INFO GRID ===== */
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-bottom: 2px solid #fce4ec;
    }
    .info-section { padding: 20px 28px; }
    .info-section:first-child { border-right: 1px solid #fce4ec; }
    .info-section h4 {
      font-size: 11px; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.8px; color: #e91e8c; margin-bottom: 12px;
      padding-bottom: 6px; border-bottom: 1px solid #fce4ec;
    }
    .info-row { display: flex; gap: 8px; margin-bottom: 7px; font-size: 13px; }
    .info-row .ir-label { color: #999; min-width: 110px; flex-shrink: 0; font-weight: 600; }
    .info-row .ir-value { color: #2c3e50; font-weight: 700; }

    /* ===== TABLA DE PRODUCTOS ===== */
    .products-section { padding: 0; }
    .products-section h4 {
      font-size: 11px; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.8px; color: #e91e8c;
      padding: 14px 28px 10px; border-bottom: 1px solid #fce4ec;
    }

    .prod-table { width: 100%; border-collapse: collapse; }
    .prod-table thead tr { background: #1a0a12; }
    .prod-table thead th {
      color: #fff; padding: 11px 14px; font-size: 12px; font-weight: 700;
      text-align: left; white-space: nowrap;
    }
    .prod-table thead th.right { text-align: right; }
    .prod-table thead th.center { text-align: center; }

    .prod-table tbody tr { border-bottom: 1px solid #fce4ec; }
    .prod-table tbody tr:nth-child(even) { background: #fff9fb; }
    .prod-table tbody td { padding: 12px 14px; font-size: 13px; color: #333; }
    .prod-table tbody td.right { text-align: right; }
    .prod-table tbody td.center { text-align: center; font-weight: 700; }
    .prod-table tbody td.cod { color: #aaa; font-size: 11px; font-family: monospace; }
    .prod-table tbody td.nombre { font-weight: 700; color: #2c3e50; }
    .prod-table tbody td.precio { color: #555; }
    .prod-table tbody td.subtotal { font-weight: 800; color: #e91e8c; text-align: right; }

    /* ===== TOTALES ===== */
    .totales-wrap {
      background: #fff9fb;
      border-top: 2px solid #fce4ec;
      padding: 18px 28px;
      display: flex;
      justify-content: flex-end;
    }
    .totales-box { min-width: 260px; }
    .total-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 6px 0; font-size: 13px; border-bottom: 1px solid #fce4ec;
    }
    .total-row:last-child { border: none; }
    .total-row .tl { color: #888; font-weight: 600; }
    .total-row .tv { font-weight: 700; color: #2c3e50; }
    .total-final-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 12px 0 6px; margin-top: 6px; border-top: 2px solid #e91e8c;
    }
    .total-final-row .tl { font-size: 15px; font-weight: 800; color: #2c3e50; }
    .total-final-row .tv { font-size: 22px; font-weight: 900; color: #e91e8c; }

    /* ===== FOOTER ===== */
    .ticket-footer {
      background: #1a0a12;
      color: rgba(255,255,255,0.6);
      text-align: center;
      padding: 16px;
      font-size: 12px;
    }
    .ticket-footer strong { color: #e91e8c; }
    .ticket-footer .gracias { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }

    /* ===== PRINT ===== */
    @media print {
      body { background: #fff; }
      .no-print { display: none !important; }
      .ticket-wrap { margin: 0; border-radius: 0; box-shadow: none; }
      .prod-table thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #1a0a12 !important; }
      .ticket-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: linear-gradient(135deg, #e91e8c, #ad1457) !important; }
    }
  </style>
</head>
<body>

  {{-- BARRA DE ACCIONES --}}
  <div class="no-print">
    <span class="np-title">
      <i class="fa fa-print" style="color:#e91e8c;"></i>
      Vista previa del ticket — Pedido #{{ $venta->num_comprobante }}
    </span>
    <button onclick="window.print()" class="btn-np btn-print">
      🖨️ Imprimir / Guardar PDF
    </button>
    <a href="{{ url('ventas/venta') }}" class="btn-np btn-volver">
      ← Volver a Ventas
    </a>
  </div>

  <div class="ticket-wrap">

    {{-- HEADER --}}
    <div class="ticket-header">
      <div class="th-brand">
        <div style="width:60px; height:60px; border-radius:50%; background:rgba(255,255,255,0.2); border:3px solid rgba(255,255,255,0.4); display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:900; color:#fff; flex-shrink:0;">A</div>
        <div>
          <div class="brand-name">AndylandPy.</div>
          <div class="brand-sub">Regalos y souvenirs personalizados</div>
        </div>
      </div>
      <div class="th-num">
        <p class="comp-type">{{ $venta->tipo_facturacion ?? $venta->tipo_comprobante }}</p>
        <p class="comp-nro">#{{ $venta->num_comprobante }}</p>
      </div>
    </div>

    {{-- ESTADO --}}
    <div class="estado-wrap">
      <span style="font-size:12px; color:#888; font-weight:600;">Estado del pedido:</span>
      <span class="estado-badge estado-{{ $venta->estado }}">
        @if($venta->estado=='A') ✓ Aprobado
        @elseif($venta->estado=='P') ⏳ Pendiente
        @else ✕ Cancelado
        @endif
      </span>
    </div>

    {{-- INFO GRID --}}
    <div class="info-grid">

      {{-- Datos del pedido --}}
      <div class="info-section">
        <h4>📋 Datos del pedido</h4>
        <div class="info-row">
          <span class="ir-label">Fecha:</span>
          <span class="ir-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
          <span class="ir-label">Hora:</span>
          <span class="ir-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }} hs</span>
        </div>
        <div class="info-row">
          <span class="ir-label">Nro. de venta:</span>
          <span class="ir-value">#{{ $venta->num_comprobante }}</span>
        </div>
        <div class="info-row">
          <span class="ir-label">Tipo:</span>
          <span class="ir-value">{{ $venta->tipo_facturacion ?? $venta->tipo_comprobante }}</span>
        </div>
        <div class="info-row">
          <span class="ir-label">Pago:</span>
          <span class="ir-value">{{ $venta->metodo_pago ?? 'Efectivo' }}</span>
        </div>
        @if($venta->fecha_entrega)
        @php
          $dias  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
          $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
          $cf    = \Carbon\Carbon::parse($venta->fecha_entrega);
          $feStr = $dias[$cf->dayOfWeek].'. '.$cf->day.' '.$meses[$cf->month-1].'. '.$cf->year;
        @endphp
        <div class="info-row">
          <span class="ir-label">Entrega:</span>
          <span class="ir-value">{{ $feStr }}</span>
        </div>
        @if($venta->hora_entrega)
        <div class="info-row">
          <span class="ir-label">Horario:</span>
          <span class="ir-value">{{ $venta->hora_entrega }}</span>
        </div>
        @endif
        @endif
      </div>

      {{-- Datos del cliente --}}
      <div class="info-section">
        <h4>👤 Datos del cliente</h4>
        <div class="info-row">
          <span class="ir-label">Nombre:</span>
          <span class="ir-value">{{ trim($venta->nombre . ' ' . ($venta->apellido ?? '')) }}</span>
        </div>
        @if($venta->num_documento)
        <div class="info-row">
          <span class="ir-label">CI / RUC:</span>
          <span class="ir-value">{{ $venta->num_documento }}</span>
        </div>
        @endif
        @if($venta->telefono)
        <div class="info-row">
          <span class="ir-label">Teléfono:</span>
          <span class="ir-value">{{ $venta->telefono }}</span>
        </div>
        @endif
        @if($venta->tipo_distribucion)
        <div class="info-row">
          <span class="ir-label">Entrega:</span>
          <span class="ir-value">{{ $venta->tipo_distribucion }}</span>
        </div>
        @endif
        @php $dir = $venta->direccion_envio ?: $venta->direccion; @endphp
        @if($dir)
        <div class="info-row">
          <span class="ir-label">Dirección:</span>
          <span class="ir-value" style="font-size:12px;">{{ $dir }}</span>
        </div>
        @endif
        @if($venta->ciudad)
        <div class="info-row">
          <span class="ir-label">Ciudad:</span>
          <span class="ir-value">{{ $venta->ciudad }}</span>
        </div>
        @endif
      </div>

    </div>

    {{-- TABLA DE PRODUCTOS --}}
    <div class="products-section">
      <h4>🛍️ Detalle del pedido</h4>
      <table class="prod-table">
        <thead>
          <tr>
            <th style="width:90px;">Código</th>
            <th>Nombre del producto</th>
            <th class="center" style="width:70px;">Cant.</th>
            <th class="right" style="width:120px;">Precio unit. Gs.</th>
            <th class="right" style="width:120px;">Subtotal Gs.</th>
          </tr>
        </thead>
        <tbody>
          @php $subtotalGeneral = 0; @endphp
          @foreach($detalles as $det)
          @php
            $subtotal = ($det->cantidad * $det->precio_venta) - ($det->descuento ?? 0);
            $subtotalGeneral += $subtotal;
          @endphp
          <tr>
            <td class="cod">{{ $det->codigo ?? '—' }}</td>
            <td class="nombre">{{ $det->articulo }}</td>
            <td class="center">{{ $det->cantidad }}</td>
            <td class="precio right">{{ number_format($det->precio_venta, 0, ',', '.') }}</td>
            <td class="subtotal">{{ number_format($subtotal, 0, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- TOTALES --}}
    <div class="totales-wrap">
      <div class="totales-box">
        <div class="total-row">
          <span class="tl">Subtotal:</span>
          <span class="tv">{{ number_format($subtotalGeneral, 0, ',', '.') }} Gs.</span>
        </div>
        <div class="total-row">
          <span class="tl">Impuesto ({{ $venta->impuesto ?? 5 }}%):</span>
          <span class="tv">{{ number_format($subtotalGeneral * (($venta->impuesto ?? 5) / 100), 0, ',', '.') }} Gs.</span>
        </div>
        <div class="total-row">
          <span class="tl">Descuentos:</span>
          <span class="tv">— Gs.</span>
        </div>
        <div class="total-final-row">
          <span class="tl">TOTAL:</span>
          <span class="tv">{{ number_format($venta->total_venta, 0, ',', '.') }} Gs.</span>
        </div>
      </div>
    </div>

    {{-- FIRMA --}}
    <div style="padding:22px 32px; border-top:1px solid #fce4ec; display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px; text-align:center;">
      <div>
        <div style="height:44px; border-bottom:1px solid #ccc; margin-bottom:6px;"></div>
        <p style="font-size:11px; color:#888; font-weight:600;">Firma del cliente</p>
        <p style="font-size:11px; color:#aaa;">{{ trim($venta->nombre . ' ' . ($venta->apellido ?? '')) }}</p>
      </div>
      <div>
        <div style="height:44px; border-bottom:1px solid #ccc; margin-bottom:6px;"></div>
        <p style="font-size:11px; color:#888; font-weight:600;">Vendedor / Encargado</p>
        <p style="font-size:11px; color:#aaa;">AndylandPy</p>
      </div>
      <div>
        <div style="height:44px; border-bottom:1px solid #ccc; margin-bottom:6px;"></div>
        <p style="font-size:11px; color:#888; font-weight:600;">Aclaración</p>
        <p style="font-size:11px; color:#aaa;">Sello / CI</p>
      </div>
    </div>

    {{-- FOOTER --}}
    <div class="ticket-footer">
      <p class="gracias">¡Gracias por tu compra!</p>
      <p><strong>AndylandPy</strong> — Regalos y souvenirs personalizados · Paraguay</p>
      <p style="margin-top:6px; font-size:11px; color:rgba(255,255,255,0.4);">
        San Lorenzo, Paraguay &nbsp;·&nbsp; andyland.com.py &nbsp;·&nbsp; WhatsApp: +595 xxx xxxxxx
      </p>
      <p style="margin-top:4px; font-size:10px; color:rgba(255,255,255,0.3);">
        Este documento es un comprobante válido de venta. Conservarlo para cualquier reclamo.
      </p>
    </div>

  </div>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script>
    // Auto-print solo si viene del botón Ticket del admin
    // (no auto-print en esta versión mejorada para que se vea la preview primero)
  </script>
</body>
</html>
