<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Andyland PY | @yield('title', 'Tienda')</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    /* ===================== BASE ===================== */
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #fdf4f8; margin: 0; padding: 0; color: #333; }
    a { text-decoration: none; }

    /* ===================== NAVBAR ===================== */
    .shop-nav {
      background: linear-gradient(135deg, #9d174d 0%, #7f1d3e 100%);
      position: sticky; top: 0; z-index: 1000;
      box-shadow: 0 3px 14px rgba(157,23,77,0.35);
    }
    .shop-nav .nav-inner {
      max-width: 1240px; margin: 0 auto;
      padding: 0 16px;
      display: flex; align-items: center; height: 68px; gap: 14px;
    }

    /* Logo */
    .brand-logo {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none; flex-shrink: 0;
    }
    .brand-logo img {
      height: 52px; width: 52px;
      object-fit: cover; border-radius: 50%;
      border: 2px solid rgba(255,255,255,0.5);
      background: #fff;
    }
    .brand-logo .brand-text {
      display: flex; flex-direction: column; line-height: 1.1;
    }
    .brand-logo .brand-name {
      font-size: 18px; font-weight: 900; color: #fff; letter-spacing: -0.3px;
    }
    .brand-logo .brand-sub {
      font-size: 11px; color: rgba(255,255,255,0.75); font-weight: 500; letter-spacing: 0.5px;
    }

    /* Search */
    .nav-search { flex: 1; max-width: 420px; }
    .nav-search form { position: relative; }
    .nav-search input {
      width: 100%; padding: 9px 40px 9px 16px;
      border: none; border-radius: 24px; font-size: 14px; outline: none;
      background: rgba(255,255,255,0.2); color: #fff;
      transition: background 0.2s;
    }
    .nav-search input::placeholder { color: rgba(255,255,255,0.65); }
    .nav-search input:focus { background: rgba(255,255,255,0.32); }
    .nav-search .ico {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      color: rgba(255,255,255,0.75); font-size: 14px;
    }

    /* Nav right */
    .nav-right { display: flex; align-items: center; gap: 10px; margin-left: auto; flex-shrink: 0; }
    .nav-link-pill {
      color: rgba(255,255,255,0.92); font-size: 13px; font-weight: 600;
      padding: 6px 13px; border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.35);
      background: rgba(255,255,255,0.14);
      transition: background 0.2s; display: inline-flex; align-items: center; gap: 5px;
    }
    .nav-link-pill:hover { background: rgba(255,255,255,0.28); color: #fff; }

    /* Cart button */
    .cart-toggle {
      position: relative; cursor: pointer;
      background: rgba(255,255,255,0.16); border: 1px solid rgba(255,255,255,0.35);
      color: #fff; border-radius: 20px; padding: 6px 14px;
      display: inline-flex; align-items: center; gap: 7px;
      font-size: 14px; font-weight: 700; transition: background 0.2s;
      white-space: nowrap;
    }
    .cart-toggle:hover { background: rgba(255,255,255,0.28); }
    .cart-count {
      position: absolute; top: -7px; right: -7px;
      background: #fff; color: #9d174d;
      width: 20px; height: 20px; border-radius: 50%;
      font-size: 11px; font-weight: 900;
      display: none; align-items: center; justify-content: center;
    }

    /* ===================== CATEGORY BAR ===================== */
    .cat-bar {
      background: #fff; border-bottom: 2px solid #fff1f2;
      position: sticky; top: 68px; z-index: 900;
      box-shadow: 0 2px 8px rgba(157,23,77,0.06);
      overflow-x: auto; white-space: nowrap;
    }
    .cat-bar::-webkit-scrollbar { height: 3px; }
    .cat-bar::-webkit-scrollbar-thumb { background: #9d174d; border-radius: 3px; }
    .cat-bar .inner {
      max-width: 1240px; margin: 0 auto;
      padding: 10px 20px;
      display: inline-flex; gap: 8px; align-items: center;
    }
    .cat-pill {
      padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 600;
      border: 2px solid #fecdd3; color: #7f1d3e; background: transparent;
      cursor: pointer; text-decoration: none; transition: all 0.18s;
      display: inline-block; white-space: nowrap;
    }
    .cat-pill:hover { border-color: #9d174d; color: #9d174d; background: #fff1f2; }
    .cat-pill.active { background: #9d174d; border-color: #9d174d; color: #fff; }

    /* ===================== MAIN ===================== */
    .shop-main { max-width: 1240px; margin: 0 auto; padding: 28px 20px 60px; }

    /* ===================== ALERTS ===================== */
    .alert { border-radius: 8px; margin-bottom: 20px; border: none; border-left: 4px solid; }
    .alert-success { background: #fff1f2; border-color: #9d174d; color: #9d174d; }
    .alert-danger  { background: #fdedec; border-color: #e74c3c; color: #922b21; }

    /* ===================== CART DRAWER ===================== */
    .cart-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.45);
      z-index: 2000; opacity: 0; pointer-events: none; transition: opacity 0.28s;
    }
    .cart-overlay.open { opacity: 1; pointer-events: all; }

    .cart-drawer {
      position: fixed; top: 0; right: -420px;
      width: 390px; max-width: 96vw; height: 100%;
      background: #fff; z-index: 2001;
      box-shadow: -6px 0 28px rgba(157,23,77,0.18);
      transition: right 0.3s cubic-bezier(.4,0,.2,1);
      display: flex; flex-direction: column;
    }
    .cart-drawer.open { right: 0; }

    .cart-head {
      background: linear-gradient(135deg, #9d174d, #7f1d3e);
      color: #fff; padding: 18px 20px;
      display: flex; align-items: center; justify-content: space-between;
      flex-shrink: 0;
    }
    .cart-head h3 { margin: 0; font-size: 18px; font-weight: 800; }
    .cart-close { background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; line-height: 1; padding: 0; }

    .cart-body { flex: 1; overflow-y: auto; padding: 0; }
    .cart-empty { text-align: center; padding: 60px 20px; color: #fda4af; }
    .cart-empty i { font-size: 58px; display: block; margin-bottom: 14px; }
    .cart-empty p { font-size: 15px; font-weight: 600; margin: 0 0 6px; color: #7f1d3e; }
    .cart-empty small { font-size: 13px; color: #aaa; }

    .cart-item {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 14px 16px; border-bottom: 1px solid #fff1f2;
      transition: background 0.15s;
    }
    .cart-item:hover { background: #fff9fb; }
    .cart-item-img {
      width: 58px; height: 58px; object-fit: contain;
      border-radius: 8px; background: #fdf4f8; flex-shrink: 0;
      border: 1px solid #fecdd3;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-info .name { font-size: 13px; font-weight: 700; color: #2c3e50; margin: 0 0 4px; line-height: 1.3; }
    .cart-item-info .unit { font-size: 12px; color: #aaa; margin: 0 0 6px; }
    .cart-item-info .subtotal { font-size: 14px; font-weight: 800; color: #9d174d; }
    .qty-row { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
    .qty-btn {
      width: 26px; height: 26px; border: 1px solid #fecdd3; border-radius: 5px;
      background: #fff1f2; cursor: pointer; font-size: 15px; font-weight: 700;
      display: flex; align-items: center; justify-content: center; transition: all 0.15s;
      color: #7f1d3e;
    }
    .qty-btn:hover { background: #9d174d; color: #fff; border-color: #9d174d; }
    .qty-num { font-size: 14px; font-weight: 800; min-width: 22px; text-align: center; }
    .cart-remove { color: #fecdd3; cursor: pointer; font-size: 17px; padding: 4px; flex-shrink: 0; transition: color 0.15s; }
    .cart-remove:hover { color: #9d174d; }

    .cart-foot {
      padding: 16px 18px; border-top: 2px solid #fff1f2;
      background: #fff9fb; flex-shrink: 0;
    }
    .cart-total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .cart-total-row .lbl { font-size: 14px; font-weight: 600; color: #888; }
    .cart-total-row .amt { font-size: 24px; font-weight: 900; color: #9d174d; }
    .btn-checkout-main {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      width: 100%; background: linear-gradient(135deg, #9d174d, #7f1d3e);
      color: #fff; border: none; border-radius: 9px; padding: 13px;
      font-size: 15px; font-weight: 800; cursor: pointer; text-decoration: none;
      transition: opacity 0.2s; letter-spacing: 0.2px;
    }
    .btn-checkout-main:hover { opacity: 0.86; color: #fff; text-decoration: none; }
    .cart-clear { display: block; text-align: center; font-size: 12px; color: #fda4af; margin-top: 10px; cursor: pointer; }
    .cart-clear:hover { color: #9d174d; }

    /* ===================== FOOTER ===================== */
    .shop-footer {
      background: #1a0a12; color: #888;
      text-align: center; padding: 28px 20px;
      font-size: 13px; border-top: 3px solid #9d174d;
    }
    .shop-footer a { color: #9d174d; text-decoration: none; }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 640px) {
      .nav-search { display: none; }
      .brand-logo .brand-sub { display: none; }
      .cart-drawer { width: 100%; max-width: 100%; right: -100%; }
      .cart-drawer.open { right: 0; }
    }

    /* ===================== TOAST ===================== */
    .cart-toast {
      position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px);
      background: linear-gradient(135deg, #9d174d, #7f1d3e);
      color: #fff; padding: 11px 22px; border-radius: 30px;
      font-size: 13px; font-weight: 700; z-index: 9999;
      box-shadow: 0 6px 20px rgba(157,23,77,0.35);
      transition: transform 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s;
      opacity: 0; pointer-events: none; white-space: nowrap;
      display: flex; align-items: center; gap: 8px;
    }
    .cart-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

    /* Logo fallback */
    .brand-avatar {
      width: 52px; height: 52px; border-radius: 50%;
      background: rgba(255,255,255,0.25); border: 2px solid rgba(255,255,255,0.5);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; font-weight: 900; color: #fff; flex-shrink: 0;
    }
  </style>
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="shop-nav">
  <div class="nav-inner">

    {{-- Logo --}}
    <a href="{{ url('tienda') }}" class="brand-logo">
      <img src="{{ asset('img/logo-andyland.png') }}" alt="AndylandPy"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <div class="brand-avatar" style="display:none;">A</div>
      <div class="brand-text">
        <span class="brand-name">Andyland<span style="color:#fff1f2;">Py.</span></span>
        <span class="brand-sub">Tienda online</span>
      </div>
    </a>

    {{-- Buscador --}}
    <div class="nav-search">
      <form method="GET" action="{{ url('tienda') }}">
        <input type="text" name="searchText" value="{{ request('searchText') }}" placeholder="Buscar productos...">
        <i class="fa fa-search ico"></i>
      </form>
    </div>

    {{-- Links y carrito --}}
    <div class="nav-right">
      @auth
        @if(auth()->user()->idrol == 1)
          <a href="{{ url('home') }}" class="nav-link-pill"><i class="fa fa-tachometer"></i> Admin</a>
        @endif
        <a href="{{ route('mis.compras') }}" class="nav-link-pill"><i class="fa fa-shopping-bag"></i> Mis Compras</a>
        <a href="{{ route('perfil') }}" class="nav-link-pill"><i class="fa fa-user-circle"></i> Mi Perfil</a>
        <a href="{{ route('logout') }}" class="nav-link-pill"><i class="fa fa-sign-out"></i> Salir</a>
      @else
        <a href="{{ route('login') }}" class="nav-link-pill"><i class="fa fa-sign-in"></i> Iniciar Sesión</a>
      @endauth

      <div class="cart-toggle" onclick="toggleCart()">
        <i class="fa fa-shopping-bag"></i>
        <span id="cart-label">Carrito</span>
        <span class="cart-count" id="cart-count">0</span>
      </div>
    </div>

  </div>
</nav>

{{-- ===== CATEGORY BAR ===== --}}
@if(!View::hasSection('hide_cat_bar'))
<div class="cat-bar">
  <div class="inner">
    <a href="{{ url('tienda') }}" class="cat-pill {{ !request('idcat') || request('idcat') == 'todos' ? 'active' : '' }}">
      <i class="fa fa-th-large"></i> Todos
    </a>
    @foreach(DB::table('categoria')->where('condicion','1')->get() as $c)
      <a href="{{ url('tienda?idcat='.$c->idcategoria) }}"
         class="cat-pill {{ request('idcat') == $c->idcategoria ? 'active' : '' }}">
        {{ $c->nombre }}
      </a>
    @endforeach
  </div>
</div>
@endif

{{-- ===== CONTENIDO ===== --}}
<main class="shop-main">
  @if(session('status'))
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> {{ session('status') }}
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
  @endif
  @yield('content')
</main>

<footer class="shop-footer">
  <div style="margin-bottom:10px;">
    <span style="display:inline-flex; align-items:center; gap:10px; justify-content:center;">
      <img src="{{ asset('img/logo-andyland.png') }}" alt="AndylandPy"
           style="width:36px; height:36px; border-radius:50%; border:2px solid #9d174d; object-fit:cover; vertical-align:middle;"
           onerror="this.style.display='none'">
      <strong style="color:#9d174d; font-size:16px;">AndylandPy.</strong>
    </span>
  </div>
  <p style="margin:0 0 6px; color:#666;">Regalos y souvenirs personalizados · San Lorenzo, Paraguay</p>
  <p style="margin:0; font-size:12px; color:#555;">&copy; {{ date('Y') }} Andyland PY — Todos los derechos reservados.</p>
</footer>

{{-- ===== TOAST ===== --}}
<div class="cart-toast" id="cart-toast">
  <i class="fa fa-check-circle"></i>
  <span id="cart-toast-msg">¡Agregado al carrito!</span>
</div>

{{-- ===== CART OVERLAY & DRAWER ===== --}}
<div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>

<div class="cart-drawer" id="cart-drawer">
  <div class="cart-head">
    <h3><i class="fa fa-shopping-bag"></i> Mi Carrito</h3>
    <button class="cart-close" onclick="toggleCart()">&times;</button>
  </div>
  <div class="cart-body" id="cart-body"></div>
  <div class="cart-foot" id="cart-foot" style="display:none;">
    <div class="cart-total-row">
      <span class="lbl">Total del pedido:</span>
      <span class="amt" id="cart-total">0 Gs.</span>
    </div>
    <a href="{{ url('tienda/checkout') }}" class="btn-checkout-main">
      <i class="fa fa-lock"></i>
      @auth Finalizar Compra @else Iniciar Sesión para Comprar @endauth
    </a>
    <span class="cart-clear" onclick="cartClear()">Vaciar carrito</span>
  </div>
</div>

{{-- ===== JS ===== --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
const CART_KEY = 'andyland_cart_v2';

const Cart = {
  all()    { return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); },
  _save(c) { localStorage.setItem(CART_KEY, JSON.stringify(c)); cartRender(); },

  add(id, nombre, precio, imagen, stock) {
    const items = this.all();
    const i = items.findIndex(x => x.id == id);
    if (i >= 0) {
      if (items[i].qty < stock) { items[i].qty++; showToast('Cantidad actualizada · ' + nombre); }
      else showToast('Stock máximo alcanzado', true);
    } else {
      items.push({ id: +id, nombre, precio: +precio, imagen, stock: +stock, qty: 1 });
      showToast('¡Agregado! · ' + nombre.slice(0, 28) + (nombre.length > 28 ? '…' : ''));
    }
    this._save(items);
  },

  remove(id)  { this._save(this.all().filter(x => x.id != id)); },

  changeQty(id, delta) {
    const items = this.all();
    const i = items.findIndex(x => x.id == id);
    if (i < 0) return;
    items[i].qty = Math.min(items[i].stock, Math.max(1, items[i].qty + delta));
    this._save(items);
  },

  total() { return this.all().reduce((s, x) => s + x.precio * x.qty, 0); },
  count() { return this.all().reduce((s, x) => s + x.qty, 0); },
  clear() { localStorage.removeItem(CART_KEY); cartRender(); },
};

function cartClear() { Cart.clear(); }

function fmt(n) {
  return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/* Encode filename con espacios para usar en src */
const NO_IMG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='58' height='58'%3E%3Crect width='58' height='58' fill='%23fdf6fa'/%3E%3Ctext x='50%25' y='50%25' font-size='22' text-anchor='middle' dominant-baseline='central' fill='%23f48fb1'%3E%3F%3C/text%3E%3C/svg%3E";

function imgSrc(filename) {
  if (!filename) return NO_IMG;
  const parts = filename.split('/');
  return '/imagenes/articulos/' + parts.map(encodeURIComponent).join('/');
}

function cartRender() {
  const items = Cart.all();
  const count = Cart.count();
  const badge = document.getElementById('cart-count');
  const body  = document.getElementById('cart-body');
  const foot  = document.getElementById('cart-foot');
  const total = document.getElementById('cart-total');
  const label = document.getElementById('cart-label');

  badge.textContent = count;
  badge.style.display = count > 0 ? 'flex' : 'none';
  label.textContent   = count > 0 ? 'Carrito (' + count + ')' : 'Carrito';

  if (!items.length) {
    foot.style.display = 'none';
    body.innerHTML = `
      <div class="cart-empty">
        <i class="fa fa-shopping-bag"></i>
        <p>Tu carrito está vacío</p>
        <small>¡Buscá productos y añadílos!</small>
      </div>`;
    return;
  }

  foot.style.display = 'block';
  total.textContent  = fmt(Cart.total()) + ' Gs.';

  body.innerHTML = items.map(item => `
    <div class="cart-item">
      <img class="cart-item-img" src="${imgSrc(item.imagen)}"
        onerror="this.onerror=null; this.src=NO_IMG;" alt="${item.nombre}">
      <div class="cart-item-info">
        <p class="name">${item.nombre}</p>
        <p class="unit">${fmt(item.precio)} Gs. c/u</p>
        <div class="qty-row">
          <button class="qty-btn" onclick="Cart.changeQty(${item.id},-1)">−</button>
          <span class="qty-num">${item.qty}</span>
          <button class="qty-btn" onclick="Cart.changeQty(${item.id},+1)">+</button>
        </div>
        <p class="subtotal" style="margin-top:6px;">${fmt(item.precio * item.qty)} Gs.</p>
      </div>
      <i class="fa fa-times cart-remove" onclick="Cart.remove(${item.id})"></i>
    </div>
  `).join('');
}

function toggleCart() {
  document.getElementById('cart-overlay').classList.toggle('open');
  document.getElementById('cart-drawer').classList.toggle('open');
}

let toastTimer;
function showToast(msg, warn = false) {
  const t = document.getElementById('cart-toast');
  const m = document.getElementById('cart-toast-msg');
  if (!t) return;
  m.textContent = msg;
  t.style.background = warn
    ? 'linear-gradient(135deg,#e67e22,#ca6f1e)'
    : 'linear-gradient(135deg, #9d174d, #7f1d3e)';
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

@if(session('order_placed'))
  Cart.clear();
@endif

cartRender();
</script>
@stack('scripts')
</body>
</html>
