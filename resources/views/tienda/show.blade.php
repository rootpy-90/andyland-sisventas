@extends('layouts.tienda')
@section('title', $articulo->nombre)
@section('content')

<style>
  /* Breadcrumb */
  .breadcrumb-nav { font-size:13px; color:#bbb; margin-bottom:18px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
  .breadcrumb-nav a { color:#9d174d; font-weight:600; text-decoration:none; }
  .breadcrumb-nav a:hover { color:#7f1d3e; }
  .breadcrumb-nav .sep { color:#ddd; }
  .breadcrumb-nav .current { color:#888; font-weight:700; }

  /* Layout */
  .prod-detail-wrap {
    display:grid; grid-template-columns:1fr 1fr; gap:36px;
    background:#fff; border-radius:14px; padding:32px;
    box-shadow:0 3px 18px rgba(157,23,77,0.1);
    border:1px solid #fff1f2; align-items:start;
  }
  @media(max-width:768px){ .prod-detail-wrap{ grid-template-columns:1fr; gap:22px; padding:20px; } }

  /* Imagen */
  .prod-detail-img {
    border-radius:12px; background:#fff9fb;
    display:flex; align-items:center; justify-content:center;
    min-height:300px; border:1px solid #fff1f2; overflow:hidden; padding:16px;
    position:relative;
  }
  .prod-detail-img img { max-height:290px; max-width:100%; object-fit:contain; transition:transform .4s; cursor:zoom-in; }
  .prod-detail-img img.zoomed { transform:scale(1.7); cursor:zoom-out; }
  .no-img-placeholder { display:flex; flex-direction:column; align-items:center; color:#fda4af; }
  .no-img-placeholder i { font-size:60px; margin-bottom:8px; }

  /* Info */
  .detail-cat { display:inline-block; background:#fff1f2; color:#7f1d3e; font-size:12px; font-weight:700; padding:4px 14px; border-radius:20px; margin-bottom:12px; text-decoration:none; }
  .detail-cat:hover { background:#fecdd3; }
  .prod-detail-info h1 { font-size:24px; font-weight:800; color:#2c3e50; margin:0 0 10px; line-height:1.3; word-break:break-word; }
  .detail-desc { font-size:14px; color:#777; line-height:1.7; margin-bottom:18px; }
  .detail-price { font-size:34px; font-weight:900; color:#9d174d; margin:0 0 18px; line-height:1; }
  .detail-price span { font-size:16px; font-weight:500; color:#aaa; }
  .detail-divider { border:none; border-top:1px solid #fff1f2; margin:18px 0; }

  .detail-meta-row { display:flex; align-items:center; gap:8px; font-size:13px; color:#888; margin-bottom:9px; }
  .detail-meta-row i { color:#9d174d; width:16px; }
  .detail-meta-row b { color:#444; }

  .stock-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
  .stock-ok    { background:#d5f5e3; color:#1e8449; }
  .stock-low   { background:#fef9e7; color:#b7770d; }
  .stock-out   { background:#fadbd8; color:#922b21; }

  .stock-bar { height:6px; background:#fff1f2; border-radius:3px; margin:8px 0 18px; overflow:hidden; }
  .stock-fill { height:100%; border-radius:3px; background:linear-gradient(90deg, #9d174d, #be185d); }

  /* Qty */
  .qty-section { display:flex; align-items:center; gap:14px; margin-bottom:18px; flex-wrap:wrap; }
  .qty-label { font-size:14px; font-weight:700; color:#666; }
  .qty-control { display:flex; align-items:center; border:2px solid #fecdd3; border-radius:8px; overflow:hidden; }
  .qty-control button { width:38px; height:38px; border:none; background:#fff1f2; font-size:20px; font-weight:700; cursor:pointer; color:#7f1d3e; transition:background .15s; }
  .qty-control button:hover { background:#9d174d; color:#fff; }
  .qty-control input { width:50px; height:38px; border:none; text-align:center; font-size:16px; font-weight:800; color:#2c3e50; outline:none; border-left:1px solid #fecdd3; border-right:1px solid #fecdd3; }

  /* Botones */
  .detail-btn-group { display:flex; gap:10px; flex-wrap:wrap; }
  .btn-detail-cart {
    flex:1; min-width:150px;
    display:flex; align-items:center; justify-content:center; gap:7px;
    background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff;
    border:none; border-radius:10px; padding:13px 18px;
    font-size:14px; font-weight:800; cursor:pointer; transition:opacity .2s;
  }
  .btn-detail-cart:hover { opacity:.85; }
  .btn-detail-cart.success { background:linear-gradient(135deg,#27ae60,#1e8449); }
  .btn-buy-now {
    flex:1; min-width:150px;
    display:flex; align-items:center; justify-content:center; gap:7px;
    background:linear-gradient(135deg,#2c3e50,#1a252f); color:#fff;
    border:none; border-radius:10px; padding:13px 18px;
    font-size:14px; font-weight:800; cursor:pointer; transition:opacity .2s; text-decoration:none;
  }
  .btn-buy-now:hover { opacity:.85; color:#fff; }

  /* ===== RELACIONADOS ===== */
  .related-section { margin-top:36px; }
  .related-title { font-size:16px; font-weight:800; color:#9d174d; margin:0 0 16px; display:flex; align-items:center; gap:8px; }
  .related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:16px; }
  .rel-card { background:#fff; border-radius:12px; border:1px solid #fff1f2; overflow:hidden; transition:transform .2s, box-shadow .2s; text-decoration:none; display:block; }
  .rel-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(157,23,77,0.14); border-color:#fda4af; }
  .rel-img { height:130px; background:#fff9fb; display:flex; align-items:center; justify-content:center; border-bottom:1px solid #fff1f2; overflow:hidden; }
  .rel-img img { max-height:120px; max-width:100%; object-fit:contain; }
  .rel-body { padding:10px 12px; }
  .rel-nombre { font-size:12px; font-weight:700; color:#2c3e50; margin:0 0 4px; line-height:1.3; }
  .rel-precio { font-size:14px; font-weight:900; color:#9d174d; margin:0; }
</style>

{{-- Breadcrumb --}}
<nav class="breadcrumb-nav">
  <a href="{{ url('tienda') }}"><i class="fa fa-home"></i> Tienda</a>
  <span class="sep">›</span>
  <a href="{{ url('tienda?idcat='.$articulo->idcategoria) }}">{{ $articulo->categoria }}</a>
  <span class="sep">›</span>
  <span class="current">{{ str_limit($articulo->nombre, 40) }}</span>
</nav>

<div class="prod-detail-wrap">

  {{-- IMAGEN --}}
  <div class="prod-detail-img">
    <img src="{{ asset('imagenes/articulos/'.rawurlencode($articulo->imagen ?? '')) }}"
         alt="{{ $articulo->nombre }}"
         id="prod-img"
         onerror="this.style.display='none'; document.getElementById('no-img').style.display='flex';"
         onclick="this.classList.toggle('zoomed')">
    <div id="no-img" style="display:none;" class="no-img-placeholder">
      <i class="fa fa-image"></i>
      <span style="font-size:12px;">Sin imagen</span>
    </div>
  </div>

  {{-- INFO --}}
  <div class="prod-detail-info">

    <a href="{{ url('tienda?idcat='.$articulo->idcategoria) }}" class="detail-cat">
      <i class="fa fa-tag"></i> {{ $articulo->categoria }}
    </a>
    <h1>{{ $articulo->nombre }}</h1>

    @if($articulo->descripcion)
      <p class="detail-desc">{{ $articulo->descripcion }}</p>
    @endif

    <div class="detail-price">
      @if($articulo->precio)
        {{ number_format($articulo->precio, 0, ',', '.') }} <span>Gs.</span>
      @else
        <span style="font-size:18px; color:#ccc;">Consultar precio</span>
      @endif
    </div>

    <hr class="detail-divider">

    {{-- Stock --}}
    <div class="detail-meta-row">
      <i class="fa fa-cubes"></i>
      @if($articulo->stock <= 0)
        <span class="stock-badge stock-out"><i class="fa fa-times-circle"></i> Sin stock</span>
      @elseif($articulo->stock <= 3)
        <span class="stock-badge stock-low"><i class="fa fa-exclamation-circle"></i> ¡Solo {{ $articulo->stock }} disponible(s)!</span>
      @else
        <span class="stock-badge stock-ok"><i class="fa fa-check-circle"></i> {{ $articulo->stock }} en stock</span>
      @endif
    </div>

    @if($articulo->stock > 0)
      <div class="stock-bar">
        <div class="stock-fill" style="width:{{ min(100, ($articulo->stock / 20) * 100) }}%;"></div>
      </div>
    @endif

    <div class="detail-meta-row">
      <i class="fa fa-clock-o"></i>
      <span>Entrega estimada: <b>{{ $articulo->tiempo_entrega ?? 'Inmediata' }}</b></span>
    </div>
    <div class="detail-meta-row">
      <i class="fa fa-shield"></i>
      <span>Compra segura con comprobante</span>
    </div>

    <hr class="detail-divider">

    @if($articulo->stock > 0 && $articulo->precio)
      <div class="qty-section">
        <span class="qty-label">Cantidad:</span>
        <div class="qty-control">
          <button onclick="changeQty(-1)">−</button>
          <input type="number" id="qty-input" value="1" min="1" max="{{ $articulo->stock }}" readonly>
          <button onclick="changeQty(+1)">+</button>
        </div>
        <span style="font-size:12px; color:#aaa;">Máx. {{ $articulo->stock }}</span>
      </div>

      <div class="detail-btn-group">
        <button class="btn-detail-cart" id="btn-cart"
          onclick="addToCartDetail(
            {{ $articulo->idarticulo }},
            {{ json_encode($articulo->nombre) }},
            {{ (float)$articulo->precio }},
            {{ json_encode($articulo->imagen ?? '') }},
            {{ (int)$articulo->stock }})">
          <i class="fa fa-shopping-bag"></i> Agregar al carrito
        </button>
        <button class="btn-buy-now" id="btn-buy"
          onclick="buyNow(
            {{ $articulo->idarticulo }},
            {{ json_encode($articulo->nombre) }},
            {{ (float)$articulo->precio }},
            {{ json_encode($articulo->imagen ?? '') }},
            {{ (int)$articulo->stock }})">
          <i class="fa fa-bolt"></i> Comprar ahora
        </button>
      </div>

    @elseif(!$articulo->precio)
      <div style="background:#fff8e1; border-left:4px solid #f39c12; border-radius:8px; padding:12px 16px; font-size:13px; color:#7d6608;">
        <i class="fa fa-info-circle"></i> Este producto no tiene precio cargado. Consultanos por WhatsApp.
      </div>
    @else
      <div style="background:#fadbd8; border-left:4px solid #e74c3c; border-radius:8px; padding:12px 16px; font-size:13px; color:#922b21;">
        <i class="fa fa-exclamation-triangle"></i> Este producto no está disponible actualmente.
      </div>
    @endif

  </div>

</div>

{{-- PRODUCTOS RELACIONADOS --}}
@php
  $relacionados = DB::table('articulo as a')
      ->join('categoria as c', 'a.idcategoria', '=', 'c.idcategoria')
      ->select('a.idarticulo','a.nombre','a.imagen','a.precio','a.stock')
      ->where('a.idcategoria', $articulo->idcategoria)
      ->where('a.idarticulo', '!=', $articulo->idarticulo)
      ->where('a.estado', 'Activo')
      ->where('a.stock', '>', 0)
      ->limit(4)
      ->get();
@endphp

@if($relacionados->count())
<div class="related-section">
  <p class="related-title"><i class="fa fa-th-large" style="color:#9d174d;"></i> Otros productos de {{ $articulo->categoria }}</p>
  <div class="related-grid">
    @foreach($relacionados as $rel)
    <a href="{{ url('tienda/articulo/'.$rel->idarticulo) }}" class="rel-card">
      <div class="rel-img">
        <img src="{{ asset('imagenes/articulos/'.rawurlencode($rel->imagen ?? '')) }}"
             alt="{{ $rel->nombre }}"
             onerror="this.style.display='none'">
      </div>
      <div class="rel-body">
        <p class="rel-nombre">{{ str_limit($rel->nombre, 38) }}</p>
        <p class="rel-precio">
          @if($rel->precio) {{ number_format($rel->precio, 0, ',', '.') }} Gs.
          @else <span style="color:#ccc; font-size:12px;">Consultar</span>
          @endif
        </p>
      </div>
    </a>
    @endforeach
  </div>
</div>
@endif

@push('scripts')
<script>
const MAX_QTY = {{ (int)$articulo->stock }};

function changeQty(delta) {
  const inp = document.getElementById('qty-input');
  inp.value = Math.min(MAX_QTY, Math.max(1, +inp.value + delta));
}

function addToCartDetail(id, nombre, precio, imagen, stock) {
  const qty = +document.getElementById('qty-input').value;
  for (let i = 0; i < qty; i++) Cart.add(id, nombre, precio, imagen, stock);

  const btn = document.getElementById('btn-cart');
  btn.classList.add('success');
  btn.innerHTML = '<i class="fa fa-check"></i> ¡Agregado (' + qty + ')!';
  setTimeout(() => {
    btn.classList.remove('success');
    btn.innerHTML = '<i class="fa fa-shopping-bag"></i> Agregar al carrito';
  }, 1800);

  toggleCart();
}

function buyNow(id, nombre, precio, imagen, stock) {
  const qty = +document.getElementById('qty-input').value;
  for (let i = 0; i < qty; i++) Cart.add(id, nombre, precio, imagen, stock);
  window.location.href = '{{ url("tienda/checkout") }}';
}
</script>
@endpush
@endsection
