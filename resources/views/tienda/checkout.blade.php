@extends('layouts.tienda')
@section('title', 'Confirmar Pedido')
@section('content')

<style>
  /* ===== LAYOUT ===== */
  .checkout-wrap { display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start; }
  @media(max-width:900px){ .checkout-wrap{grid-template-columns:1fr;} }

  /* ===== SECCIÓN ===== */
  .cs-box { background:#fff; border-radius:14px; box-shadow:0 3px 16px rgba(157,23,77,0.08); border:1px solid #fff1f2; overflow:hidden; margin-bottom:20px; }
  .cs-head { padding:14px 20px; border-bottom:1px solid #fff1f2; font-size:14px; font-weight:800; color:#9d174d; display:flex; align-items:center; gap:8px; }
  .cs-head i { color:#9d174d; }
  .cs-head .step-num { width:24px; height:24px; border-radius:50%; background:#9d174d; color:#fff; font-size:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .cs-body { padding:18px 20px; }

  /* ===== OPCIÓN CARD (radio visual) ===== */
  .opt-grid { display:grid; gap:10px; }
  .opt-grid.cols2 { grid-template-columns:1fr 1fr; }
  @media(max-width:520px){ .opt-grid.cols2{grid-template-columns:1fr;} }

  .opt-card {
    display:flex; align-items:flex-start; gap:12px; padding:13px 15px;
    border:2px solid #fecdd3; border-radius:10px; cursor:pointer;
    transition:all 0.18s; background:#fff; position:relative;
  }
  .opt-card:hover { border-color:#9d174d; background:#fff9fb; }
  .opt-card.selected { border-color:#9d174d; background:#fff9fb; box-shadow:0 0 0 3px rgba(157,23,77,0.1); }
  .opt-card input[type=radio] { position:absolute; opacity:0; pointer-events:none; }
  .opt-icon { font-size:22px; color:#9d174d; width:28px; text-align:center; flex-shrink:0; margin-top:1px; }
  .opt-info .label { font-size:14px; font-weight:800; color:#2c3e50; }
  .opt-info .desc  { font-size:12px; color:#aaa; margin-top:2px; }

  /* ===== FORM FIELDS ===== */
  .field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:14px; }
  @media(max-width:520px){ .field-row{grid-template-columns:1fr;} }
  .field-full { grid-column:1/-1; }
  .fl { display:flex; flex-direction:column; }
  .fl label { font-size:12px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
  .fl label span { color:#9d174d; }
  .finput {
    padding:9px 13px; border:2px solid #fecdd3; border-radius:8px;
    font-size:14px; outline:none; width:100%;
    transition:border-color .18s;
  }
  .finput:focus { border-color:#9d174d; box-shadow:0 0 0 3px rgba(157,23,77,.1); }

  /* ===== SIDEBAR ===== */
  .sidebar-sticky { position:sticky; top:140px; }
  .order-table { width:100%; border-collapse:collapse; font-size:13px; }
  .order-table th { background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff; padding:9px 12px; font-weight:700; text-align:left; }
  .order-table td { padding:10px 12px; border-bottom:1px solid #fff1f2; vertical-align:middle; }
  .order-table tbody tr:hover { background:#fff9fb; }
  .order-table .item-img { width:40px; height:40px; object-fit:contain; border-radius:6px; background:#f9f9f9; }
  .order-table tfoot td { border-top:2px solid #f0f0f0; font-weight:800; font-size:15px; }
  .total-amt { font-size:20px; font-weight:900; color:#9d174d; }

  /* ===== CONFIRM BTN ===== */
  .btn-confirm {
    display:flex; align-items:center; justify-content:center; gap:8px;
    width:100%; background:linear-gradient(135deg, #9d174d, #7f1d3e);
    color:#fff; border:none; border-radius:10px; padding:14px;
    font-size:15px; font-weight:900; cursor:pointer; transition:opacity .2s;
    margin-top:18px; letter-spacing:.2px;
  }
  .btn-confirm:hover { opacity:.86; }
  .btn-confirm:disabled { background:linear-gradient(135deg,#ccc,#aaa); cursor:not-allowed; }

  /* ===== RESUMEN LATERAL ===== */
  .resumen-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #fff1f2; font-size:13px; }
  .resumen-row:last-child { border:none; }
  .resumen-row .rl { color:#888; }
  .resumen-row .rv { font-weight:700; color:#2c3e50; font-size:12px; text-align:right; max-width:160px; }

  /* ===== PERFIL INCOMPLETO ===== */
  .notice-banner { background:#fff8e1; border-left:4px solid #f39c12; border-radius:8px; padding:12px 16px; font-size:13px; color:#7d6608; margin-bottom:20px; }

  /* ===== PROGRESS STEPPER ===== */
  .checkout-stepper { display:flex; align-items:center; margin-bottom:24px; padding:16px 20px; background:#fff; border-radius:12px; border:1px solid #fff1f2; box-shadow:0 2px 8px rgba(157,23,77,0.05); }
  .cs-stp { display:flex; align-items:center; flex:1; }
  .cs-stp:last-child { flex:none; }
  .stp-circle { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; flex-shrink:0; transition:all .2s; }
  .stp-done   .stp-circle { background:#9d174d; color:#fff; }
  .stp-active .stp-circle { background:#fff; border:2px solid #9d174d; color:#9d174d; box-shadow:0 0 0 4px rgba(157,23,77,0.12); }
  .stp-idle   .stp-circle { background:#f5f5f5; color:#ccc; border:2px solid #eee; }
  .stp-label { font-size:11px; font-weight:700; margin-left:7px; white-space:nowrap; }
  .stp-done .stp-label, .stp-active .stp-label { color:#9d174d; }
  .stp-idle .stp-label { color:#bbb; }
  .stp-line { flex:1; height:2px; margin:0 8px; border-radius:2px; }
  .stp-line.done { background:#9d174d; }
  .stp-line.idle { background:#eee; }
  @media(max-width:520px){ .stp-label{ display:none; } }

  /* Carrito vacío en checkout */
  .empty-cart-notice { background:#fff; border-radius:14px; border:1px solid #fff1f2; padding:48px 24px; text-align:center; box-shadow:0 2px 12px rgba(157,23,77,0.07); }
</style>

{{-- Breadcrumb --}}
<div style="font-size:13px; color:#aaa; margin-bottom:18px;">
  <a href="{{ url('tienda') }}" style="color:#9d174d; font-weight:600;"><i class="fa fa-home"></i> Tienda</a>
  <span style="margin:0 8px;">›</span>
  <span style="font-weight:700; color:#555;">Confirmar Pedido</span>
</div>

@if(session('error'))
  <div style="background:#fadbd8; border-left:4px solid #e74c3c; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:13px; color:#922b21;">
    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
  </div>
@endif

{{-- PERFIL INCOMPLETO --}}
@if(is_null(auth()->user()->persona->num_documento) || is_null(auth()->user()->persona->telefono))
  <div class="notice-banner"><i class="fa fa-info-circle"></i> <strong>Completá tus datos</strong> para continuar.</div>
  <div style="max-width:520px;">
    <div class="cs-box">
      <div class="cs-head"><i class="fa fa-user"></i> Datos de entrega</div>
      <div class="cs-body">
        <form action="{{ url('completar-perfil') }}" method="POST">
          {{ csrf_field() }}
          <div class="field-row">
            <div class="fl">
              <label>CI / RUC <span>*</span></label>
              <input type="text" name="num_documento" class="finput" required placeholder="Ej: 4540123">
            </div>
            <div class="fl">
              <label>Teléfono <span>*</span></label>
              <input type="text" name="telefono" class="finput" required placeholder="Ej: 0981123456">
            </div>
            <div class="fl field-full">
              <label>Dirección <span>*</span></label>
              <input type="text" name="direccion" class="finput" required placeholder="Calle, nro, barrio...">
            </div>
          </div>
          <button type="submit" class="btn-confirm" style="margin-top:14px;">
            <i class="fa fa-save"></i> Guardar y Continuar
          </button>
        </form>
      </div>
    </div>
  </div>

@else

{{-- ===== AVISO CARRITO VACÍO ===== --}}
<div class="empty-cart-notice" id="empty-cart-notice" style="display:none;">
  <i class="fa fa-shopping-bag" style="font-size:52px; color:#fff1f2; display:block; margin-bottom:16px;"></i>
  <p style="font-size:18px; font-weight:800; color:#7f1d3e; margin:0 0 8px;">Tu carrito está vacío</p>
  <p style="font-size:14px; color:#aaa; margin:0 0 20px;">Agregá productos antes de confirmar el pedido.</p>
  <a href="{{ url('tienda') }}"
     style="background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff; border-radius:10px; padding:11px 28px; font-size:14px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
    <i class="fa fa-arrow-left"></i> Ir a la tienda
  </a>
</div>

{{-- ===== STEPPER ===== --}}
<div class="checkout-stepper" id="checkout-stepper">
  <div class="cs-stp stp-active" id="stp-1">
    <div class="stp-circle">1</div>
    <span class="stp-label">Entrega</span>
  </div>
  <div class="stp-line idle" id="line-1"></div>
  <div class="cs-stp stp-idle" id="stp-2">
    <div class="stp-circle">2</div>
    <span class="stp-label">Fecha</span>
  </div>
  <div class="stp-line idle" id="line-2"></div>
  <div class="cs-stp stp-idle" id="stp-3">
    <div class="stp-circle">3</div>
    <span class="stp-label">Pago</span>
  </div>
  <div class="stp-line idle" id="line-3"></div>
  <div class="cs-stp stp-idle" id="stp-4">
    <div class="stp-circle">4</div>
    <span class="stp-label">Comprobante</span>
  </div>
  <div class="stp-line idle" id="line-4"></div>
  <div class="cs-stp stp-idle" id="stp-5">
    <div class="stp-circle"><i class="fa fa-check"></i></div>
    <span class="stp-label">Confirmar</span>
  </div>
</div>

{{-- ======= CHECKOUT PRINCIPAL ======= --}}
<form action="{{ url('tienda/pedido') }}" method="POST" id="checkout-form" enctype="multipart/form-data">
  {{ csrf_field() }}
  <input type="hidden" name="cart_json"         id="fld-cart">
  <input type="hidden" name="tipo_distribucion" id="fld-distrib" value="Delivery">
  <input type="hidden" name="metodo_pago"       id="fld-pago"   value="Efectivo en tienda">
  <input type="hidden" name="tipo_facturacion"  id="fld-factura" value="Ticket">
  <input type="hidden" name="fecha_entrega"     id="fld-fecha">
  <input type="hidden" name="hora_entrega"      id="fld-hora"   value="A coordinar">
  <input type="hidden" name="num_transaccion"   id="fld-transaccion" value="">

<div class="checkout-wrap">

  {{-- ===== COLUMNA IZQUIERDA ===== --}}
  <div>

    {{-- ═══ PASO 1: ESTIMACIÓN DE FECHA DE ENTREGA ═══ --}}
    <div class="cs-box" style="border-top:3px solid #9d174d;">
      <div class="cs-head" style="background:linear-gradient(135deg,#fff1f2,#fff);">
        <span class="step-num">1</span>
        <span style="display:flex; flex-direction:column; gap:2px;">
          <span>Estimación de Fecha de Entrega</span>
          <span style="font-size:11px; color:#aaa; font-weight:400;">Seleccioná una fecha antes de continuar</span>
        </span>
      </div>
      <div class="cs-body">

        {{-- Tiempos de entrega por producto --}}
        <div id="tiempos-productos" style="margin-bottom:18px;"></div>

        {{-- Fechas disponibles --}}
        @if($fechas->isEmpty())
          <div style="background:#fff8e1; border-left:4px solid #f39c12; border-radius:8px; padding:12px 14px; font-size:13px; color:#7d6608;">
            <i class="fa fa-clock-o"></i> Sin fechas disponibles por el momento. Coordinaremos la entrega por WhatsApp.
          </div>
        @else
          <p style="font-size:13px; font-weight:700; color:#555; margin:0 0 10px;">
            <i class="fa fa-calendar" style="color:#9d174d;"></i> Fechas disponibles para entrega:
          </p>
          <div class="opt-grid" style="margin-bottom:16px;">
            @php
              $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
              $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            @endphp
            @foreach($fechas as $f)
            @php
              $cf = \Carbon\Carbon::parse($f->fecha);
              $fechaTxt = $dias[$cf->dayOfWeek].' '.$cf->day.' de '.$meses[$cf->month-1];
              $esHoy    = $cf->isToday();
              $diasRest = now()->diffInDays($cf, false);
            @endphp
            <div class="opt-card" id="fecha-{{ $f->id }}"
              onclick="selectFecha('{{ $f->fecha }}','{{ $fechaTxt }}','fecha-{{ $f->id }}')">
              <span class="opt-icon" style="font-size:20px;"><i class="fa fa-calendar-check-o"></i></span>
              <div class="opt-info">
                <p class="label">{{ $fechaTxt }}</p>
                <p class="desc">
                  @if($esHoy) Hoy
                  @elseif($diasRest == 1) Mañana
                  @elseif($diasRest > 0) En {{ $diasRest }} días
                  @endif
                  @if($f->descripcion) · {{ $f->descripcion }} @endif
                </p>
              </div>
            </div>
            @endforeach
          </div>

          {{-- Alerta si no eligió fecha --}}
          <div id="alerta-fecha" style="display:none; background:#fef2f2; border-left:4px solid #e74c3c; border-radius:8px; padding:10px 14px; font-size:13px; color:#991b1b; font-weight:600; margin-bottom:12px;">
            <i class="fa fa-exclamation-circle"></i> Debés seleccionar una fecha de entrega para continuar.
          </div>
        @endif

        <div class="fl">
          <label>Franja horaria preferida</label>
          <select class="finput" id="sel-hora" onchange="document.getElementById('fld-hora').value=this.value">
            <option value="Mañana (9:00 - 12:00)">Mañana (9:00 - 12:00)</option>
            <option value="Tarde (13:00 - 18:00)">Tarde (13:00 - 18:00)</option>
            <option value="A coordinar">A coordinar por WhatsApp</option>
          </select>
        </div>

      </div>
    </div>

    {{-- PASO 2: TIPO DE DISTRIBUCIÓN --}}
    <div class="cs-box">
      <div class="cs-head"><span class="step-num">2</span> Tipo de entrega</div>
      <div class="cs-body">
        <div class="opt-grid cols2">
          <div class="opt-card selected" id="opt-delivery" onclick="selectDistrib('Delivery')">
            <input type="radio" name="_distrib" value="Delivery" checked>
            <span class="opt-icon"><i class="fa fa-truck"></i></span>
            <div class="opt-info">
              <p class="label">Delivery</p>
              <p class="desc">Recibís en tu domicilio</p>
            </div>
          </div>
          <div class="opt-card" id="opt-retiro" onclick="selectDistrib('Retiro en tienda')">
            <input type="radio" name="_distrib" value="Retiro en tienda">
            <span class="opt-icon"><i class="fa fa-store"></i></span>
            <div class="opt-info">
              <p class="label">Recoger en tienda</p>
              <p class="desc">Pasás a buscar tu pedido</p>
            </div>
          </div>
        </div>

        {{-- Dirección de envío (solo para Delivery) --}}
        <div id="seccion-direccion" style="margin-top:16px;">
          <div class="fl">
            <label>Dirección de envío <span style="color:#9d174d;">*</span></label>
            <input type="text" id="inp-direccion" class="finput"
              value="{{ auth()->user()->persona->direccion ?? '' }}"
              placeholder="Calle, número, barrio, ciudad...">
            <span style="font-size:11px; color:#bbb; margin-top:3px;">
              Dirección registrada en tu perfil. Podés modificarla solo para este pedido.
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- PASO 3: MÉTODO DE PAGO --}}
    <div class="cs-box">
      <div class="cs-head"><span class="step-num">3</span> Método de pago</div>
      <div class="cs-body">
        <div class="opt-grid cols2">
          <div class="opt-card selected" id="pago-efectivo" onclick="selectPago('Efectivo','pago-efectivo')">
            <span class="opt-icon"><i class="fa fa-money"></i></span>
            <div class="opt-info">
              <p class="label">Efectivo</p>
              <p class="desc">Pagás al recibir o retirar</p>
            </div>
          </div>
          <div class="opt-card" id="pago-transferencia" onclick="selectPago('Transferencia','pago-transferencia')">
            <span class="opt-icon"><i class="fa fa-bank"></i></span>
            <div class="opt-info">
              <p class="label">Transferencia bancaria</p>
              <p class="desc">Adjuntá tu comprobante</p>
            </div>
          </div>
        </div>

        {{-- Adjuntar comprobante (solo para Transferencia) --}}
        <div id="seccion-transferencia" style="display:none; margin-top:16px;">
          <div style="background:#fff1f2; border-radius:10px; padding:16px;">
            <p style="font-size:13px; font-weight:700; color:#9d174d; margin:0 0 10px;">
              <i class="fa fa-info-circle"></i> Datos bancarios para la transferencia:
            </p>
            <p style="font-size:13px; color:#555; margin:0 0 14px; line-height:1.7;">
              <b>Banco:</b> Banco Nacional de Fomento<br>
              <b>Titular:</b> AndylandPy<br>
              <b>Cuenta:</b> 000-123456-7
            </p>
            <div class="fl" style="margin-bottom:12px;">
              <label>Nro. de comprobante de transferencia</label>
              <input type="text" name="num_transaccion" class="finput" placeholder="Ej: 123456789"
                oninput="document.getElementById('fld-transaccion').value=this.value">
            </div>
            <div class="fl">
              <label>Adjuntar comprobante <span style="color:#9d174d;">*</span></label>
              <div style="border:2px dashed #fecdd3; border-radius:8px; padding:16px; text-align:center; cursor:pointer; background:#fff;"
                onclick="document.getElementById('comp-file').click()"
                ondragover="event.preventDefault(); this.style.borderColor='#9d174d'"
                ondragleave="this.style.borderColor='#fecdd3'"
                ondrop="handleDropCheckout(event)">
                <i class="fa fa-cloud-upload" style="font-size:28px; color:#fda4af; display:block; margin-bottom:6px;"></i>
                <p id="comp-file-name" style="font-size:13px; color:#aaa; margin:0;">
                  Hacé clic o arrastrá el archivo aquí · JPG, PNG o PDF · Máx. 5 MB
                </p>
              </div>
              <input type="file" name="comprobante_archivo" id="comp-file"
                accept=".jpg,.jpeg,.png,.pdf" style="display:none;"
                onchange="mostrarArchivoCheckout(this)">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- PASO 4: TIPO DE FACTURACIÓN --}}
    <div class="cs-box">
      <div class="cs-head"><span class="step-num">4</span> Tipo de comprobante</div>
      <div class="cs-body">
        <div class="opt-grid cols2">
          <div class="opt-card selected" id="fact-ticket" onclick="selectFactura('Ticket','fact-ticket')">
            <span class="opt-icon"><i class="fa fa-receipt"></i></span>
            <div class="opt-info">
              <p class="label">Ticket</p>
              <p class="desc">Comprobante simple</p>
            </div>
          </div>
          <div class="opt-card" id="fact-factura" onclick="selectFactura('Factura','fact-factura')">
            <span class="opt-icon"><i class="fa fa-file-text"></i></span>
            <div class="opt-info">
              <p class="label">Factura</p>
              <p class="desc">Requiere datos del contribuyente</p>
            </div>
          </div>
        </div>

        {{-- Datos de factura (RUC) --}}
        <div id="seccion-factura" style="display:none; margin-top:14px;">
          <div class="fl">
            <label>RUC / CI del contribuyente <span style="color:#9d174d;">*</span></label>
            <input type="text" id="inp-ruc" class="finput"
              value="{{ auth()->user()->persona->num_documento ?? '' }}"
              placeholder="Ej: 80012345-6">
          </div>
        </div>
      </div>
    </div>

  </div>{{-- fin columna izquierda --}}

  {{-- ===== SIDEBAR RESUMEN ===== --}}
  <div class="sidebar-sticky">

    {{-- Datos del comprador --}}
    <div class="cs-box">
      <div class="cs-head"><i class="fa fa-user"></i> Comprador</div>
      <div class="cs-body" style="padding:14px 18px;">
        <div class="resumen-row">
          <span class="rl"><i class="fa fa-user" style="color:#9d174d;"></i></span>
          <span class="rv">{{ auth()->user()->name }}</span>
        </div>
        <div class="resumen-row">
          <span class="rl"><i class="fa fa-phone" style="color:#9d174d;"></i></span>
          <span class="rv">{{ auth()->user()->persona->telefono }}</span>
        </div>
        <div class="resumen-row" id="resumen-distrib">
          <span class="rl"><i class="fa fa-truck" style="color:#9d174d;"></i></span>
          <span class="rv">Delivery</span>
        </div>
        <div class="resumen-row" id="resumen-fecha" style="display:none;">
          <span class="rl"><i class="fa fa-calendar" style="color:#9d174d;"></i></span>
          <span class="rv" id="resumen-fecha-txt">—</span>
        </div>
        <div class="resumen-row" id="resumen-pago">
          <span class="rl"><i class="fa fa-credit-card" style="color:#9d174d;"></i></span>
          <span class="rv">Efectivo en tienda</span>
        </div>
        <div class="resumen-row" id="resumen-factura">
          <span class="rl"><i class="fa fa-file-text-o" style="color:#9d174d;"></i></span>
          <span class="rv">Ticket</span>
        </div>
      </div>
    </div>

    {{-- Resumen carrito --}}
    <div class="cs-box">
      <div class="cs-head"><i class="fa fa-list"></i> Productos</div>
      <div id="sidebar-items" style="padding:8px 16px 4px;"></div>
      <div style="padding:14px 18px; border-top:1px solid #fff1f2; background:#fff9fb;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:14px; font-weight:700; color:#555;">Total:</span>
          <span class="total-amt" id="sidebar-total">0 Gs.</span>
        </div>
        <button type="submit" class="btn-confirm" id="btn-confirm" disabled>
          <i class="fa fa-check-circle"></i> Confirmar Pedido
        </button>
        <div style="text-align:center; margin-top:10px;">
          <a href="{{ url('tienda') }}" style="font-size:12px; color:#bbb;">
            <i class="fa fa-arrow-left"></i> Seguir comprando
          </a>
        </div>
      </div>
    </div>

  </div>{{-- fin sidebar --}}

</div>{{-- fin grid --}}
</form>
@endif

@push('scripts')
<script>
/* ===== TIEMPOS DE ENTREGA POR PRODUCTO ===== */
const tiemposMap = @json($tiemposEntrega->map(function($t){ return ['nombre'=>$t->nombre,'tiempo'=>$t->tiempo_entrega]; }));

function renderTiemposProductos() {
  const items = Cart.all();
  const cont  = document.getElementById('tiempos-productos');
  if (!cont || !items.length) return;

  // Agrupar tiempos únicos
  const tiempos = items.map(it => {
    const info = tiemposMap[it.id];
    return {
      nombre: it.nombre,
      tiempo: (info && info.tiempo) ? info.tiempo : 'Entrega inmediata'
    };
  });

  if (!tiempos.length) return;

  cont.innerHTML = `
    <div style="background:#fff1f2; border-radius:10px; padding:14px 16px; margin-bottom:14px; border:1px solid #fecdd3;">
      <p style="font-size:12px; font-weight:800; color:#9d174d; text-transform:uppercase; letter-spacing:.5px; margin:0 0 10px;">
        <i class="fa fa-clock-o"></i> Tiempo estimado por producto
      </p>
      ${tiempos.map(t => `
        <div style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #fecdd3; font-size:13px;">
          <span style="color:#555; font-weight:600;">${t.nombre}</span>
          <span style="background:#9d174d; color:#fff; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:700; white-space:nowrap;">
            <i class="fa fa-calendar-o"></i> ${t.tiempo}
          </span>
        </div>
      `).join('')}
      <p style="font-size:11px; color:#aaa; margin:8px 0 0;">
        <i class="fa fa-info-circle"></i> Los tiempos son estimados a partir de la fecha de confirmación del pedido.
      </p>
    </div>
  `;
}

/* ===== STEPPER ===== */
function advanceStepper(step) {
  for (let i = 1; i <= 5; i++) {
    const el = document.getElementById('stp-' + i);
    if (!el) continue;
    el.className = 'cs-stp ' + (i < step ? 'stp-done' : i === step ? 'stp-active' : 'stp-idle');
    if (i < step) el.querySelector('.stp-circle').innerHTML = '<i class="fa fa-check"></i>';
    else el.querySelector('.stp-circle').textContent = i;
    const ln = document.getElementById('line-' + i);
    if (ln) ln.className = 'stp-line ' + (i < step ? 'done' : 'idle');
  }
}

/* ===== INIT ===== */
document.addEventListener('DOMContentLoaded', function() {
  renderTiemposProductos();
});

/* ===== CARRITO VACÍO CHECK ===== */
(function() {
  const items = JSON.parse(localStorage.getItem('andyland_cart_v2') || '[]');
  if (!items.length) {
    const notice  = document.getElementById('empty-cart-notice');
    const stepper = document.getElementById('checkout-stepper');
    const form    = document.getElementById('checkout-form');
    if (notice)  notice.style.display  = 'block';
    if (stepper) stepper.style.display = 'none';
    if (form)    form.style.display    = 'none';
  }
})();

/* ===== SELECCIÓN DISTRIBUCIÓN ===== */
function selectDistrib(val) {
  document.getElementById('fld-distrib').value = val;
  ['opt-delivery','opt-retiro'].forEach(id => document.getElementById(id).classList.remove('selected'));
  document.getElementById(val === 'Delivery' ? 'opt-delivery' : 'opt-retiro').classList.add('selected');
  document.getElementById('seccion-direccion').style.display = val === 'Delivery' ? 'block' : 'none';
  sincronizarDireccion();
  actualizarResumen('resumen-distrib', val);
  advanceStepper(2);
}

/* ===== SELECCIÓN FECHA ===== */
function selectFecha(fecha, texto, cardId) {
  document.getElementById('fld-fecha').value = fecha;
  document.querySelectorAll('[id^="fecha-"]').forEach(el => el.classList.remove('selected'));
  document.getElementById(cardId).classList.add('selected');
  document.getElementById('resumen-fecha').style.display = 'flex';
  document.getElementById('resumen-fecha-txt').textContent = texto;
  advanceStepper(3);
  checkReady();
}

/* ===== SELECCIÓN PAGO ===== */
function selectPago(val, cardId) {
  document.getElementById('fld-pago').value = val;
  ['pago-efectivo','pago-transferencia'].forEach(id => document.getElementById(id).classList.remove('selected'));
  document.getElementById(cardId).classList.add('selected');
  document.getElementById('seccion-transferencia').style.display = val === 'Transferencia' ? 'block' : 'none';
  actualizarResumen('resumen-pago', val);
  advanceStepper(4);
}

function mostrarArchivoCheckout(input) {
  const label = document.getElementById('comp-file-name');
  if (input.files && input.files[0]) {
    label.textContent = '📎 ' + input.files[0].name;
    label.style.color = '#9d174d';
    label.style.fontWeight = '700';
  }
}

function handleDropCheckout(e) {
  e.preventDefault();
  e.currentTarget.style.borderColor = '#fecdd3';
  const file = e.dataTransfer.files[0];
  if (!file) return;
  const input = document.getElementById('comp-file');
  const dt = new DataTransfer();
  dt.items.add(file);
  input.files = dt.files;
  mostrarArchivoCheckout(input);
}

/* ===== SELECCIÓN FACTURA ===== */
function selectFactura(val, cardId) {
  document.getElementById('fld-factura').value = val;
  ['fact-ticket','fact-factura'].forEach(id => document.getElementById(id).classList.remove('selected'));
  document.getElementById(cardId).classList.add('selected');
  document.getElementById('seccion-factura').style.display = val === 'Factura' ? 'block' : 'none';
  actualizarResumen('resumen-factura', val);
  advanceStepper(5);
}

function actualizarResumen(rowId, texto) {
  const row = document.getElementById(rowId);
  if (row) row.querySelector('.rv').textContent = texto;
}

function sincronizarDireccion() {
  const distrib = document.getElementById('fld-distrib').value;
  // La dirección de envío se maneja en el backend según el tipo de distribución
}

/* ===== RENDER CARRITO EN SIDEBAR ===== */
function renderSidebar() {
  const items = Cart.all();
  const cont  = document.getElementById('sidebar-items');
  const total = document.getElementById('sidebar-total');
  const btn   = document.getElementById('btn-confirm');
  const inp   = document.getElementById('fld-cart');

  if (!cont) return;

  if (!items.length) {
    cont.innerHTML = '<p style="text-align:center;color:#bbb;padding:16px;font-size:13px;">Carrito vacío</p>';
    if (btn) btn.disabled = true;
    return;
  }

  cont.innerHTML = items.map(it => `
    <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #fff1f2;">
      <img src="/imagenes/articulos/${encodeURIComponent(it.imagen||'')}"
        onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect width=\'40\' height=\'40\' fill=\'%23fff9fb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'18\' text-anchor=\'middle\' dominant-baseline=\'central\' fill=\'%23f48fb1\'%3E%3F%3C/text%3E%3C/svg%3E'"
        style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#f9f9f9;flex-shrink:0;">
      <div style="flex:1;min-width:0;">
        <p style="font-size:12px;font-weight:700;color:#2c3e50;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${it.nombre}</p>
        <p style="font-size:11px;color:#aaa;margin:2px 0 0;">${it.qty} × ${fmt(it.precio)} Gs.</p>
      </div>
      <span style="font-size:12px;font-weight:800;color:#9d174d;flex-shrink:0;">${fmt(it.precio*it.qty)} Gs.</span>
    </div>
  `).join('');

  if (total) total.textContent = fmt(Cart.total()) + ' Gs.';
  if (inp)   inp.value = JSON.stringify(items);
  checkReady();
}

function checkReady() {
  const btn      = document.getElementById('btn-confirm');
  const items    = Cart.all();
  const hayFechas = document.querySelectorAll('[id^="fecha-"]').length > 0;
  const fechaOk  = !hayFechas || document.getElementById('fld-fecha').value !== '';
  if (btn) btn.disabled = !(items.length > 0 && fechaOk);
}

// Sincronizar dirección y validar fecha antes de enviar
document.getElementById('checkout-form') && document.getElementById('checkout-form').addEventListener('submit', function(e) {
  // Validar que haya fecha seleccionada si existen fechas disponibles
  const hayFechas = document.querySelectorAll('[id^="fecha-"]').length > 0;
  const fechaVal  = document.getElementById('fld-fecha').value;
  if (hayFechas && !fechaVal) {
    e.preventDefault();
    const alerta = document.getElementById('alerta-fecha');
    if (alerta) {
      alerta.style.display = 'block';
      alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return false;
  }

  const distrib = document.getElementById('fld-distrib').value;
  if (distrib === 'Delivery') {
    const dir = document.getElementById('inp-direccion') && document.getElementById('inp-direccion').value;
    let h = document.getElementById('fld-dir-envio');
    if (!h) { h = document.createElement('input'); h.type='hidden'; h.name='direccion_envio'; h.id='fld-dir-envio'; this.appendChild(h); }
    h.value = dir || '{{ auth()->user()->persona->direccion ?? '' }}';
  }
});

renderSidebar();
</script>
@endpush
@endsection
