<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ANDYLAND Py | Admin</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/skins/skin-purple.min.css">

    <style>
      /* ========== LIGHT ROSE THEME ========== */

      /* ── Variables ── */
      :root {
        --rose:       #be185d;   /* rose-700  — acento principal */
        --rose-lt:    #f43f5e;   /* rose-500  — hover / badges */
        --rose-bg:    #fff1f2;   /* rose-50   — fondos suaves */
        --rose-border:#fecdd3;   /* rose-200  — bordes */
        --slate-dark: #1e293b;   /* slate-800 — header */
        --slate-mid:  #334155;   /* slate-700 — textos oscuros */
        --slate-soft: #64748b;   /* slate-500 — subtítulos */
        --bg-page:    #f1f5f9;   /* slate-100 — fondo general */
        --bg-white:   #ffffff;
        --border:     #e2e8f0;   /* slate-200 */
      }

      body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg-page); }
      .content-wrapper { background: var(--bg-page); }

      /* ── Header ── */
      .skin-purple .main-header .navbar,
      .skin-purple .main-header .logo {
        background: var(--slate-dark) !important;
        border-color: var(--slate-mid) !important;
      }
      .skin-purple .main-header .logo {
        display: flex; align-items: center; gap: 10px;
        font-weight: 900 !important;
        background: var(--slate-dark) !important;
      }
      .skin-purple .main-header .logo:hover { background: var(--slate-mid) !important; }
      .skin-purple .main-header .navbar .sidebar-toggle:hover { background: rgba(255,255,255,0.08); }
      .skin-purple .main-header li.user-header { background: var(--slate-dark); }
      .skin-purple .navbar-nav > .user-menu > .dropdown-menu { border-top-color: var(--rose); }

      /* ── Sidebar — blanco ── */
      .skin-purple .main-sidebar,
      .skin-purple .left-side {
        background: var(--bg-white);
        border-right: 1px solid var(--border);
        box-shadow: 2px 0 8px rgba(0,0,0,0.04);
      }
      .skin-purple .sidebar-menu > li.header {
        background: var(--bg-page);
        color: var(--slate-soft);
        font-weight: 700; font-size: 10px; letter-spacing: 1px;
        border-bottom: 1px solid var(--border);
      }
      .skin-purple .sidebar a { color: var(--slate-mid); }
      .skin-purple .sidebar-menu > li > a { border-left: 3px solid transparent; }
      .skin-purple .sidebar-menu > li:hover > a {
        background: var(--rose-bg);
        color: var(--rose);
        border-left-color: var(--rose-lt);
      }
      .skin-purple .sidebar-menu > li.active > a {
        background: var(--rose-bg);
        color: var(--rose);
        border-left-color: var(--rose);
        font-weight: 700;
      }
      .skin-purple .sidebar-menu > li > .treeview-menu {
        background: #fafafa;
        border-left: 3px solid var(--rose-border);
        margin-left: 0;
      }
      .skin-purple .treeview-menu > li > a { color: var(--slate-soft); font-size: 13px; }
      .skin-purple .treeview-menu > li > a:hover { color: var(--rose); background: var(--rose-bg); }
      .skin-purple .treeview-menu > li.active > a { color: var(--rose); font-weight: 700; }
      .skin-purple .sidebar-form input { background: var(--bg-page); border-color: var(--border); color: var(--slate-mid); }

      /* User panel del sidebar */
      .skin-purple .user-panel { background: var(--bg-white); border-bottom: 1px solid var(--border) !important; }

      /* ── Boxes ── */
      .box {
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06) !important;
        border: 1px solid var(--border) !important;
        border-top: 2px solid var(--rose) !important;
        background: var(--bg-white);
      }
      .box-header { border-bottom: 1px solid var(--border); background: var(--bg-white); }
      .box-title { font-weight: 700; color: var(--slate-dark); font-size: 14px; }

      /* ── Tables ── */
      .table thead > tr > th {
        background: var(--slate-dark);
        color: #fff; border: none; font-weight: 600; font-size: 12px;
      }
      .table-striped > tbody > tr:nth-of-type(odd) { background-color: #fafafa; }
      .table > tbody > tr:hover { background: var(--rose-bg); }
      .table-bordered { border-color: var(--border); }
      .table > tbody > tr > td { border-color: var(--border); color: var(--slate-mid); }

      /* ── Buttons ── */
      .btn-primary { background: var(--rose); border-color: var(--rose); }
      .btn-primary:hover, .btn-primary:focus { background: #9d174d; border-color: #9d174d; }
      .btn-success { background: #16a34a; border-color: #16a34a; }
      .btn-success:hover { background: #15803d; border-color: #15803d; }
      .btn-default { border-color: var(--border); color: var(--slate-mid); }
      .btn-default:hover { background: var(--rose-bg); border-color: var(--rose-border); color: var(--rose); }

      /* ── Pagination ── */
      .pagination > .active > a,
      .pagination > .active > span { background: var(--rose); border-color: var(--rose); }
      .pagination > li > a { color: var(--rose); border-color: var(--rose-border); }
      .pagination > li > a:hover { background: var(--rose-bg); color: var(--rose); }

      /* ── Footer ── */
      .main-footer {
        background: var(--bg-white);
        color: var(--slate-soft);
        border-top: 1px solid var(--border);
      }
      .main-footer a { color: var(--rose); }

      /* ── Content header ── */
      .content-header h1 { font-size: 20px; font-weight: 700; color: var(--slate-dark); }
      .content-header h1 small { color: var(--slate-soft); font-weight: 400; font-size: 14px; }
      .content-header .breadcrumb { background: transparent; }

      /* ── Alerts ── */
      .alert-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
      .alert-danger  { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
      .alert-warning { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
      .alert-info    { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }

      /* ── Form controls ── */
      .form-control {
        border-color: var(--border);
        border-radius: 6px;
        color: var(--slate-dark);
        box-shadow: none;
      }
      .form-control:focus {
        border-color: var(--rose);
        box-shadow: 0 0 0 3px rgba(190,24,93,0.1);
      }

      /* ── Label ── */
      label { color: var(--slate-mid); font-weight: 600; font-size: 13px; }

      /* ── Input group ── */
      .input-group-btn .btn-primary { background: var(--rose); border-color: var(--rose); }

      /* ── Logo header ── */
      .main-header .logo { overflow: hidden; }
      .main-header .logo img { max-height: 38px; max-width: 38px; }
      .skin-purple .main-header .logo-lg { display: flex !important; align-items: center; gap: 10px; }
    </style>
  </head>
  <body class="hold-transition skin-purple sidebar-mini">
    <div class="wrapper">

      <!-- HEADER -->
      <header class="main-header">
        <a href="{{ url('home') }}" class="logo">
          <span class="logo-mini">
            <img src="{{ asset('img/logo-andyland.png') }}" alt="Logo"
                 style="height:34px; width:34px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.4);"
                 onerror="this.style.display='none'">
          </span>
          <span class="logo-lg" style="color:#fff; font-size:17px; display:flex; align-items:center; gap:10px;">
            <img src="{{ asset('img/logo-andyland.png') }}" alt="Logo"
                 style="height:38px; width:38px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.4); flex-shrink:0;"
                 onerror="this.style.display='none'">
            <span><b>Andyland</b><span style="color:rgba(255,255,255,0.7);">Py.</span></span>
          </span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Navegación</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <small class="bg-green" style="border-radius:10px; padding:2px 8px;">Online</small>
                  <span class="hidden-xs" style="color:#fff; font-weight:700; margin-left:6px;">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="{{ url('tienda/perfil') }}" class="btn btn-default btn-flat">
                        <i class="fa fa-user"></i> Mi Perfil
                      </a>
                    </div>
                    <div class="pull-right">
                      <a href="{{ route('logout') }}" class="btn btn-default btn-flat">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
                      </a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      <!-- SIDEBAR -->
      <aside class="main-sidebar">
        <section class="sidebar">
          <div class="user-panel" style="padding:14px 12px;">
            <div style="display:flex; align-items:center; gap:10px;">
              <div style="width:36px; height:36px; border-radius:50%; background:#be185d; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:900; font-size:15px; flex-shrink:0;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
              </div>
              <div style="min-width:0;">
                <p style="margin:0; color:#1e293b; font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</p>
                <small style="color:#be185d; font-size:11px; font-weight:600;">Administrador</small>
              </div>
            </div>
          </div>

          <ul class="sidebar-menu" data-widget="tree">
            <li class="header">NAVEGACIÓN</li>

            @if(Auth::user()->idrol == 1)

            <li class="{{ Request::is('home') ? 'active' : '' }}">
              <a href="{{ url('home') }}"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a>
            </li>

            <li class="treeview {{ Request::is('almacen/*') ? 'active' : '' }}">
              <a href="#"><i class="fa fa-archive"></i> <span>Almacén</span><i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="{{ Request::is('almacen/articulo*') ? 'active' : '' }}">
                  <a href="{{ url('almacen/articulo') }}"><i class="fa fa-circle-o"></i> Artículos</a>
                </li>
                <li class="{{ Request::is('almacen/categoria*') ? 'active' : '' }}">
                  <a href="{{ url('almacen/categoria') }}"><i class="fa fa-circle-o"></i> Categorías</a>
                </li>
              </ul>
            </li>

            <li class="treeview {{ Request::is('compras/*') ? 'active' : '' }}">
              <a href="#"><i class="fa fa-shopping-cart"></i> <span>Compras</span><i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="{{ url('compras/ingreso') }}"><i class="fa fa-circle-o"></i> Ingresos</a></li>
                <li><a href="{{ url('compras/proveedor') }}"><i class="fa fa-circle-o"></i> Proveedores</a></li>
              </ul>
            </li>

            <li class="treeview {{ Request::is('ventas/*') ? 'active' : '' }}">
              <a href="#"><i class="fa fa-shopping-bag"></i> <span>Ventas</span><i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="{{ Request::is('ventas/venta*') ? 'active' : '' }}">
                  <a href="{{ url('ventas/venta') }}"><i class="fa fa-circle-o"></i> Pedidos</a>
                </li>
                <li class="{{ Request::is('ventas/cliente*') ? 'active' : '' }}">
                  <a href="{{ url('ventas/cliente') }}"><i class="fa fa-circle-o"></i> Clientes</a>
                </li>
              </ul>
            </li>

            <li class="treeview {{ Request::is('seguridad/*') ? 'active' : '' }}">
              <a href="#"><i class="fa fa-users"></i> <span>Acceso</span><i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="{{ url('seguridad/usuario') }}"><i class="fa fa-circle-o"></i> Usuarios</a></li>
              </ul>
            </li>

            <li class="treeview {{ Request::is('registros/*') || Request::is('ventas/reporte*') ? 'active' : '' }}">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>Reportes</span><i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="{{ url('registros/registro') }}"><i class="fa fa-circle-o"></i> Registros</a></li>
                <li class="{{ Request::is('ventas/reporte*') ? 'active' : '' }}">
                  <a href="{{ url('ventas/reporte') }}"><i class="fa fa-circle-o"></i> Reporte de Ventas</a>
                </li>
              </ul>
            </li>

            <li class="{{ Request::is('admin/fechas-entrega*') ? 'active' : '' }}">
              <a href="{{ url('admin/fechas-entrega') }}">
                <i class="fa fa-calendar-check-o"></i> <span>Fechas de Entrega</span>
              </a>
            </li>

            <li class="{{ Request::is('admin/caja*') ? 'active' : '' }}">
              <a href="{{ url('admin/caja') }}"><i class="fa fa-money"></i> <span>Caja</span></a>
            </li>

            @endif

            <li>
              <a href="{{ url('tienda') }}" target="_blank">
                <i class="fa fa-store"></i> <span>Ver Tienda</span>
              </a>
            </li>
          </ul>
        </section>
      </aside>

      <!-- CONTENT -->
      <div class="content-wrapper">
        <section class="content-header">
          <h1>@yield('page_title', 'Panel de Control') <small>@yield('page_subtitle', 'AndylandPy')</small></h1>
        </section>
        <section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">@yield('box_title', 'AndylandPy — Sistema de Ventas')</h3>
                </div>
                <div class="box-body">
                  @if(session('msj'))
                    <div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <i class="fa fa-check-circle"></i> {{ session('msj') }}
                    </div>
                  @endif
                  @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                  @endif
                  @yield('contenido')
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Versión</b> 1.0</div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">AndylandPy</a></strong>
      </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/js/adminlte.min.js"></script>
    @stack('scripts')
  </body>
</html>
