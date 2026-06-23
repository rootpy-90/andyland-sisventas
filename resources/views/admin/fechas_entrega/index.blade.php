@extends('layouts.admin')
@section('page_title', 'Fechas de Entrega')
@section('page_subtitle', 'Gestión de fechas disponibles para el checkout')
@section('box_title', 'Fechas de Entrega')
@section('contenido')

<style>
  /* ===== CARDS RESUMEN ===== */
  .fe-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:14px; margin-bottom:24px; }
  .fe-stat {
    background:#fff; border-radius:12px; border:1px solid #fff1f2;
    padding:16px 18px; display:flex; align-items:center; gap:14px;
    box-shadow:0 2px 8px rgba(233,30,140,0.06);
  }
  .fe-stat .fe-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
  .fe-stat .fe-num  { font-size:26px; font-weight:900; line-height:1; }
  .fe-stat .fe-lbl  { font-size:11px; color:#aaa; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }

  /* ===== FORMULARIO ===== */
  .add-box { background:#fff; border-radius:14px; border:1px solid #fff1f2; overflow:hidden; margin-bottom:22px; box-shadow:0 2px 10px rgba(233,30,140,0.06); }
  .add-box-head { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; padding:13px 20px; font-size:14px; font-weight:800; display:flex; align-items:center; gap:8px; }
  .add-box-body { padding:18px 20px; }
  .add-form { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
  .add-form .fg { display:flex; flex-direction:column; }
  .add-form label { font-size:11px; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
  .add-form input[type=date], .add-form input[type=text] {
    border:2px solid #fecdd3; border-radius:8px; padding:8px 12px;
    font-size:13px; outline:none; transition:border-color .15s;
  }
  .add-form input:focus { border-color:#be185d; }
  .add-form .fg-desc { flex:1; min-width:220px; }
  .add-form .fg-desc input { width:100%; }
  .btn-agregar {
    background:linear-gradient(135deg,#be185d,#9d174d); color:#fff;
    border:none; border-radius:8px; padding:9px 22px;
    font-size:13px; font-weight:800; cursor:pointer; white-space:nowrap;
    transition:opacity .18s;
  }
  .btn-agregar:hover { opacity:.86; }

  /* ===== TABLA ===== */
  .fe-table-wrap { background:#fff; border-radius:14px; border:1px solid #fff1f2; overflow:hidden; box-shadow:0 2px 10px rgba(233,30,140,0.06); }
  .fe-table-head { padding:13px 20px; border-bottom:1px solid #fff1f2; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
  .fe-table-head h4 { margin:0; font-size:13px; font-weight:800; color:#2c3e50; display:flex; align-items:center; gap:7px; }
  .fe-table-head h4 i { color:#be185d; }

  .ftable { width:100%; border-collapse:collapse; }
  .ftable thead th { background:linear-gradient(135deg,#1e293b,#334155); color:#fff; padding:11px 14px; font-size:12px; font-weight:700; text-align:left; white-space:nowrap; }
  .ftable tbody td { padding:12px 14px; border-bottom:1px solid #fff1f2; font-size:13px; color:#444; vertical-align:middle; }
  .ftable tbody tr:hover { background:#fff9fb; }
  .ftable tbody tr.row-vencida { opacity:.55; }

  /* Badges */
  .b-activa   { background:#d5f5e3; color:#1e8449; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .b-inactiva { background:#f5f5f5; color:#aaa;    padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .b-vencida  { background:#fde8e8; color:#999;    padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .b-hoy      { background:#fef9e7; color:#b7770d; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .b-pedidos  { background:#e8f4fd; color:#1a6fa3; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; }
  .b-sin      { color:#ddd; font-size:12px; }

  /* Botones acciones */
  .btn-toggle { background:#fff1f2; color:#9d174d; border:none; border-radius:6px; padding:5px 12px; font-size:11px; font-weight:700; cursor:pointer; transition:background .15s; white-space:nowrap; }
  .btn-toggle:hover { background:#fecdd3; }
  .btn-del { background:#fadbd8; color:#922b21; border:none; border-radius:6px; padding:5px 10px; font-size:11px; font-weight:700; cursor:pointer; transition:background .15s; }
  .btn-del:hover { background:#f5b7b1; }
  .btn-ver-pedidos { background:#e8f4fd; color:#1a6fa3; border:none; border-radius:6px; padding:5px 11px; font-size:11px; font-weight:700; cursor:pointer; text-decoration:none; white-space:nowrap; }
  .btn-ver-pedidos:hover { background:#d0eaf8; color:#1a6fa3; }

  /* Modal editar */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:14px; width:100%; max-width:440px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,.18); }
  .modal-head { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; padding:14px 20px; font-size:14px; font-weight:800; display:flex; align-items:center; justify-content:space-between; }
  .modal-body { padding:22px 20px; }
  .modal-fg { display:flex; flex-direction:column; margin-bottom:16px; }
  .modal-fg label { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
  .modal-input { border:2px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; outline:none; transition:border-color .15s; width:100%; }
  .modal-input:focus { border-color:#be185d; }
  .modal-foot { padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; gap:10px; justify-content:flex-end; }

  /* Próxima fecha banner */
  .proxima-banner {
    background:linear-gradient(135deg,#fff9fb,#fff1f2);
    border:1px solid #fecdd3; border-radius:12px;
    padding:14px 20px; margin-bottom:22px;
    display:flex; align-items:center; gap:14px; flex-wrap:wrap;
  }
  .proxima-banner .pb-icon { font-size:28px; color:#be185d; }
  .proxima-banner .pb-label { font-size:11px; color:#aaa; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
  .proxima-banner .pb-fecha { font-size:18px; font-weight:900; color:#7f1d3e; }
  .proxima-banner .pb-desc  { font-size:12px; color:#c48; margin-top:2px; }

  /* Filtro tabs */
  .fe-tabs { display:flex; gap:6px; flex-wrap:wrap; }
  .fe-tab { padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; border:2px solid #fecdd3; color:#9d174d; background:transparent; cursor:pointer; text-decoration:none; transition:all .15s; }
  .fe-tab:hover { background:#fff1f2; }
  .fe-tab.active { background:#be185d; border-color:#be185d; color:#fff; }
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

{{-- Cards resumen --}}
<div class="fe-stats">
  <div class="fe-stat">
    <div class="fe-icon" style="background:#fff1f2; color:#be185d;"><i class="fa fa-calendar-check-o"></i></div>
    <div>
      <div class="fe-num" style="color:#be185d;">{{ $totalActivas }}</div>
      <div class="fe-lbl">Fechas activas próximas</div>
    </div>
  </div>
  <div class="fe-stat">
    <div class="fe-icon" style="background:#e8f4fd; color:#1a6fa3;"><i class="fa fa-shopping-bag"></i></div>
    <div>
      <div class="fe-num" style="color:#1a6fa3;">{{ $totalPedidos }}</div>
      <div class="fe-lbl">Pedidos con fecha asignada</div>
    </div>
  </div>
  <div class="fe-stat">
    <div class="fe-icon" style="background:#fef9e7; color:#b7770d;"><i class="fa fa-list"></i></div>
    <div>
      <div class="fe-num" style="color:#b7770d;">{{ $fechas->count() }}</div>
      <div class="fe-lbl">Total fechas cargadas</div>
    </div>
  </div>
</div>

{{-- Próxima fecha activa --}}
@if($proximaFecha)
@php
  $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
  $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
  $cp    = \Carbon\Carbon::parse($proximaFecha->fecha);
  $eHoy  = $cp->isToday();
@endphp
<div class="proxima-banner">
  <span class="pb-icon"><i class="fa fa-calendar-check-o"></i></span>
  <div>
    <div class="pb-label">{{ $eHoy ? '¡Hoy es día de entrega!' : 'Próxima fecha de entrega' }}</div>
    <div class="pb-fecha">{{ $dias[$cp->dayOfWeek] }} {{ $cp->day }} de {{ $meses[$cp->month-1] }} {{ $cp->year }}</div>
    @if($proximaFecha->descripcion)
      <div class="pb-desc"><i class="fa fa-info-circle"></i> {{ $proximaFecha->descripcion }}</div>
    @endif
  </div>
  @if($proximaFecha->total_pedidos > 0)
    <div style="margin-left:auto;">
      <a href="{{ url('ventas/venta?fecha='.$proximaFecha->fecha) }}" class="btn-ver-pedidos">
        <i class="fa fa-shopping-bag"></i> {{ $proximaFecha->total_pedidos }} pedido(s) asignado(s)
      </a>
    </div>
  @endif
</div>
@endif

{{-- Formulario agregar --}}
<div class="add-box">
  <div class="add-box-head"><i class="fa fa-plus-circle"></i> Agregar nueva fecha disponible</div>
  <div class="add-box-body">
    <form method="POST" action="{{ url('admin/fechas-entrega') }}" class="add-form">
      {{ csrf_field() }}
      <div class="fg">
        <label>Fecha <span style="color:#be185d;">*</span></label>
        <input type="date" name="fecha" required min="{{ date('Y-m-d') }}" value="{{ old('fecha') }}">
      </div>
      <div class="fg fg-desc">
        <label>Descripción (opcional)</label>
        <input type="text" name="descripcion" placeholder="Ej: Entrega zona Asunción, Turno tarde..." value="{{ old('descripcion') }}">
      </div>
      <div class="fg">
        <label>&nbsp;</label>
        <button type="submit" class="btn-agregar">
          <i class="fa fa-calendar-plus-o"></i> Agregar fecha
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Tabla de fechas --}}
<div class="fe-table-wrap">
  <div class="fe-table-head">
    <h4><i class="fa fa-list"></i> Fechas cargadas ({{ $fechas->count() }})</h4>
    <a href="{{ url('admin/fechas-entrega/informe') }}" target="_blank"
       style="background:#1e293b; color:#fff; border-radius:8px; padding:7px 14px; font-size:12px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
      <i class="fa fa-print"></i> Emitir informe
    </a>
    <div class="fe-tabs">
      <a href="#" class="fe-tab active" onclick="filtrarFechas('todas',this)">Todas</a>
      <a href="#" class="fe-tab" onclick="filtrarFechas('proximas',this)">Próximas</a>
      <a href="#" class="fe-tab" onclick="filtrarFechas('vencidas',this)">Vencidas</a>
    </div>
  </div>

  @if($fechas->isEmpty())
    <div style="text-align:center; padding:48px; color:#fda4af;">
      <i class="fa fa-calendar-times-o" style="font-size:44px; display:block; margin-bottom:12px;"></i>
      <p style="font-weight:700; color:#9d174d; margin:0 0 6px;">No hay fechas cargadas todavía.</p>
      <p style="font-size:13px; color:#aaa; margin:0;">Agregá fechas arriba para que los clientes puedan elegirlas al hacer su pedido.</p>
    </div>
  @else
  <div class="table-responsive">
    <table class="ftable" id="tabla-fechas">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Día</th>
          <th>Descripción</th>
          <th>Pedidos asignados</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @php
          $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
          $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
          $hoy   = date('Y-m-d');
        @endphp
        @foreach($fechas as $f)
        @php
          $cf      = \Carbon\Carbon::parse($f->fecha);
          $vencida = $f->fecha < $hoy;
          $esHoy   = $f->fecha === $hoy;
        @endphp
        <tr class="{{ $vencida ? 'row-vencida' : '' }}" data-tipo="{{ $vencida ? 'vencida' : 'proxima' }}">
          <td>
            <b style="font-size:14px; color:{{ $esHoy ? '#be185d' : ($vencida ? '#bbb' : '#2c3e50') }};">
              {{ $cf->format('d/m/Y') }}
            </b>
            @if($esHoy)
              <span class="b-hoy" style="margin-left:6px;"><i class="fa fa-star"></i> Hoy</span>
            @endif
          </td>
          <td style="color:#888;">{{ $dias[$cf->dayOfWeek] }}</td>
          <td style="color:#666; font-size:12px;">{{ $f->descripcion ?? '—' }}</td>
          <td>
            @if($f->total_pedidos > 0)
              <a href="{{ url('ventas/venta') }}" class="b-pedidos">
                <i class="fa fa-shopping-bag"></i> {{ $f->total_pedidos }} pedido(s)
              </a>
            @else
              <span class="b-sin"><i class="fa fa-minus"></i> Sin pedidos</span>
            @endif
          </td>
          <td>
            @if($vencida)
              <span class="b-vencida"><i class="fa fa-clock-o"></i> Vencida</span>
            @elseif($f->activo)
              <span class="b-activa"><i class="fa fa-check"></i> Activa</span>
            @else
              <span class="b-inactiva"><i class="fa fa-times"></i> Inactiva</span>
            @endif
          </td>
          <td>
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
              <button type="button" class="btn-toggle"
                onclick="abrirEditar({{ $f->id }}, '{{ $f->fecha }}', '{{ addslashes($f->descripcion ?? '') }}')"
                style="background:#e8f4fd; color:#1a6fa3;">
                <i class="fa fa-pencil"></i> Editar
              </button>
              @if(!$vencida)
              <form method="POST" action="{{ url('admin/fechas-entrega/'.$f->id.'/toggle') }}" style="display:inline;">
                {{ csrf_field() }}
                <button type="submit" class="btn-toggle">
                  <i class="fa fa-{{ $f->activo ? 'eye-slash' : 'eye' }}"></i>
                  {{ $f->activo ? 'Desactivar' : 'Activar' }}
                </button>
              </form>
              @endif
              <form method="POST" action="{{ url('admin/fechas-entrega/'.$f->id) }}" style="display:inline;"
                onsubmit="return confirm('¿Eliminar la fecha {{ $cf->format('d/m/Y') }}?{{ $f->total_pedidos > 0 ? ' Tiene '.$f->total_pedidos.' pedido(s) asignado(s).' : '' }}')">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn-del"><i class="fa fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- Modal editar fecha --}}
<div class="modal-overlay" id="modal-editar">
  <div class="modal-box">
    <div class="modal-head">
      <span><i class="fa fa-pencil"></i> Editar fecha de entrega</span>
      <button type="button" onclick="cerrarModal()"
        style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer; line-height:1;">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <form id="form-editar" method="POST" action="">
      {{ csrf_field() }}
      {{ method_field('PUT') }}
      <div class="modal-body">
        <div class="modal-fg">
          <label>Fecha <span style="color:#be185d;">*</span></label>
          <input type="date" name="fecha" id="edit-fecha" class="modal-input" required>
        </div>
        <div class="modal-fg">
          <label>Descripción (opcional)</label>
          <input type="text" name="descripcion" id="edit-descripcion"
                 class="modal-input" maxlength="150"
                 placeholder="Ej: Entrega zona Asunción, Turno tarde...">
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" onclick="cerrarModal()"
          style="background:#f1f5f9; color:#64748b; border:none; border-radius:8px; padding:9px 20px; font-size:13px; font-weight:700; cursor:pointer;">
          Cancelar
        </button>
        <button type="submit"
          style="background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border:none; border-radius:8px; padding:9px 22px; font-size:13px; font-weight:800; cursor:pointer;">
          <i class="fa fa-save"></i> Guardar cambios
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function abrirEditar(id, fecha, descripcion) {
  document.getElementById('form-editar').action = '/admin/fechas-entrega/' + id;
  document.getElementById('edit-fecha').value       = fecha;
  document.getElementById('edit-descripcion').value = descripcion;
  document.getElementById('modal-editar').classList.add('open');
}

function cerrarModal() {
  document.getElementById('modal-editar').classList.remove('open');
}

// Cerrar al hacer clic fuera del modal
document.getElementById('modal-editar').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});

function filtrarFechas(tipo, el) {
  document.querySelectorAll('.fe-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');

  document.querySelectorAll('#tabla-fechas tbody tr').forEach(function(tr) {
    if (tipo === 'todas') {
      tr.style.display = '';
    } else if (tipo === 'proximas') {
      tr.style.display = tr.dataset.tipo === 'proxima' ? '' : 'none';
    } else {
      tr.style.display = tr.dataset.tipo === 'vencida' ? '' : 'none';
    }
  });
}
</script>
@endpush

@endsection
