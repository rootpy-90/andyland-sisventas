@extends('layouts.admin')
@section('page_title', 'Reporte de Ventas')
@section('page_subtitle', 'Análisis del período seleccionado')
@section('box_title', 'Reporte de Ventas')
@section('contenido')

<style>
  /* ---- Accesos rápidos ---- */
  .quick-btns { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
  .quick-btn {
    padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700;
    border:2px solid #fecdd3; color:#9d174d; background:#fff;
    cursor:pointer; text-decoration:none; transition:all .15s;
  }
  .quick-btn:hover { background:#fff1f2; color:#7f1d3e; }

  /* ---- Filtros ---- */
  .filtro-form {
    background:#fff; border:1px solid #fff1f2; border-radius:12px;
    padding:16px 20px; margin-bottom:20px;
    display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
  }
  .filtro-form .fg { display:flex; flex-direction:column; }
  .filtro-form label { font-size:11px; font-weight:700; color:#be185d; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px; }
  .filtro-form input, .filtro-form select {
    border:1.5px solid #fecdd3; border-radius:8px; padding:7px 12px;
    font-size:13px; color:#333; background:#fff; min-width:140px; outline:none;
  }
  .filtro-form input:focus, .filtro-form select:focus { border-color:#be185d; }
  .btn-filtrar { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border:none; border-radius:8px; padding:8px 20px; font-size:13px; font-weight:800; cursor:pointer; }
  .btn-imprimir { background:#1a252f; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }

  /* ---- Cards resumen ---- */
  .res-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:12px; margin-bottom:20px; }
  .res-card { background:#fff; border:1px solid #fff1f2; border-radius:12px; padding:14px 16px; text-align:center; box-shadow:0 2px 8px rgba(233,30,140,0.05); }
  .res-card .rc-num { font-size:24px; font-weight:900; color:#be185d; line-height:1.1; }
  .res-card .rc-label { font-size:10px; color:#aaa; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }
  .res-card.dark { background:linear-gradient(135deg,#1e293b,#334155); border:none; }
  .res-card.dark .rc-num { color:#fda4af; font-size:18px; }
  .res-card.dark .rc-label { color:rgba(255,255,255,0.45); }

  /* ---- Gráfico + Métodos de pago ---- */
  .mid-grid { display:grid; grid-template-columns:1fr 280px; gap:16px; margin-bottom:20px; }
  @media(max-width:860px){ .mid-grid{ grid-template-columns:1fr; } }
  .panel { background:#fff; border:1px solid #fff1f2; border-radius:12px; padding:16px 18px; box-shadow:0 2px 8px rgba(233,30,140,0.05); }
  .panel-title { font-size:12px; font-weight:800; color:#2c3e50; text-transform:uppercase; letter-spacing:.5px; margin:0 0 14px; display:flex; align-items:center; gap:6px; }
  .panel-title i { color:#be185d; }

  /* Métodos de pago */
  .metodo-row { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #fff1f2; }
  .metodo-row:last-child { border:none; }
  .metodo-bar-wrap { flex:1; background:#fff1f2; border-radius:20px; height:7px; overflow:hidden; }
  .metodo-bar { height:100%; border-radius:20px; background:linear-gradient(90deg,#be185d,#9d174d); }
  .metodo-label { font-size:12px; color:#555; font-weight:600; min-width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .metodo-total { font-size:11px; color:#be185d; font-weight:800; white-space:nowrap; text-align:right; min-width:80px; }

  /* ---- Tabla ---- */
  .rtable { width:100%; border-collapse:collapse; font-size:12px; }
  .rtable thead th { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; padding:9px 11px; text-align:left; white-space:nowrap; }
  .rtable tbody td { padding:9px 11px; border-bottom:1px solid #fff1f2; vertical-align:middle; color:#444; }
  .rtable tbody tr:hover { background:#fff9fb; }
  .rtable tfoot td { padding:10px 11px; font-weight:800; background:#fff9fb; border-top:2px solid #fff1f2; }

  .badge-P { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }
  .badge-A { background:#d5f5e3; color:#1e8449; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }
  .badge-C { background:#fadbd8; color:#922b21; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }

  .toggle-det { background:none; border:none; color:#bbb; cursor:pointer; font-size:11px; padding:2px 6px; border-radius:4px; }
  .toggle-det:hover { color:#be185d; background:#fff1f2; }
  .det-list { display:none; margin-top:4px; padding-left:4px; }
  .det-list.open { display:block; }
  .det-list li { list-style:none; font-size:11px; color:#888; padding:1px 0; }
  .det-list li span { color:#be185d; font-weight:700; }

  /* ---- PRINT ---- */
  @media print {
    .print-hide, .quick-btns, .filtro-form, .mid-grid,
    .sidebar, .main-header, .content-header, .breadcrumb { display:none !important; }
    .print-header { display:block !important; }
    .res-grid { grid-template-columns:repeat(6,1fr) !important; }
    .content-wrapper { margin-left:0 !important; }
    .box, .box-body { box-shadow:none !important; border:none !important; }
    .rtable thead th { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .det-list { display:block !important; }
  }

  /* ---- Encabezado impresión ---- */
  .print-header {
    display:none; text-align:center; border-bottom:3px solid #be185d;
    padding-bottom:14px; margin-bottom:20px;
  }
  .print-header h2 { margin:0 0 4px; font-size:20px; color:#7f1d3e; }
  .print-header p  { margin:0; font-size:12px; color:#666; }
  .print-firma { display:none; margin-top:40px; padding-top:16px; border-top:1px solid #ddd; }
  @media print {
    .print-firma { display:flex; justify-content:space-between; }
    .print-firma .firma-box { text-align:center; width:30%; }
    .print-firma .firma-linea { border-top:1px solid #333; padding-top:6px; font-size:11px; color:#666; }
  }
</style>

{{-- Encabezado solo visible al imprimir --}}
<div class="print-header">
  <h2>ANDYLAND — Reporte de Ventas</h2>
  <p>Período: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
    @if($estado) &nbsp;·&nbsp; Estado: {{ ['P'=>'Pendiente','A'=>'Aprobado','C'=>'Cancelado'][$estado] ?? '' }} @endif
  </p>
  <p>Generado el {{ now()->format('d/m/Y H:i') }} hs</p>
</div>

{{-- Accesos rápidos de período --}}
<div class="quick-btns print-hide">
  <span style="font-size:12px; color:#aaa; font-weight:700; align-self:center;">Período rápido:</span>
  @php
    $hoy    = date('Y-m-d');
    $lunes  = date('Y-m-d', strtotime('monday this week'));
    $dom    = date('Y-m-d', strtotime('sunday this week'));
    $m1     = date('Y-m-01');
    $m2     = date('Y-m-d');
    $pm1    = date('Y-m-01', strtotime('first day of last month'));
    $pm2    = date('Y-m-t', strtotime('last day of last month'));
    $a1     = date('Y-01-01');
    $a2     = date('Y-12-31');
  @endphp
  <a href="{{ url('ventas/reporte?desde='.$hoy.'&hasta='.$hoy) }}" class="quick-btn"><i class="fa fa-circle"></i> Hoy</a>
  <a href="{{ url('ventas/reporte?desde='.$lunes.'&hasta='.$dom) }}" class="quick-btn"><i class="fa fa-calendar"></i> Esta semana</a>
  <a href="{{ url('ventas/reporte?desde='.$m1.'&hasta='.$m2) }}" class="quick-btn"><i class="fa fa-calendar-o"></i> Este mes</a>
  <a href="{{ url('ventas/reporte?desde='.$pm1.'&hasta='.$pm2) }}" class="quick-btn"><i class="fa fa-calendar-minus-o"></i> Mes anterior</a>
  <a href="{{ url('ventas/reporte?desde='.$a1.'&hasta='.$a2) }}" class="quick-btn"><i class="fa fa-calendar-check-o"></i> Este año</a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ url('ventas/reporte') }}" class="filtro-form print-hide">
  <div class="fg">
    <label>Desde</label>
    <input type="date" name="desde" value="{{ $desde }}">
  </div>
  <div class="fg">
    <label>Hasta</label>
    <input type="date" name="hasta" value="{{ $hasta }}">
  </div>
  <div class="fg">
    <label>Estado</label>
    <select name="estado">
      <option value=""  {{ $estado===''  ? 'selected':'' }}>Todos</option>
      <option value="P" {{ $estado==='P' ? 'selected':'' }}>Pendiente</option>
      <option value="A" {{ $estado==='A' ? 'selected':'' }}>Aprobado</option>
      <option value="C" {{ $estado==='C' ? 'selected':'' }}>Cancelado</option>
    </select>
  </div>
  <div class="fg" style="margin-left:auto; flex-direction:row; gap:8px; align-items:flex-end;">
    <button type="submit" class="btn-filtrar"><i class="fa fa-search"></i> Filtrar</button>
    <button type="button" onclick="window.print()" class="btn-imprimir"><i class="fa fa-print"></i> Imprimir</button>
  </div>
</form>

{{-- Período activo --}}
<div style="margin-bottom:16px; font-size:13px; color:#888;">
  Mostrando:
  <strong style="color:#333;">{{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}</strong>
  al <strong style="color:#333;">{{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</strong>
  @if($estado)
    &nbsp;·&nbsp; Estado: <strong style="color:#be185d;">{{ ['P'=>'Pendiente','A'=>'Aprobado','C'=>'Cancelado'][$estado] ?? $estado }}</strong>
  @endif
</div>

{{-- Cards resumen --}}
<div class="res-grid">
  <div class="res-card">
    <div class="rc-num">{{ $totales['cantidad'] }}</div>
    <div class="rc-label">Total pedidos</div>
  </div>
  <div class="res-card">
    <div class="rc-num" style="color:#27ae60;">{{ $totales['aprobadas'] }}</div>
    <div class="rc-label">Aprobados</div>
  </div>
  <div class="res-card">
    <div class="rc-num" style="color:#b7770d;">{{ $totales['pendientes'] }}</div>
    <div class="rc-label">Pendientes</div>
  </div>
  <div class="res-card">
    <div class="rc-num" style="color:#922b21;">{{ $totales['canceladas'] }}</div>
    <div class="rc-label">Cancelados</div>
  </div>
  <div class="res-card dark">
    <div class="rc-num">{{ number_format($totales['suma'],0,',','.') }} Gs.</div>
    <div class="rc-label">Total (sin cancelados)</div>
  </div>
  <div class="res-card" style="border-color:#d5f5e3;">
    <div class="rc-num" style="color:#1e8449; font-size:18px;">{{ number_format($totales['promedio'],0,',','.') }} Gs.</div>
    <div class="rc-label">Ticket promedio</div>
  </div>
</div>

{{-- Gráfico por día + Desglose por método de pago --}}
@if(!$ventas->isEmpty())
<div class="mid-grid print-hide">

  {{-- Gráfico ventas por día --}}
  <div class="panel">
    <p class="panel-title"><i class="fa fa-bar-chart"></i> Ventas por día en el período</p>
    <canvas id="chartDia" height="80"></canvas>
  </div>

  {{-- Métodos de pago --}}
  <div class="panel">
    <p class="panel-title"><i class="fa fa-credit-card"></i> Por método de pago</p>
    @php $maxMetodo = $porMetodo->max('total') ?: 1; @endphp
    @forelse($porMetodo as $m)
    <div class="metodo-row">
      <div class="metodo-label" title="{{ $m->metodo }}">{{ $m->metodo }}</div>
      <div class="metodo-bar-wrap">
        <div class="metodo-bar" style="width:{{ ($m->total / $maxMetodo) * 100 }}%;"></div>
      </div>
      <div class="metodo-total">{{ number_format($m->total,0,',','.') }} Gs.</div>
    </div>
    @empty
      <p style="color:#aaa; font-size:13px; text-align:center; margin:20px 0;">Sin datos</p>
    @endforelse
  </div>

</div>
@endif

{{-- Tabla --}}
@if($ventas->isEmpty())
<div style="text-align:center; padding:44px; color:#fda4af; background:#fff; border-radius:12px; border:1px solid #fff1f2;">
  <i class="fa fa-inbox" style="font-size:44px; display:block; margin-bottom:12px;"></i>
  <p style="font-weight:700; color:#9d174d; margin:0 0 4px;">Sin resultados para el período seleccionado.</p>
  <p style="font-size:13px; color:#aaa; margin:0;">Probá con otro rango de fechas o quitá el filtro de estado.</p>
</div>
@else
<div class="table-responsive" style="border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(233,30,140,0.07);">
  <table class="rtable">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Nro.</th>
        <th>Cliente</th>
        <th>Método pago</th>
        <th>Tipo comp.</th>
        <th>Productos</th>
        <th style="text-align:right;">Total Gs.</th>
        <th>Estado</th>
        <th class="print-hide"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($ventas as $v)
      <tr>
        <td style="white-space:nowrap; color:#888; font-size:11px;">
          {{ \Carbon\Carbon::parse($v->fecha_hora)->format('d/m/Y') }}<br>
          {{ \Carbon\Carbon::parse($v->fecha_hora)->format('H:i') }} hs
        </td>
        <td style="font-weight:800; color:#be185d;">#{{ $v->num_comprobante }}</td>
        <td style="font-weight:600;">{{ $v->nombre }}</td>
        <td style="font-size:11px; color:#666;">{{ $v->metodo_pago ?? 'Efectivo' }}</td>
        <td style="font-size:11px; color:#666;">{{ $v->tipo_comprobante }}</td>
        <td>
          <button class="toggle-det" onclick="toggleDet({{ $v->idventa }})">
            <i class="fa fa-list"></i> {{ count($v->detalles) }} ítem(s)
          </button>
          <ul class="det-list" id="det-{{ $v->idventa }}">
            @foreach($v->detalles as $d)
            <li>{{ $d->articulo }} × {{ $d->cantidad }} — <span>{{ number_format($d->precio_venta,0,',','.') }} Gs.</span></li>
            @endforeach
          </ul>
        </td>
        <td style="text-align:right; font-weight:800; color:#be185d; white-space:nowrap;">
          {{ number_format($v->total_venta,0,',','.') }} Gs.
        </td>
        <td>
          @if($v->estado=='A') <span class="badge-A"><i class="fa fa-check"></i> Aprobado</span>
          @elseif($v->estado=='P') <span class="badge-P"><i class="fa fa-clock-o"></i> Pendiente</span>
          @else <span class="badge-C"><i class="fa fa-times"></i> Cancelado</span>
          @endif
        </td>
        <td class="print-hide" style="white-space:nowrap;">
          <a href="{{ URL::action('VentaController@show', $v->idventa) }}"
             style="color:#aaa; font-size:11px; text-decoration:none; border:1px solid #eee; border-radius:5px; padding:3px 8px;">
            <i class="fa fa-eye"></i>
          </a>
        </td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="6" style="text-align:right; color:#888; font-size:12px;">TOTAL DEL PERÍODO (sin cancelados)</td>
        <td style="text-align:right; color:#be185d; font-size:15px; white-space:nowrap;">
          {{ number_format($totales['suma'],0,',','.') }} Gs.
        </td>
        <td colspan="2"></td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- Firmas para impresión --}}
<div class="print-firma">
  <div class="firma-box">
    <div style="height:40px;"></div>
    <div class="firma-linea">Responsable del reporte</div>
  </div>
  <div class="firma-box">
    <div style="height:40px;"></div>
    <div class="firma-linea">Gerencia / Administración</div>
  </div>
  <div class="firma-box">
    <div style="height:40px;"></div>
    <div class="firma-linea">Contador / Auditor</div>
  </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Toggle detalles
function toggleDet(id) {
  const el = document.getElementById('det-' + id);
  if (el) el.classList.toggle('open');
}

// Gráfico ventas por día
@if(!$ventas->isEmpty())
const porDiaData = @json($porDia);
if (porDiaData.length > 0) {
  const ctx = document.getElementById('chartDia');
  if (ctx) {
    new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: porDiaData.map(d => {
          const p = d.dia.split('-');
          return p[2] + '/' + p[1];
        }),
        datasets: [{
          label: 'Total Gs.',
          data: porDiaData.map(d => d.total),
          backgroundColor: 'rgba(233,30,140,0.18)',
          borderColor: 'rgba(233,30,140,1)',
          borderWidth: 2,
          borderRadius: 4,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ' ' + Number(ctx.parsed.y).toLocaleString('es-PY') + ' Gs.'
            }
          }
        },
        scales: {
          y: {
            grid: { color: 'rgba(233,30,140,0.06)' },
            ticks: {
              font: { size: 10 },
              callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v
            }
          },
          x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
      }
    });
  }
}
@endif
</script>
@endpush

@endsection
