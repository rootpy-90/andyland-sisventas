@extends('layouts.admin')
@section('page_title', 'Clientes')
@section('page_subtitle', 'Base de clientes registrados')
@section('box_title', 'Listado de Clientes')
@section('contenido')

<style>
  .cli-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:18px; }
  .cli-table { width:100%; border-collapse:collapse; background:#fff; }
  .cli-table thead th { background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; padding:11px 12px; font-size:12px; font-weight:700; text-align:left; }
  .cli-table tbody td { padding:10px 12px; border-bottom:1px solid #fce4ec; font-size:13px; color:#444; vertical-align:middle; }
  .cli-table tbody tr:hover { background:#fff9fb; }

  .avatar-sm {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#e91e8c,#ad1457);
    display:inline-flex; align-items:center; justify-content:center;
    color:#fff; font-weight:900; font-size:14px;
  }
  .cat-badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
  .cat-vip      { background:#fef9e7; color:#b7770d; border:1px solid #f9e79f; }
  .cat-frecuente{ background:#d5f5e3; color:#1e8449; }
  .cat-regular  { background:#fce4ec; color:#e91e8c; }
  .cat-nuevo    { background:#f5f5f5; color:#aaa; }
  .btn-c { border:none; border-radius:5px; padding:5px 10px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:opacity 0.18s; }
  .btn-c:hover { opacity:.82; text-decoration:none; }
  .bc-edit { background:#2980b9; color:#fff; }
  .bc-del  { background:#e74c3c; color:#fff; }
</style>

<div class="cli-header">
  <div>@include('ventas.cliente.search')</div>
  <a href="{{ url('ventas/cliente/create') }}" style="background:linear-gradient(135deg,#e91e8c,#ad1457); color:#fff; border-radius:8px; padding:8px 18px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
    <i class="fa fa-plus"></i> Nuevo Cliente
  </a>
</div>

<div class="table-responsive" style="border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(233,30,140,0.08);">
  <table class="cli-table">
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Contacto</th>
        <th>Dirección</th>
        <th>Categoría</th>
        <th>Pedidos</th>
        <th>Total gastado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($personas as $per)
      @php
        $totalPedidos = DB::table('venta')->where('idcliente',$per->idpersona)->where('estado','!=','C')->count();
        $totalGastado = DB::table('venta')->where('idcliente',$per->idpersona)->where('estado','A')->sum('total_venta');
        if ($totalPedidos === 0)          { $cat = ['label'=>'Nuevo',    'class'=>'cat-nuevo']; }
        elseif ($totalGastado >= 500000 || $totalPedidos >= 10) { $cat = ['label'=>'VIP', 'class'=>'cat-vip']; }
        elseif ($totalPedidos >= 4)       { $cat = ['label'=>'Frecuente','class'=>'cat-frecuente']; }
        else                              { $cat = ['label'=>'Regular',  'class'=>'cat-regular']; }
      @endphp
      <tr>
        <td>
          <div style="display:flex; align-items:center; gap:10px;">
            <span class="avatar-sm">{{ strtoupper(substr($per->nombre,0,1)) }}</span>
            <div>
              <b style="color:#2c3e50;">{{ $per->nombre }} {{ $per->apellido }}</b>
              <br><small style="color:#aaa;">{{ $per->email }}</small>
            </div>
          </div>
        </td>
        <td>
          @if($per->telefono)
            <a href="https://wa.me/{{ preg_replace('/\D/','',$per->telefono) }}" target="_blank"
              style="color:#25d366; font-weight:700; font-size:13px; text-decoration:none;">
              <i class="fa fa-whatsapp"></i> {{ $per->telefono }}
            </a>
          @else
            <span style="color:#ccc;">—</span>
          @endif
        </td>
        <td>
          <span style="font-size:12px; color:#666;">
            {{ $per->ciudad ?? '—' }}
            @if($per->barrio), {{ $per->barrio }} @endif
          </span>
        </td>
        <td>
          <span class="cat-badge {{ $cat['class'] }}">{{ $cat['label'] }}</span>
        </td>
        <td style="text-align:center; font-weight:700; color:#2c3e50;">{{ $totalPedidos }}</td>
        <td style="font-weight:800; color:#e91e8c; font-size:13px;">
          {{ $totalGastado > 0 ? number_format($totalGastado,0,',','.').' Gs.' : '—' }}
        </td>
        <td style="white-space:nowrap;">
          <a href="{{ URL::action('ClienteController@edit',$per->idpersona) }}" class="btn-c bc-edit">
            <i class="fa fa-pencil"></i> Editar
          </a>
          <a href="" data-target="#modal-delete-{{ $per->idpersona }}" data-toggle="modal" class="btn-c bc-del">
            <i class="fa fa-trash"></i>
          </a>
        </td>
      </tr>
      @include('ventas.cliente.modal')
      @endforeach
    </tbody>
  </table>
</div>

<div style="margin-top:16px;">{{ $personas->render() }}</div>

@endsection
