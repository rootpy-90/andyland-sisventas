@extends('layouts.admin')
@section('page_title', 'Artículos')
@section('page_subtitle', 'Gestión de productos')
@section('box_title', 'Catálogo de Artículos')
@section('contenido')

<style>
  .art-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:18px; }
  .btn-nuevo { background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; border:none; border-radius:8px; padding:8px 18px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
  .btn-nuevo:hover { opacity:.86; color:#fff; }

  .art-table { width:100%; border-collapse:collapse; background:#fff; }
  .art-table thead th { background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; padding:11px 12px; font-size:12px; font-weight:700; text-align:left; }
  .art-table tbody td { padding:10px 12px; border-bottom:1px solid #fce4ec; font-size:13px; color:#444; vertical-align:middle; }
  .art-table tbody tr:hover { background:#fff9fb; }

  .art-img { width:52px; height:52px; object-fit:contain; border-radius:8px; background:#f9f9f9; border:1px solid #fce4ec; }
  .precio-cell { font-size:15px; font-weight:900; color:#e91e8c; }
  .precio-none { font-size:12px; color:#ccc; font-style:italic; }

  .stock-badge { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; }
  .stock-ok   { background:#d5f5e3; color:#1e8449; }
  .stock-low  { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; }
  .stock-zero { background:#fadbd8; color:#922b21; }

  .estado-badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .estado-activo   { background:#d5f5e3; color:#1e8449; }
  .estado-inactivo { background:#f5f5f5; color:#aaa; }

  .btn-b { border:none; border-radius:5px; padding:5px 11px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:opacity 0.18s; }
  .btn-b:hover { opacity:.82; text-decoration:none; }
  .bb-edit { background:#2980b9; color:#fff; }
  .bb-del  { background:#e74c3c; color:#fff; }
</style>

<div class="art-header">
  <div>@include('almacen.articulo.search')</div>
  <a href="{{ url('almacen/articulo/create') }}" class="btn-nuevo">
    <i class="fa fa-plus"></i> Nuevo Artículo
  </a>
</div>

<div class="table-responsive" style="border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(233,30,140,0.08);">
  <table class="art-table">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($articulos as $art)
      <tr>
        <td>
          <img class="art-img"
            src="{{ asset('imagenes/articulos/'.rawurlencode($art->imagen ?? '')) }}"
            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'52\' height=\'52\'%3E%3Crect width=\'52\' height=\'52\' fill=\'%23fff9fb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'20\' text-anchor=\'middle\' dominant-baseline=\'central\' fill=\'%23f48fb1\'%3E%3F%3C/text%3E%3C/svg%3E'"
            alt="{{ $art->nombre }}">
        </td>
        <td>
          <b style="color:#2c3e50;">{{ $art->nombre }}</b>
          @if($art->descripcion)
            <br><small style="color:#aaa;">{{ str_limit($art->descripcion, 40) }}</small>
          @endif
        </td>
        <td>
          <span style="background:#fce4ec; color:#ad1457; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:700;">
            {{ $art->categoria }}
          </span>
        </td>
        <td>
          @if($art->precio)
            <span class="precio-cell">{{ number_format($art->precio, 0, ',', '.') }} Gs.</span>
          @else
            <span class="precio-none">Sin precio</span>
          @endif
        </td>
        <td>
          @if($art->stock == 0)
            <span class="stock-badge stock-zero"><i class="fa fa-times"></i> Sin stock</span>
          @elseif($art->stock < 5)
            <span class="stock-badge stock-low"><i class="fa fa-warning"></i> {{ $art->stock }}</span>
          @else
            <span class="stock-badge stock-ok"><i class="fa fa-check"></i> {{ $art->stock }}</span>
          @endif
        </td>
        <td>
          <span class="estado-badge {{ $art->estado === 'Activo' ? 'estado-activo' : 'estado-inactivo' }}">
            {{ $art->estado }}
          </span>
        </td>
        <td style="white-space:nowrap;">
          <a href="{{ URL::action('ArticuloController@edit', $art->idarticulo) }}" class="btn-b bb-edit">
            <i class="fa fa-pencil"></i> Editar
          </a>
          <a href="" data-target="#modal-delete-{{ $art->idarticulo }}" data-toggle="modal" class="btn-b bb-del">
            <i class="fa fa-trash"></i>
          </a>
        </td>
      </tr>
      @include('almacen.articulo.modal')
      @endforeach
    </tbody>
  </table>
</div>

<div style="margin-top:16px;">{{ $articulos->render() }}</div>

@endsection
