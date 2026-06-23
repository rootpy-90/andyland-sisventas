@extends('layouts.admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen del negocio')
@section('box_title', 'Panel de Control — AndylandPy')
@section('contenido')

<style>
  /* ===== STAT CARDS ===== */
  .stat-card-wrap {
    border-radius:14px; overflow:hidden;
    box-shadow:0 4px 18px rgba(0,0,0,0.12);
    margin-bottom:20px; transition:transform .18s, box-shadow .18s;
  }
  .stat-card-wrap:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,0.16); }
  .stat-card { padding:20px 18px; color:#fff; display:flex; align-items:center; justify-content:space-between; }
  .stat-card .sc-info h2 { margin:0 0 2px; font-size:34px; font-weight:900; line-height:1; }
  .stat-card .sc-info p  { margin:0; font-size:12px; font-weight:600; opacity:.85; text-transform:uppercase; letter-spacing:.5px; }
  .stat-card .sc-icon { font-size:46px; opacity:.2; }
  .card-pink   { background:linear-gradient(135deg,#be185d,#9d174d); }
  .card-dark   { background:linear-gradient(135deg,#2c3e50,#1a252f); }
  .card-orange { background:linear-gradient(135deg,#e67e22,#ca6f1e); }
  .card-green  { background:linear-gradient(135deg,#27ae60,#1e8449); }
  .card-purple { background:linear-gradient(135deg,#8e44ad,#6c3483); }
  .card-teal   { background:linear-gradient(135deg,#16a085,#1abc9c); }
  .stat-footer {
    display:block; text-align:center; padding:8px;
    color:#fff; font-size:11px; font-weight:700;
    text-decoration:none; transition:opacity .18s; opacity:.8;
  }
  .stat-footer:hover { opacity:1; color:#fff; }
  .sf-pink   { background:#9d174d; }
  .sf-dark   { background:#1a252f; }
  .sf-orange { background:#ca6f1e; }
  .sf-green  { background:#1e8449; }
  .sf-purple { background:#6c3483; }
  .sf-teal   { background:#16a085; }

  /* ===== BOXES ===== */
  .dash-box {
    background:#fff; border-radius:14px;
    box-shadow:0 2px 12px rgba(157,23,77,0.07);
    border:1px solid #fff1f2; margin-bottom:20px; overflow:hidden;
  }
  .dash-box-head {
    padding:14px 18px; border-bottom:1px solid #fff1f2;
    display:flex; align-items:center; justify-content:space-between;
  }
  .dash-box-head h4 { margin:0; font-size:13px; font-weight:800; color:#2c3e50; display:flex; align-items:center; gap:7px; }
  .dash-box-head h4 i { color:#be185d; }
  .dash-box-body { padding:16px 18px; }

  /* ===== PEDIDOS PENDIENTES ===== */
  .pedido-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 0; border-bottom:1px solid #fff1f2; gap:8px; flex-wrap:wrap;
  }
  .pedido-item:last-child { border:none; }
  .pi-num   { font-size:13px; font-weight:800; color:#be185d; }
  .pi-info  { font-size:11px; color:#999; margin-top:2px; }
  .pi-total { font-size:14px; font-weight:800; color:#2c3e50; white-space:nowrap; }
  .badge-comp { background:#d5f5e3; color:#1e8449; border-radius:20px; padding:2px 8px; font-size:10px; font-weight:700; }
  .btn-ap {
    background:linear-gradient(135deg,#27ae60,#1e8449); color:#fff;
    border:none; border-radius:6px; padding:5px 11px; font-size:11px;
    font-weight:700; cursor:pointer; text-decoration:none; white-space:nowrap;
  }
  .btn-ap:hover { opacity:.85; color:#fff; }
  .btn-ver {
    color:#aaa; font-size:11px; text-decoration:none;
    border:1px solid #eee; border-radius:6px; padding:4px 9px;
  }
  .btn-ver:hover { color:#be185d; border-color:#f8bbd0; }

  /* ===== STOCK ===== */
  .stock-item { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #fff1f2; font-size:12px; }
  .stock-item:last-child { border:none; }
  .stock-bar-wrap { flex:1; margin:0 10px; background:#fff1f2; border-radius:20px; height:6px; overflow:hidden; }
  .stock-bar { height:100%; border-radius:20px; }
  .s-0   { color:#e74c3c; }
  .s-low { color:#e67e22; }

  /* ===== RESUMEN ===== */
  .res-row { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #fff1f2; font-size:13px; }
  .res-row:last-child { border:none; }
  .res-row .label { color:#888; }
  .res-row .value { font-weight:800; color:#2c3e50; }
  .res-row .value.pink { color:#be185d; }

  /* ===== CHART CANVAS ===== */
  canvas { max-width:100%; }

  /* ===== VER TODO LINK ===== */
  .ver-todo { font-size:11px; color:#be185d; font-weight:700; text-decoration:none; }
  .ver-todo:hover { color:#9d174d; }
</style>

{{-- ===== FILA 1: 6 CARDS ===== --}}
<div class="row">
  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-pink">
        <div class="sc-info">
          <h2>{{ $nPendientes }}</h2>
          <p>Pendientes</p>
        </div>
        <div class="sc-icon"><i class="fa fa-clock-o"></i></div>
      </div>
      <a href="{{ url('ventas/venta?estado=P') }}" class="stat-footer sf-pink">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-green">
        <div class="sc-info">
          <h2>{{ $nAprobados }}</h2>
          <p>Aprobados</p>
        </div>
        <div class="sc-icon"><i class="fa fa-check-circle"></i></div>
      </div>
      <a href="{{ url('ventas/venta?estado=A') }}" class="stat-footer sf-green">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-teal">
        <div class="sc-info">
          <h2>{{ $ventasHoy }}</h2>
          <p>Pedidos hoy</p>
        </div>
        <div class="sc-icon"><i class="fa fa-calendar"></i></div>
      </div>
      <a href="{{ url('ventas/venta') }}" class="stat-footer sf-teal">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-dark">
        <div class="sc-info">
          <h2>{{ $nArticulos }}</h2>
          <p>Artículos</p>
        </div>
        <div class="sc-icon"><i class="fa fa-archive"></i></div>
      </div>
      <a href="{{ url('almacen/articulo') }}" class="stat-footer sf-dark">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-orange">
        <div class="sc-info">
          <h2>{{ $nStockBajo }}</h2>
          <p>Stock crítico</p>
        </div>
        <div class="sc-icon"><i class="fa fa-exclamation-triangle"></i></div>
      </div>
      <a href="{{ url('almacen/articulo?filtrarStock=si') }}" class="stat-footer sf-orange">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>

  <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
    <div class="stat-card-wrap">
      <div class="stat-card card-purple">
        <div class="sc-info">
          <h2>{{ $nClientes }}</h2>
          <p>Clientes</p>
        </div>
        <div class="sc-icon"><i class="fa fa-users"></i></div>
      </div>
      <a href="{{ url('ventas/cliente') }}" class="stat-footer sf-purple">
        <i class="fa fa-arrow-right"></i> Ver
      </a>
    </div>
  </div>
</div>

{{-- ===== FILA 2: GRÁFICOS ===== --}}
<div class="row">

  {{-- Gráfico: Ventas por mes --}}
  <div class="col-lg-7 col-md-12">
    <div class="dash-box">
      <div class="dash-box-head">
        <h4><i class="fa fa-line-chart"></i> Ventas aprobadas por mes (Gs.)</h4>
        <a href="{{ url('ventas/reporte') }}" class="ver-todo">Ver reporte completo <i class="fa fa-arrow-right"></i></a>
      </div>
      <div class="dash-box-body">
        <canvas id="chartVentasMes" height="100"></canvas>
      </div>
    </div>
  </div>

  {{-- Gráfico: Productos más vendidos --}}
  <div class="col-lg-5 col-md-12">
    <div class="dash-box">
      <div class="dash-box-head">
        <h4><i class="fa fa-bar-chart"></i> Productos más vendidos</h4>
      </div>
      <div class="dash-box-body">
        <canvas id="chartProductos" height="135"></canvas>
      </div>
    </div>
  </div>

</div>

{{-- ===== FILA 3: PEDIDOS + RESUMEN + STOCK ===== --}}
<div class="row">

  {{-- Pedidos pendientes --}}
  <div class="col-lg-7 col-md-12">
    <div class="dash-box">
      <div class="dash-box-head">
        <h4><i class="fa fa-clock-o"></i> Pedidos pendientes de aprobación</h4>
        <a href="{{ url('ventas/venta?estado=P') }}" class="ver-todo">Ver todos <i class="fa fa-arrow-right"></i></a>
      </div>
      <div class="dash-box-body" style="padding:6px 18px 12px;">
        @forelse($ultimosPendientes as $p)
        <div class="pedido-item">
          <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
              <span class="pi-num">#{{ $p->num_comprobante }}</span>
              @if($p->comprobante_pago)
                <span class="badge-comp"><i class="fa fa-check"></i> Comp. adjunto</span>
              @endif
            </div>
            <div class="pi-info">
              <i class="fa fa-user"></i> {{ $p->cliente }}
              &nbsp;·&nbsp; <i class="fa fa-credit-card"></i> {{ $p->metodo_pago ?? 'Efectivo' }}
              @if($p->tipo_distribucion)
                &nbsp;·&nbsp; <i class="fa fa-truck"></i> {{ $p->tipo_distribucion }}
              @endif
            </div>
            <div class="pi-info"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($p->fecha_hora)->diffForHumans() }}</div>
          </div>
          <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; justify-content:flex-end;">
            <span class="pi-total">{{ number_format($p->total_venta, 0, ',', '.') }} Gs.</span>
            <a href="{{ URL::action('VentaController@cambiarEstado', $p->idventa) }}" class="btn-ap">
              <i class="fa fa-check"></i> Aprobar
            </a>
            <a href="{{ URL::action('VentaController@show', $p->idventa) }}" class="btn-ver">
              <i class="fa fa-eye"></i>
            </a>
          </div>
        </div>
        @empty
        <div style="text-align:center; padding:28px; color:#f48fb1;">
          <i class="fa fa-check-circle" style="font-size:36px; display:block; margin-bottom:10px; color:#27ae60; opacity:.6;"></i>
          <p style="margin:0; font-weight:700; color:#888;">¡Todo al día! No hay pedidos pendientes.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Resumen + Stock --}}
  <div class="col-lg-5 col-md-12">

    {{-- Resumen financiero --}}
    <div class="dash-box">
      <div class="dash-box-head">
        <h4><i class="fa fa-money"></i> Resumen financiero</h4>
      </div>
      <div class="dash-box-body" style="padding:10px 18px;">
        <div class="res-row">
          <span class="label">Total ventas aprobadas</span>
          <span class="value pink">{{ number_format($totalVentas, 0, ',', '.') }} Gs.</span>
        </div>
        <div class="res-row">
          <span class="label">Ventas aprobadas este mes</span>
          <span class="value pink">{{ number_format($ventasMes, 0, ',', '.') }} Gs.</span>
        </div>
        <div class="res-row">
          <span class="label">Pedidos este mes</span>
          <span class="value">{{ $pedidosMes }}</span>
        </div>
        <div class="res-row">
          <span class="label">Pedidos hoy</span>
          <span class="value">{{ $ventasHoy }}</span>
        </div>
        <div class="res-row">
          <span class="label">Clientes registrados</span>
          <span class="value">{{ $nClientes }}</span>
        </div>
        <div style="margin-top:12px; text-align:right;">
          <a href="{{ url('ventas/reporte') }}" class="ver-todo"><i class="fa fa-bar-chart"></i> Ver reporte completo</a>
        </div>
      </div>
    </div>

    {{-- Stock crítico --}}
    @if($articulosStockBajo->count())
    <div class="dash-box">
      <div class="dash-box-head">
        <h4><i class="fa fa-warning" style="color:#e67e22;"></i> Stock crítico</h4>
        <a href="{{ url('almacen/articulo?filtrarStock=si') }}" class="ver-todo">Ver todos <i class="fa fa-arrow-right"></i></a>
      </div>
      <div class="dash-box-body" style="padding:8px 18px 12px;">
        @foreach($articulosStockBajo as $art)
        @php $pct = min(100, ($art->stock / 5) * 100); $color = $art->stock == 0 ? '#e74c3c' : ($art->stock <= 2 ? '#e67e22' : '#f39c12'); @endphp
        <div class="stock-item">
          <span style="flex:1; font-size:12px; color:#555; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;" title="{{ $art->nombre }}">{{ $art->nombre }}</span>
          <div class="stock-bar-wrap">
            <div class="stock-bar" style="width:{{ $pct }}%; background:{{ $color }};"></div>
          </div>
          <span class="stock-num {{ $art->stock == 0 ? 's-0' : 's-low' }}" style="min-width:50px; text-align:right;">
            {{ $art->stock }} unid.
          </span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// ── Datos desde PHP ──────────────────────────────
const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

const ventasMesData = @json($ventasPorMes);
const labelsVentas  = ventasMesData.map(v => meses[v.mes - 1] + ' ' + String(v.anio).slice(2));
const totalesVentas = ventasMesData.map(v => v.total);
const cantVentas    = ventasMesData.map(v => v.cantidad);

const topProdData    = @json($topProductos);
const labelsProductos = topProdData.map(p => p.nombre.length > 22 ? p.nombre.slice(0,20)+'…' : p.nombre);
const cantProductos   = topProdData.map(p => p.total_vendido);

// ── Colores ─────────────────────────────────────
const rosa   = 'rgba(157,23,77,1)';
const rosaFg = 'rgba(157,23,77,0.15)';
const oscuro = 'rgba(44,62,80,0.85)';

// ── Chart 1: Ventas por mes ──────────────────────
const ctx1 = document.getElementById('chartVentasMes').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: labelsVentas,
    datasets: [
      {
        label: 'Total (Gs.)',
        data: totalesVentas,
        backgroundColor: 'rgba(157,23,77,0.18)',
        borderColor: rosa,
        borderWidth: 2,
        borderRadius: 6,
        yAxisID: 'y',
      },
      {
        label: 'Nro. pedidos',
        data: cantVentas,
        type: 'line',
        borderColor: oscuro,
        backgroundColor: 'transparent',
        borderWidth: 2,
        pointBackgroundColor: oscuro,
        pointRadius: 4,
        tension: 0.35,
        yAxisID: 'y2',
      }
    ]
  },
  options: {
    responsive: true,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 14 } },
      tooltip: {
        callbacks: {
          label: ctx => {
            if (ctx.dataset.yAxisID === 'y') {
              return ' ' + Number(ctx.parsed.y).toLocaleString('es-PY') + ' Gs.';
            }
            return ' ' + ctx.parsed.y + ' pedidos';
          }
        }
      }
    },
    scales: {
      y: {
        position: 'left',
        grid: { color: 'rgba(157,23,77,0.06)' },
        ticks: {
          font: { size: 10 },
          callback: v => {
            if (v >= 1000000) return (v/1000000).toFixed(1) + 'M';
            if (v >= 1000)    return (v/1000).toFixed(0) + 'K';
            return v;
          }
        }
      },
      y2: {
        position: 'right',
        grid: { drawOnChartArea: false },
        ticks: { font: { size: 10 }, stepSize: 1 }
      },
      x: { grid: { display: false }, ticks: { font: { size: 11 } } }
    }
  }
});

// ── Chart 2: Productos más vendidos ─────────────
const ctx2 = document.getElementById('chartProductos').getContext('2d');
const colores = [
  'rgba(157,23,77,0.75)','rgba(127,29,62,0.7)','rgba(44,62,80,0.72)',
  'rgba(22,160,133,0.7)','rgba(230,126,34,0.7)','rgba(142,68,173,0.7)'
];
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: labelsProductos,
    datasets: [{
      label: 'Unidades vendidas',
      data: cantProductos,
      backgroundColor: colores,
      borderRadius: 5,
      borderSkipped: false,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.x + ' unidades' } }
    },
    scales: {
      x: { grid: { color: 'rgba(157,23,77,0.06)' }, ticks: { font:{ size:10 }, stepSize:1 } },
      y: { grid: { display: false }, ticks: { font:{ size:10 } } }
    }
  }
});
</script>
@endpush

@endsection
