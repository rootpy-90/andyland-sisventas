@extends('layouts.tienda')
@section('title', 'Comprobante #'.$venta->num_comprobante)
@section('content')

<style>
  .comp-wrap { max-width:720px; margin:0 auto; }

  /* ===== ÉXITO ===== */
  .success-banner {
    background:linear-gradient(135deg, #9d174d, #7f1d3e);
    border-radius:14px; padding:28px; text-align:center; color:#fff;
    margin-bottom:28px; box-shadow:0 6px 24px rgba(157,23,77,0.25);
  }
  .success-banner .check-icon {
    width:64px; height:64px; border-radius:50%;
    background:rgba(255,255,255,0.25); border:3px solid rgba(255,255,255,0.5);
    display:flex; align-items:center; justify-content:center;
    font-size:28px; margin:0 auto 14px;
  }
  .success-banner h2 { margin:0 0 6px; font-size:22px; font-weight:900; }
  .success-banner p  { margin:0; font-size:14px; color:rgba(255,255,255,0.85); }
  .comp-num { font-size:28px; font-weight:900; letter-spacing:2px; margin:10px 0 0; }

  /* ===== COMPROBANTE ===== */
  .comp-card {
    background:#fff; border-radius:14px;
    box-shadow:0 3px 16px rgba(157,23,77,0.08);
    border:1px solid #fff1f2; overflow:hidden; margin-bottom:20px;
  }
  .comp-head {
    background:linear-gradient(135deg,#1a0a12,#2c1020);
    color:#fff; padding:16px 24px;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
  }
  .comp-head .title { font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px; }
  .comp-head .title i { color:#9d174d; }
  .comp-head .subtitle { font-size:12px; color:rgba(255,255,255,0.6); }

  .comp-body { padding:0; }

  /* Grid de datos */
  .data-grid { display:grid; grid-template-columns:1fr 1fr; border-bottom:1px solid #fff1f2; }
  @media(max-width:560px){ .data-grid{grid-template-columns:1fr;} }
  .data-cell { padding:14px 20px; border-right:1px solid #fff1f2; }
  .data-cell:nth-child(2n){ border-right:none; }
  .data-cell .dc-label { font-size:11px; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
  .data-cell .dc-value { font-size:14px; font-weight:700; color:#2c3e50; }
  .data-cell .dc-value.pink { color:#9d174d; font-size:16px; }

  /* Tabla de productos */
  .prod-table { width:100%; border-collapse:collapse; }
  .prod-table thead tr { background:linear-gradient(135deg, #9d174d, #7f1d3e); }
  .prod-table th { color:#fff; padding:10px 14px; font-size:12px; font-weight:700; text-align:left; }
  .prod-table td { padding:11px 14px; border-bottom:1px solid #fff1f2; font-size:13px; vertical-align:middle; }
  .prod-table tbody tr:hover { background:#fff9fb; }
  .prod-table tfoot tr { background:#fff9fb; }
  .prod-table tfoot td { padding:13px 14px; font-weight:800; border-top:2px solid #fff1f2; }
  .prod-img { width:40px; height:40px; object-fit:contain; border-radius:6px; background:#f9f9f9; }
  .total-final { font-size:20px; font-weight:900; color:#9d174d; }

  /* ===== ACCIONES ===== */
  .comp-actions { display:flex; gap:12px; flex-wrap:wrap; justify-content:center; margin-top:24px; }
  .btn-print {
    display:inline-flex; align-items:center; gap:8px;
    background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff;
    border:none; border-radius:10px; padding:12px 24px;
    font-size:14px; font-weight:800; cursor:pointer; transition:opacity .2s;
    text-decoration:none;
  }
  .btn-print:hover { opacity:.86; color:#fff; }
  .btn-tienda {
    display:inline-flex; align-items:center; gap:8px;
    background:#fff1f2; color:#7f1d3e;
    border:2px solid #fecdd3; border-radius:10px; padding:11px 22px;
    font-size:14px; font-weight:700; cursor:pointer; text-decoration:none;
    transition:background .2s;
  }
  .btn-tienda:hover { background:#fecdd3; color:#9d174d; }

  /* ===== TIMELINE ===== */
  .timeline { display:flex; align-items:flex-start; justify-content:center; gap:0; margin:0 0 4px; padding:20px 24px 16px; }
  .tl-step { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; }
  .tl-step:not(:last-child)::after {
    content:''; position:absolute; top:18px; left:calc(50% + 18px);
    width:calc(100% - 36px); height:2px;
    background:#fecdd3;
  }
  .tl-step.done:not(:last-child)::after { background:#9d174d; }
  .tl-circle {
    width:36px; height:36px; border-radius:50%; border:2px solid #fecdd3;
    background:#fff; display:flex; align-items:center; justify-content:center;
    font-size:14px; color:#fecdd3; margin-bottom:6px; position:relative; z-index:1;
    transition:all .3s;
  }
  .tl-step.done .tl-circle { background:#9d174d; border-color:#9d174d; color:#fff; }
  .tl-step.active .tl-circle { background:#fff; border-color:#9d174d; color:#9d174d; box-shadow:0 0 0 4px rgba(157,23,77,0.15); }
  .tl-label { font-size:10px; font-weight:700; color:#ccc; text-align:center; text-transform:uppercase; letter-spacing:.4px; }
  .tl-step.done .tl-label, .tl-step.active .tl-label { color:#9d174d; }

  /* ===== WHATSAPP BTN ===== */
  .btn-wsp {
    display:inline-flex; align-items:center; gap:8px;
    background:#25d366; color:#fff;
    border:none; border-radius:10px; padding:11px 22px;
    font-size:14px; font-weight:700; cursor:pointer; text-decoration:none;
    transition:background .2s;
  }
  .btn-wsp:hover { background:#1ebe5d; color:#fff; }

  @media print {
    .shop-nav, .cat-bar, .comp-actions, .shop-footer,
    .comp-card:last-of-type { display:none !important; }
    .comp-wrap { max-width:100%; }
    .success-banner { box-shadow:none; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .comp-head { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .prod-table thead tr { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  }
</style>

<div class="comp-wrap">

  {{-- ===== BANNER ÉXITO ===== --}}
  <div class="success-banner">
    <div class="check-icon"><i class="fa fa-check"></i></div>
    <h2>¡Pedido confirmado!</h2>
    <p>Gracias por tu compra. Te contactaremos pronto para coordinar.</p>
    <p class="comp-num">#{{ $venta->num_comprobante }}</p>
  </div>

  {{-- ===== COMPROBANTE ===== --}}
  <div class="comp-card">

    <div class="comp-head">
      <div>
        <p class="title"><i class="fa fa-file-text"></i> Comprobante de Pago</p>
        <p class="subtitle">AndylandPy — {{ $venta->tipo_comprobante }}</p>
      </div>
      <div style="text-align:right;">
        <p style="font-size:12px; color:rgba(255,255,255,0.5); margin:0;">Nro. de compra</p>
        <p style="font-size:18px; font-weight:900; color:#9d174d; margin:0;">#{{ $venta->num_comprobante }}</p>
      </div>
    </div>

    {{-- Timeline de estado --}}
    @php
      $estado = $venta->estado;
      $steps = [
        ['icon'=>'fa-check',        'label'=>'Recibido',    'done'=> in_array($estado,['P','A']), 'active'=> $estado==='P'],
        ['icon'=>'fa-cog',          'label'=>'En proceso',  'done'=> $estado==='A',               'active'=> false],
        ['icon'=>'fa-truck',        'label'=>'En camino',   'done'=> false,                       'active'=> $estado==='A'],
        ['icon'=>'fa-check-circle', 'label'=>'Entregado',   'done'=> false,                       'active'=> false],
      ];
    @endphp
    <div class="timeline">
      @foreach($steps as $s)
      <div class="tl-step {{ $s['done'] ? 'done' : ($s['active'] ? 'active' : '') }}">
        <div class="tl-circle"><i class="fa {{ $s['icon'] }}"></i></div>
        <div class="tl-label">{{ $s['label'] }}</div>
      </div>
      @endforeach
    </div>

    <div class="comp-body">

      {{-- Datos del pago --}}
      <div class="data-grid">
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-calendar"></i> Fecha de pago</p>
          <p class="dc-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-clock-o"></i> Hora de pago</p>
          <p class="dc-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }} hs</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-credit-card"></i> Tipo de pago</p>
          <p class="dc-value">{{ $venta->metodo_pago }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-hashtag"></i> Nro. de transacción</p>
          <p class="dc-value">{{ $venta->num_transaccion ?? '—' }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-file-text-o"></i> Tipo de comprobante</p>
          <p class="dc-value">{{ $venta->tipo_comprobante }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-info-circle"></i> Estado</p>
          <p class="dc-value" style="color:#b7770d;">
            <i class="fa fa-clock-o"></i> Pendiente de aprobación
          </p>
        </div>
      </div>

      {{-- Datos del cliente y entrega --}}
      <div class="data-grid">
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-user"></i> Cliente</p>
          <p class="dc-value">{{ trim($venta->cliente_nombre . ' ' . $venta->cliente_apellido) }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-phone"></i> Teléfono</p>
          <p class="dc-value">{{ $venta->telefono }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-truck"></i> Tipo de entrega</p>
          <p class="dc-value">{{ $venta->tipo_distribucion ?? 'Delivery' }}</p>
        </div>
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-map-marker"></i> Dirección de entrega</p>
          <p class="dc-value" style="font-size:13px;">{{ $venta->direccion_envio ?? '—' }}</p>
        </div>
        @if($venta->fecha_entrega)
        @php
          $dias  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
          $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
          $cf    = \Carbon\Carbon::parse($venta->fecha_entrega);
          $feStr = $dias[$cf->dayOfWeek].'. '.$cf->day.' '.$meses[$cf->month-1].'. '.$cf->year;
        @endphp
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-calendar-check-o"></i> Fecha de entrega</p>
          <p class="dc-value">{{ $feStr }}</p>
        </div>
        @endif
        @if($venta->hora_entrega)
        <div class="data-cell">
          <p class="dc-label"><i class="fa fa-clock-o"></i> Franja horaria</p>
          <p class="dc-value">{{ $venta->hora_entrega }}</p>
        </div>
        @endif
      </div>

      {{-- Tabla de productos --}}
      <div style="overflow-x:auto;">
        <table class="prod-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th style="text-align:center;">Cant.</th>
              <th style="text-align:right;">Precio unit.</th>
              <th style="text-align:right;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($detalles as $det)
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <img class="prod-img"
                    src="{{ asset('imagenes/articulos/'.rawurlencode($det->imagen ?? '')) }}"
                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect width=\'40\' height=\'40\' fill=\'%23fff9fb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'18\' text-anchor=\'middle\' dominant-baseline=\'central\' fill=\'%23f48fb1\'%3E%3F%3C/text%3E%3C/svg%3E'"
                    alt="{{ $det->articulo }}">
                  <span style="font-weight:700;">{{ $det->articulo }}</span>
                </div>
              </td>
              <td style="text-align:center;">{{ $det->cantidad }}</td>
              <td style="text-align:right;">{{ number_format($det->precio_venta, 0, ',', '.') }} Gs.</td>
              <td style="text-align:right; font-weight:700; color:#9d174d;">
                {{ number_format($det->cantidad * $det->precio_venta, 0, ',', '.') }} Gs.
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="text-align:right;">TOTAL A PAGAR:</td>
              <td style="text-align:right;" class="total-final">
                {{ number_format($venta->total_venta, 0, ',', '.') }} Gs.
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Nota al pie --}}
      <div style="padding:16px 20px; background:#fff9fb; border-top:1px solid #fff1f2; font-size:12px; color:#aaa; text-align:center;">
        <i class="fa fa-info-circle" style="color:#9d174d;"></i>
        Este comprobante es válido como constancia de pedido. La aprobación final será confirmada por AndylandPy vía WhatsApp.
      </div>

    </div>
  </div>

  {{-- ===== ADJUNTAR COMPROBANTE DE PAGO ===== --}}
  <div class="comp-card" style="border-color:#fecdd3;">
    <div class="comp-head" style="background:linear-gradient(135deg, #9d174d, #7f1d3e);">
      <div class="title"><i class="fa fa-upload"></i> Adjuntar comprobante de pago</div>
    </div>
    <div style="padding:20px;">
      @if(session('status'))
        <div style="background:#fff1f2; border-left:4px solid #9d174d; border-radius:8px; padding:11px 14px; margin-bottom:16px; font-size:13px; color:#9d174d; font-weight:600;">
          <i class="fa fa-check-circle"></i> {{ session('status') }}
        </div>
      @endif

      @if($venta->comprobante_pago)
        <div style="background:#d5f5e3; border-left:4px solid #27ae60; border-radius:8px; padding:12px 16px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
          <div style="display:flex; align-items:center; gap:10px; color:#1e8449; font-weight:700; font-size:13px;">
            <i class="fa fa-check-circle" style="font-size:20px;"></i>
            <span>Comprobante adjuntado correctamente</span>
          </div>
          <a href="{{ asset('comprobantes/'.$venta->comprobante_pago) }}" target="_blank"
            style="background:#27ae60; color:#fff; border-radius:8px; padding:7px 16px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="fa fa-eye"></i> Ver comprobante
          </a>
        </div>
      @else
        <p style="font-size:13px; color:#888; margin:0 0 14px;">
          Si realizaste una <strong>transferencia bancaria</strong> o cualquier otro pago digital, podés adjuntar la captura de pantalla o PDF del comprobante para agilizar la aprobación de tu pedido.
        </p>
      @endif

      @if(!$venta->comprobante_pago)
      <form method="POST" action="{{ route('compra.comprobante', $venta->idventa) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div style="border:2px dashed #fecdd3; border-radius:10px; padding:20px; text-align:center; cursor:pointer; transition:background 0.15s; background:#fff9fb;"
          onclick="document.getElementById('file-input').click()"
          ondragover="event.preventDefault(); this.style.borderColor='#9d174d'"
          ondragleave="this.style.borderColor='#fecdd3'"
          ondrop="handleDrop(event)">
          <i class="fa fa-cloud-upload" style="font-size:36px; color:#fda4af; display:block; margin-bottom:8px;"></i>
          <p style="font-size:14px; font-weight:700; color:#7f1d3e; margin:0 0 4px;">
            Hacé clic o arrastrá tu archivo aquí
          </p>
          <p style="font-size:12px; color:#bbb; margin:0;">JPG, PNG o PDF · Máx. 5 MB</p>
          <p id="file-name" style="font-size:13px; color:#9d174d; font-weight:700; margin:8px 0 0; display:none;"></p>
        </div>
        <input type="file" name="comprobante_archivo" id="file-input" accept=".jpg,.jpeg,.png,.pdf"
          style="display:none;" onchange="mostrarNombre(this)">
        <div style="margin-top:14px; text-align:right;">
          <button type="submit" id="btn-subir" disabled
            style="background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff; border:none; border-radius:9px; padding:10px 24px; font-size:14px; font-weight:800; cursor:pointer; opacity:0.5; transition:opacity 0.2s;">
            <i class="fa fa-upload"></i> Subir comprobante
          </button>
        </div>
      </form>
      @endif
    </div>
  </div>

  {{-- ===== ACCIONES ===== --}}
  <div class="comp-actions">
    <button onclick="window.print()" class="btn-print">
      <i class="fa fa-print"></i> Imprimir / PDF
    </button>
    <a href="https://wa.me/595000000000?text={{ urlencode('Hola AndylandPy! Mi pedido es el #'.$venta->num_comprobante.'. Quería consultar sobre el estado.') }}"
       target="_blank" class="btn-wsp">
      <i class="fa fa-whatsapp"></i> Consultar por WhatsApp
    </a>
    <a href="{{ url('tienda') }}" class="btn-tienda">
      <i class="fa fa-shopping-bag"></i> Seguir comprando
    </a>
    <a href="{{ route('mis.compras') }}" class="btn-tienda">
      <i class="fa fa-list"></i> Mis pedidos
    </a>
  </div>

</div>

@push('scripts')
<script>
Cart.clear();

function mostrarNombre(input) {
  const btn  = document.getElementById('btn-subir');
  const label = document.getElementById('file-name');
  if (input.files && input.files[0]) {
    label.textContent = '📎 ' + input.files[0].name;
    label.style.display = 'block';
    btn.disabled = false;
    btn.style.opacity = '1';
  }
}

function handleDrop(e) {
  e.preventDefault();
  e.currentTarget.style.borderColor = '#fecdd3';
  const file = e.dataTransfer.files[0];
  if (!file) return;
  const input = document.getElementById('file-input');
  const dt = new DataTransfer();
  dt.items.add(file);
  input.files = dt.files;
  mostrarNombre(input);
}
</script>
@endpush
@endsection
