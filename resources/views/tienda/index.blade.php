@extends('layouts.tienda')
@section('title', 'Catálogo')
@section('content')

<style>
  /* ===== HEADER ===== */
  .shop-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:10px; }
  .shop-header h2 { font-size:20px; font-weight:800; color:#9d174d; margin:0; }
  .results-count { font-size:13px; color:#fda4af; font-weight:600; }

  /* ===== BARRA DE FILTROS ACTIVOS ===== */
  .active-filters { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
  .filter-tag { background:#fff1f2; color:#7f1d3e; border:1px solid #fecdd3; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; }
  .filter-tag a { color:#7f1d3e; text-decoration:none; font-size:14px; line-height:1; }
  .filter-tag a:hover { color:#9d174d; }

  /* ===== SORT BAR ===== */
  .sort-bar { display:flex; align-items:center; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .sort-bar label { font-size:12px; font-weight:700; color:#aaa; }
  .sort-select { border:1.5px solid #fecdd3; border-radius:20px; padding:5px 12px; font-size:12px; color:#555; outline:none; cursor:pointer; background:#fff; }
  .sort-select:focus { border-color:#9d174d; }

  /* ===== GRID ===== */
  .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:20px; }

  /* ===== CARD ===== */
  .prod-card {
    background:#fff; border-radius:14px;
    box-shadow:0 2px 12px rgba(157,23,77,0.08);
    border:1px solid #fff1f2; overflow:hidden;
    display:flex; flex-direction:column;
    transition:transform .22s, box-shadow .22s;
  }
  .prod-card:hover { transform:translateY(-6px); box-shadow:0 14px 36px rgba(157,23,77,0.17); border-color:#fda4af; }

  /* Imagen */
  .prod-img-wrap {
    position:relative; height:190px;
    background:#fff9fb; overflow:hidden;
    border-bottom:1px solid #fff1f2;
    display:flex; align-items:center; justify-content:center;
  }
  .prod-img-wrap img { max-height:178px; max-width:100%; object-fit:contain; transition:transform .32s; }
  .prod-card:hover .prod-img-wrap img { transform:scale(1.06); }

  .prod-badge-cat { position:absolute; top:9px; left:9px; background:rgba(157,23,77,0.85); color:#fff; font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; }
  .prod-badge-nostock { position:absolute; inset:0; background:rgba(0,0,0,0.3); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800; letter-spacing:1px; }
  .prod-badge-last { position:absolute; top:9px; right:9px; background:linear-gradient(135deg,#e67e22,#ca6f1e); color:#fff; font-size:10px; font-weight:800; padding:3px 9px; border-radius:20px; animation:pulse .88s ease-in-out infinite alternate; }
  @keyframes pulse { from{opacity:.85} to{opacity:1} }

  /* Hover overlay — ver detalle */
  .prod-img-overlay { position:absolute; inset:0; background:rgba(157,23,77,0); display:flex; align-items:center; justify-content:center; transition:background .22s; }
  .prod-card:hover .prod-img-overlay { background:rgba(157,23,77,0.08); }
  .overlay-btn { background:rgba(255,255,255,0.95); color:#9d174d; border-radius:20px; padding:6px 16px; font-size:12px; font-weight:800; text-decoration:none; opacity:0; transform:translateY(6px); transition:opacity .2s, transform .2s; border:1.5px solid #fecdd3; }
  .prod-card:hover .overlay-btn { opacity:1; transform:translateY(0); }

  /* Body */
  .prod-body { padding:13px 15px; flex:1; display:flex; flex-direction:column; }
  .prod-body h4 { font-size:13px; font-weight:700; color:#2c3e50; margin:0 0 6px; line-height:1.35; word-break:break-word; }
  .prod-price { font-size:21px; font-weight:900; color:#9d174d; margin:3px 0 8px; line-height:1; }
  .prod-price small { font-size:12px; font-weight:400; color:#aaa; }
  .prod-meta { font-size:11px; color:#aaa; margin:2px 0; }
  .prod-meta i { color:#fda4af; width:14px; text-align:center; margin-right:3px; }

  /* Footer */
  .prod-foot { padding:10px 14px 13px; border-top:1px solid #fff1f2; display:flex; gap:7px; }
  .btn-add {
    flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
    background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff;
    border:none; border-radius:8px; padding:9px 10px; font-size:13px;
    font-weight:700; cursor:pointer; transition:opacity .18s, transform .15s;
  }
  .btn-add:hover:not(:disabled) { opacity:.86; transform:scale(1.02); }
  .btn-add:disabled { background:linear-gradient(135deg,#ddd,#bbb); cursor:not-allowed; }
  .btn-add.success { background:linear-gradient(135deg,#27ae60,#1e8449); }
  .btn-detail {
    display:flex; align-items:center; justify-content:center;
    background:#fff1f2; color:#7f1d3e; border:none; border-radius:8px;
    padding:9px 11px; font-size:13px; cursor:pointer; text-decoration:none;
    transition:background .15s; flex-shrink:0;
  }
  .btn-detail:hover { background:#fecdd3; color:#9d174d; }

  /* Empty state */
  .empty-state { grid-column:1/-1; text-align:center; padding:72px 20px; color:#fda4af; }
  .empty-state i { font-size:56px; display:block; margin-bottom:14px; }
  .empty-state p { font-size:15px; font-weight:600; color:#7f1d3e; margin:0 0 14px; }

  /* ===== SELECCIÓN MÚLTIPLE ===== */
  .prod-check {
    position:absolute; top:8px; right:8px; z-index:10;
    width:24px; height:24px; display:none; cursor:pointer;
    accent-color:#9d174d;
  }
  .prod-card.modo-seleccion .prod-check { display:block; }
  .prod-card.seleccionado { border-color:#9d174d !important; box-shadow:0 0 0 3px rgba(157,23,77,0.2) !important; }
  .prod-card.seleccionado .prod-img-wrap { background:#fff1f2; }

  /* Barra flotante de compra */
  .compra-bar {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px);
    background:linear-gradient(135deg,#9d174d,#7f1d3e);
    color:#fff; border-radius:30px; padding:14px 28px;
    display:flex; align-items:center; gap:16px;
    box-shadow:0 8px 30px rgba(157,23,77,0.4);
    z-index:3000; transition:transform .3s cubic-bezier(.4,0,.2,1);
    white-space:nowrap;
  }
  .compra-bar.visible { transform:translateX(-50%) translateY(0); }
  .compra-bar .cb-info { font-size:14px; font-weight:700; }
  .compra-bar .cb-total { font-size:16px; font-weight:900; }
  .btn-comprar-sel {
    background:#fff; color:#9d174d; border:none; border-radius:20px;
    padding:8px 20px; font-size:13px; font-weight:800; cursor:pointer;
    transition:opacity .2s;
  }
  .btn-comprar-sel:hover { opacity:.88; }
  .btn-limpiar-sel {
    background:rgba(255,255,255,0.2); color:#fff; border:none;
    border-radius:20px; padding:7px 14px; font-size:12px; font-weight:700; cursor:pointer;
  }

  /* Pagination */
  .pagination-wrap { margin-top:30px; text-align:center; }
  .pagination > li > a, .pagination > li > span { border-radius:6px !important; margin:0 2px; color:#9d174d; border-color:#fecdd3; }
  .pagination > .active > a { background:#9d174d; border-color:#9d174d; color:#fff !important; }
  .pagination > li > a:hover { background:#fff1f2; color:#9d174d; }
</style>

{{-- Header --}}
<div class="shop-header">
  <h2><i class="fa fa-heart" style="color:#9d174d;"></i>
    @if(request('searchText'))
      Resultados para "{{ request('searchText') }}"
    @elseif(request('idcat') && request('idcat') !== 'todos')
      @php $catActual = \DB::table('categoria')->where('idcategoria', request('idcat'))->value('nombre'); @endphp
      {{ $catActual ?? 'Categoría' }}
    @else
      Nuestros Productos
    @endif
  </h2>
  <span class="results-count">{{ $articulos->total() }} producto(s)</span>
</div>

{{-- Filtros activos --}}
@if(request('searchText') || (request('idcat') && request('idcat') !== 'todos'))
<div class="active-filters">
  <span style="font-size:12px; color:#aaa; font-weight:600;">Filtros activos:</span>
  @if(request('searchText'))
    <span class="filter-tag">
      <i class="fa fa-search"></i> "{{ request('searchText') }}"
      <a href="{{ url('tienda'.(request('idcat') ? '?idcat='.request('idcat') : '')) }}">&times;</a>
    </span>
  @endif
  @if(request('idcat') && request('idcat') !== 'todos')
    <span class="filter-tag">
      <i class="fa fa-tag"></i> {{ $catActual ?? 'Categoría' }}
      <a href="{{ url('tienda'.(request('searchText') ? '?searchText='.urlencode(request('searchText')) : '')) }}">&times;</a>
    </span>
  @endif
  <a href="{{ url('tienda') }}" style="font-size:12px; color:#bbb; text-decoration:none;">Limpiar todo</a>
</div>
@endif

{{-- Barra selección múltiple --}}
<div class="sort-bar" style="justify-content:space-between;">
  <div style="display:flex; align-items:center; gap:8px;">
    <label><i class="fa fa-sort"></i> Ordenar:</label>

  <label><i class="fa fa-sort"></i> Ordenar:</label>
    <select class="sort-select" id="sort-select" onchange="sortProducts(this.value)">
      <option value="default" {{ !request('orden') ? 'selected':'' }}>Relevancia</option>
      <option value="precio_asc"  {{ request('orden')==='precio_asc'  ? 'selected':'' }}>Precio: menor a mayor</option>
      <option value="precio_desc" {{ request('orden')==='precio_desc' ? 'selected':'' }}>Precio: mayor a menor</option>
      <option value="nombre_asc"  {{ request('orden')==='nombre_asc'  ? 'selected':'' }}>Nombre A–Z</option>
      <option value="stock_desc"  {{ request('orden')==='stock_desc'  ? 'selected':'' }}>Mayor stock</option>
    </select>
  </div>
  <button onclick="toggleModoSeleccion()" id="btn-modo-sel"
    style="background:#fff; border:2px solid #fecdd3; color:#9d174d; border-radius:20px; padding:5px 14px; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .2s;">
    <i class="fa fa-check-square-o"></i> Seleccionar varios
  </button>
</div>

{{-- Grid de productos --}}
<div class="product-grid" id="product-grid">
  @forelse($articulos as $art)
  <div class="prod-card" data-precio="{{ $art->precio ?? 0 }}" data-nombre="{{ $art->nombre }}" data-stock="{{ $art->stock }}">
    <div class="prod-img-wrap">
      {{-- Checkbox selección múltiple --}}
      @if($art->stock > 0 && $art->precio)
      <input type="checkbox" class="prod-check"
        id="chk-{{ $art->idarticulo }}"
        data-id="{{ $art->idarticulo }}"
        data-nombre="{{ $art->nombre }}"
        data-precio="{{ (float)$art->precio }}"
        data-imagen="{{ $art->imagen ?? '' }}"
        data-stock="{{ (int)$art->stock }}"
        onchange="toggleSeleccion(this)">
      @endif
      <span class="prod-badge-cat">{{ $art->categoria }}</span>
      @if($art->stock > 0 && $art->stock <= 3)
        <span class="prod-badge-last"><i class="fa fa-fire"></i> ¡Últimas!</span>
      @endif
      <img src="{{ asset('imagenes/articulos/'.rawurlencode($art->imagen ?? '')) }}"
           alt="{{ $art->nombre }}"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
      <div style="display:none; flex-direction:column; align-items:center; color:#fda4af;">
        <i class="fa fa-image" style="font-size:40px; margin-bottom:6px;"></i>
        <span style="font-size:11px;">Sin imagen</span>
      </div>
      @if($art->stock <= 0)
        <div class="prod-badge-nostock"><i class="fa fa-ban"></i> Sin Stock</div>
      @endif
      <div class="prod-img-overlay">
        <a href="{{ url('tienda/articulo/'.$art->idarticulo) }}" class="overlay-btn">
          <i class="fa fa-eye"></i> Ver detalle
        </a>
      </div>
    </div>

    <div class="prod-body">
      <h4>{{ $art->nombre }}</h4>
      <div class="prod-price">
        @if($art->precio)
          {{ number_format($art->precio, 0, ',', '.') }} <small>Gs.</small>
        @else
          <span style="font-size:13px; color:#fda4af; font-weight:600;">Consultar precio</span>
        @endif
      </div>
      <p class="prod-meta"><i class="fa fa-cubes"></i>
        @if($art->stock <= 0)
          <span style="color:#e74c3c; font-weight:700;">Sin stock</span>
        @elseif($art->stock <= 3)
          <span style="color:#e67e22; font-weight:700;">¡Solo {{ $art->stock }} disponible(s)!</span>
        @else
          Stock: <b>{{ $art->stock }}</b> unid.
        @endif
      </p>
      <p class="prod-meta"><i class="fa fa-clock-o"></i> {{ $art->tiempo_entrega ?? 'Entrega inmediata' }}</p>
    </div>

    <div class="prod-foot">
      @if($art->stock > 0 && $art->precio)
        <button class="btn-add" id="btn-{{ $art->idarticulo }}"
          onclick="addCart(this,
            {{ $art->idarticulo }},
            {{ json_encode($art->nombre) }},
            {{ (float)$art->precio }},
            {{ json_encode($art->imagen ?? '') }},
            {{ (int)$art->stock }})">
          <i class="fa fa-shopping-bag"></i> Agregar
        </button>
      @else
        <button class="btn-add" disabled>
          <i class="fa fa-ban"></i> {{ $art->stock <= 0 ? 'Sin stock' : 'Sin precio' }}
        </button>
      @endif
      <a href="{{ url('tienda/articulo/'.$art->idarticulo) }}" class="btn-detail" title="Ver detalle">
        <i class="fa fa-eye"></i>
      </a>
    </div>
  </div>

  @empty
  <div class="empty-state">
    <i class="fa fa-search"></i>
    <p>No se encontraron productos.</p>
    @if(request('searchText') || request('idcat'))
      <a href="{{ url('tienda') }}" style="color:#9d174d; font-weight:700; font-size:14px; text-decoration:none;">
        <i class="fa fa-arrow-left"></i> Ver todo el catálogo
      </a>
    @endif
  </div>
  @endforelse
</div>

{{-- Barra flotante de compra múltiple --}}
<div class="compra-bar" id="compra-bar">
  <div>
    <div class="cb-info"><span id="cb-cant">0</span> producto(s) seleccionado(s)</div>
    <div class="cb-total">Total: <span id="cb-total">0</span> Gs.</div>
  </div>
  <button class="btn-comprar-sel" onclick="comprarSeleccionados()">
    <i class="fa fa-shopping-bag"></i> Agregar al carrito
  </button>
  <button class="btn-limpiar-sel" onclick="limpiarSeleccion()">
    <i class="fa fa-times"></i> Cancelar
  </button>
</div>

<div class="pagination-wrap">
  {{ $articulos->appends(request()->query())->render() }}
</div>

@push('scripts')
<script>
/* ── SELECCIÓN MÚLTIPLE ── */
let modoSeleccion = false;
const seleccionados = {};

function toggleModoSeleccion() {
  modoSeleccion = !modoSeleccion;
  const btn = document.getElementById('btn-modo-sel');

  document.querySelectorAll('.prod-card').forEach(card => {
    card.classList.toggle('modo-seleccion', modoSeleccion);
  });

  if (!modoSeleccion) {
    limpiarSeleccion();
    btn.innerHTML = '<i class="fa fa-check-square-o"></i> Seleccionar varios';
    btn.style.background = '#fff';
    btn.style.color = '#9d174d';
  } else {
    btn.innerHTML = '<i class="fa fa-times"></i> Cancelar selección';
    btn.style.background = '#9d174d';
    btn.style.color = '#fff';
  }
}

function toggleSeleccion(chk) {
  const card = chk.closest('.prod-card');
  if (chk.checked) {
    seleccionados[chk.dataset.id] = {
      id:     +chk.dataset.id,
      nombre: chk.dataset.nombre,
      precio: +chk.dataset.precio,
      imagen: chk.dataset.imagen,
      stock:  +chk.dataset.stock,
    };
    card.classList.add('seleccionado');
  } else {
    delete seleccionados[chk.dataset.id];
    card.classList.remove('seleccionado');
  }
  actualizarBarra();
}

function actualizarBarra() {
  const items = Object.values(seleccionados);
  const barra = document.getElementById('compra-bar');
  const cant  = document.getElementById('cb-cant');
  const total = document.getElementById('cb-total');

  if (items.length > 0) {
    cant.textContent  = items.length;
    total.textContent = items.reduce((s, x) => s + x.precio, 0)
                             .toLocaleString('es-PY');
    barra.classList.add('visible');
  } else {
    barra.classList.remove('visible');
  }
}

function comprarSeleccionados() {
  const items = Object.values(seleccionados);
  if (!items.length) return;

  items.forEach(it => Cart.add(it.id, it.nombre, it.precio, it.imagen, it.stock));

  limpiarSeleccion();
  toggleModoSeleccion();
  toggleCart(); // Abrir drawer del carrito
}

function limpiarSeleccion() {
  Object.keys(seleccionados).forEach(k => delete seleccionados[k]);
  document.querySelectorAll('.prod-check').forEach(c => {
    c.checked = false;
    c.closest('.prod-card').classList.remove('seleccionado');
  });
  actualizarBarra();
}

function addCart(btn, id, nombre, precio, imagen, stock) {
  Cart.add(id, nombre, precio, imagen, stock);

  const orig = btn.innerHTML;
  btn.classList.add('success');
  btn.innerHTML = '<i class="fa fa-check"></i> ¡Agregado!';
  btn.disabled = true;

  setTimeout(() => {
    btn.classList.remove('success');
    btn.innerHTML = orig;
    btn.disabled = false;
  }, 1600);
}

function sortProducts(orden) {
  const url = new URL(window.location.href);
  if (orden === 'default') url.searchParams.delete('orden');
  else url.searchParams.set('orden', orden);
  url.searchParams.delete('page');
  window.location.href = url.toString();
}
</script>
@endpush
@endsection
