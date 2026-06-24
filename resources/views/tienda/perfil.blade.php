@extends('layouts.tienda')
@section('title', 'Mi Perfil')
@section('content')

<style>
  /* ===== HEADER ===== */
  .perfil-hero {
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    border-radius: 16px; padding: 28px 32px;
    display: flex; align-items: center; gap: 22px;
    margin-bottom: 28px; color: #fff;
    box-shadow: 0 6px 24px rgba(157,23,77,0.25);
  }
  .perfil-avatar {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; font-weight: 900; color: #fff;
    border: 3px solid rgba(255,255,255,0.45); flex-shrink: 0;
  }
  .perfil-hero .hinfo h2 { margin: 0 0 4px; font-size: 22px; font-weight: 900; }
  .perfil-hero .hinfo p  { margin: 0; font-size: 13px; color: rgba(255,255,255,0.78); }
  .perfil-hero .hinfo .badges { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
  .badge-pill {
    background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);
    border-radius: 20px; padding: 3px 12px; font-size: 12px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 5px;
  }

  /* ===== TABS ===== */
  .perfil-tabs { display: flex; gap: 4px; margin-bottom: 22px; flex-wrap: wrap; }
  .tab-btn {
    padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 700;
    border: 2px solid #fecdd3; color: #7f1d3e; background: transparent;
    cursor: pointer; transition: all 0.18s; text-decoration: none;
    display: inline-flex; align-items: center; gap: 7px;
  }
  .tab-btn:hover { background: #fff1f2; color: #9d174d; }
  .tab-btn.active { background: #9d174d; border-color: #9d174d; color: #fff; }

  /* ===== CARD ===== */
  .perfil-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 3px 16px rgba(157,23,77,0.08);
    border: 1px solid #fff1f2; overflow: hidden; margin-bottom: 20px;
  }
  .card-head {
    padding: 16px 24px; border-bottom: 1px solid #fff1f2;
    display: flex; align-items: center; gap: 10px;
    font-size: 15px; font-weight: 800; color: #9d174d;
  }
  .card-head i { color: #9d174d; }
  .card-body { padding: 24px; }

  /* ===== FORM ===== */
  .fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .fields-grid .full { grid-column: 1 / -1; }
  @media (max-width: 600px) { .fields-grid { grid-template-columns: 1fr; } }

  .field-group { display: flex; flex-direction: column; }
  .field-group label { font-size: 12px; font-weight: 700; color: #888; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
  .field-group label i { color: #9d174d; margin-right: 4px; }
  .field-group label .req { color: #9d174d; }
  .field-input {
    padding: 10px 13px; border-radius: 9px;
    border: 2px solid #fecdd3; font-size: 14px; outline: none;
    transition: border-color 0.18s, box-shadow 0.18s; color: #333; width: 100%;
    background: #fff;
  }
  .field-input:focus { border-color: #9d174d; box-shadow: 0 0 0 3px rgba(157,23,77,0.1); }
  .field-input.error { border-color: #e74c3c; }
  .field-error { font-size: 12px; color: #e74c3c; margin-top: 3px; font-weight: 600; }

  /* ===== BUTTONS ===== */
  .btn-save {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    color: #fff; border: none; border-radius: 9px; padding: 11px 24px;
    font-size: 14px; font-weight: 800; cursor: pointer; transition: opacity 0.2s;
  }
  .btn-save:hover { opacity: 0.86; }

  /* ===== PEDIDOS ===== */
  .pedido-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 0; border-bottom: 1px solid #fff1f2; gap: 12px; flex-wrap: wrap;
  }
  .pedido-row:last-child { border: none; }
  .pedido-num { font-size: 13px; font-weight: 800; color: #2c3e50; }
  .pedido-fecha { font-size: 12px; color: #aaa; }
  .pedido-total { font-size: 15px; font-weight: 900; color: #9d174d; }
  .badge-estado {
    padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    display: inline-block;
  }
  .badge-A { background: #d5f5e3; color: #1e8449; }
  .badge-P { background: #fef9e7; color: #b7770d; border: 1px solid #f9e79f; }
  .badge-C { background: #fadbd8; color: #922b21; }

  /* ===== ALERT ===== */
  .alert-ok {
    background: #fff1f2; border-left: 4px solid #9d174d; border-radius: 8px;
    padding: 12px 16px; margin-bottom: 22px; font-size: 14px; color: #9d174d; font-weight: 600;
  }
  .alert-err {
    background: #fdedec; border-left: 4px solid #e74c3c; border-radius: 8px;
    padding: 12px 16px; margin-bottom: 22px; font-size: 13px; color: #922b21;
  }

  /* Password eye */
  .pwd-wrap { position: relative; }
  .pwd-eye {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    cursor: pointer; color: #fda4af; font-size: 15px;
  }
  .pwd-eye:hover { color: #9d174d; }
</style>

{{-- ===== HERO ===== --}}
<div class="perfil-hero">
  <div class="perfil-avatar">
    {{ strtoupper(substr($persona->nombre ?? auth()->user()->name, 0, 1)) }}
  </div>
  <div class="hinfo" style="flex:1;">
    <h2>{{ $persona->nombre }} {{ $persona->apellido }}</h2>
    <p>{{ auth()->user()->email }}</p>
    <div class="badges" style="margin-top:10px;">
      {{-- Categoría automática --}}
      <span class="badge-pill" style="background:{{ $categoria['color'] }}; border-color:{{ $categoria['color'] }}; color:#fff;">
        <i class="fa {{ $categoria['icon'] }}"></i> {{ $categoria['label'] }}
      </span>
      @if($persona->telefono)
        <span class="badge-pill"><i class="fa fa-phone"></i> {{ $persona->telefono }}</span>
      @endif
      @if($persona->ciudad)
        <span class="badge-pill"><i class="fa fa-map-marker"></i> {{ $persona->ciudad }}</span>
      @endif
    </div>
  </div>
  <a href="{{ route('mis.compras') }}" style="
    display:flex; align-items:center; gap:7px; flex-shrink:0;
    background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.4);
    color:#fff; border-radius:10px; padding:10px 16px; font-size:13px; font-weight:700;
    text-decoration:none; transition:background 0.2s; white-space:nowrap;
  " onmouseover="this.style.background='rgba(255,255,255,0.3)'"
     onmouseout="this.style.background='rgba(255,255,255,0.2)'">
    <i class="fa fa-shopping-bag"></i> Mis Compras
  </a>
</div>

{{-- ===== ALERTAS ===== --}}
@if(session('status'))
  <div class="alert-ok"><i class="fa fa-check-circle"></i> {{ session('status') }}</div>
@endif
@if($errors->any() && !session('tab'))
  <div class="alert-err">
    <i class="fa fa-exclamation-circle"></i>
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
  </div>
@endif

{{-- ===== TABS ===== --}}
<div class="perfil-tabs">
  <a href="#datos" class="tab-btn active" onclick="showTab('datos', this)">
    <i class="fa fa-user"></i> Mis datos
  </a>
  <a href="#direccion" class="tab-btn" onclick="showTab('direccion', this)">
    <i class="fa fa-map-marker"></i> Dirección
  </a>
  <a href="#pedidos" class="tab-btn" onclick="showTab('pedidos', this)">
    <i class="fa fa-shopping-bag"></i> Mis pedidos
  </a>
  <a href="#password" class="tab-btn {{ session('tab') == 'password' ? 'active' : '' }}" onclick="showTab('password', this)">
    <i class="fa fa-lock"></i> Contraseña
  </a>
</div>

{{-- ===== TAB: DATOS PERSONALES ===== --}}
<div id="tab-datos" class="tab-content">
  <div class="perfil-card">
    <div class="card-head"><i class="fa fa-user"></i> Datos personales</div>
    <div class="card-body">
      <form method="POST" action="{{ route('perfil.update') }}">
        {{ csrf_field() }}
        <div class="fields-grid">

          <div class="field-group">
            <label><i class="fa fa-id-card"></i> Nombre <span class="req">*</span></label>
            <input type="text" name="nombre" class="field-input {{ $errors->has('nombre') ? 'error' : '' }}"
              value="{{ old('nombre', $persona->nombre) }}" required>
            @if($errors->has('nombre')) <p class="field-error">{{ $errors->first('nombre') }}</p> @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-id-card"></i> Apellido</label>
            <input type="text" name="apellido" class="field-input"
              value="{{ old('apellido', $persona->apellido) }}">
          </div>

          <div class="field-group">
            <label><i class="fa fa-phone"></i> Teléfono / WhatsApp <span class="req">*</span></label>
            <input type="text" name="telefono" class="field-input {{ $errors->has('telefono') ? 'error' : '' }}"
              value="{{ old('telefono', $persona->telefono) }}" required>
            @if($errors->has('telefono')) <p class="field-error">{{ $errors->first('telefono') }}</p> @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-envelope"></i> Correo electrónico</label>
            <form method="POST" action="{{ route('perfil.email') }}" style="display:flex; gap:8px; align-items:flex-start;">
              {{ csrf_field() }}
              <input type="email" name="email" class="field-input {{ $errors->has('email') ? 'error' : '' }}"
                value="{{ old('email', auth()->user()->email) }}" required style="flex:1;">
              <button type="submit" class="btn-save" style="padding:10px 16px; white-space:nowrap;">
                <i class="fa fa-save"></i> Guardar
              </button>
            </form>
            @if($errors->has('email')) <p class="field-error">{{ $errors->first('email') }}</p> @endif
            <span style="font-size:11px; color:#bbb; margin-top:3px;">Se usará para notificaciones y recuperación de contraseña.</span>
          </div>

          {{-- Campos de dirección ocultos para que no se borren al guardar solo datos --}}
          <input type="hidden" name="direccion" value="{{ $persona->direccion }}">
          <input type="hidden" name="ciudad"    value="{{ $persona->ciudad }}">
          <input type="hidden" name="barrio"    value="{{ $persona->barrio }}">
          <input type="hidden" name="pais"      value="{{ $persona->pais }}">
          <input type="hidden" name="referencia" value="{{ $persona->referencia }}">

        </div>
        <div style="margin-top:20px;">
          <button type="submit" class="btn-save">
            <i class="fa fa-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== TAB: DIRECCIÓN ===== --}}
<div id="tab-direccion" class="tab-content" style="display:none;">
  <div class="perfil-card">
    <div class="card-head"><i class="fa fa-map-marker"></i> Dirección de entrega</div>
    <div class="card-body">
      <form method="POST" action="{{ route('perfil.update') }}">
        {{ csrf_field() }}
        {{-- Campos personales ocultos --}}
        <input type="hidden" name="nombre"   value="{{ $persona->nombre }}">
        <input type="hidden" name="apellido" value="{{ $persona->apellido }}">
        <input type="hidden" name="telefono" value="{{ $persona->telefono }}">

        <div class="fields-grid">

          <div class="field-group full">
            <label><i class="fa fa-home"></i> Dirección <span class="req">*</span></label>
            <input type="text" name="direccion" class="field-input {{ $errors->has('direccion') ? 'error' : '' }}"
              value="{{ old('direccion', $persona->direccion) }}" required placeholder="Calle, número de casa...">
            @if($errors->has('direccion')) <p class="field-error">{{ $errors->first('direccion') }}</p> @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-building"></i> Ciudad <span class="req">*</span></label>
            <input type="text" name="ciudad" class="field-input {{ $errors->has('ciudad') ? 'error' : '' }}"
              value="{{ old('ciudad', $persona->ciudad) }}" required placeholder="Ej: Asunción">
            @if($errors->has('ciudad')) <p class="field-error">{{ $errors->first('ciudad') }}</p> @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-map"></i> Barrio</label>
            <input type="text" name="barrio" class="field-input"
              value="{{ old('barrio', $persona->barrio) }}" placeholder="Ej: Villa Morra">
          </div>

          <div class="field-group">
            <label><i class="fa fa-globe"></i> País</label>
            <input type="text" name="pais" class="field-input"
              value="{{ old('pais', $persona->pais ?? 'Paraguay') }}" placeholder="Paraguay">
          </div>

          <div class="field-group">
            <label><i class="fa fa-info-circle"></i> Referencia</label>
            <input type="text" name="referencia" class="field-input"
              value="{{ old('referencia', $persona->referencia) }}"
              placeholder="Casa color azul, frente a la plaza...">
          </div>

        </div>
        <div style="margin-top:20px;">
          <button type="submit" class="btn-save">
            <i class="fa fa-save"></i> Guardar dirección
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== TAB: PEDIDOS ===== --}}
<div id="tab-pedidos" class="tab-content" style="display:none;">
  <div class="perfil-card">
    <div class="card-head"><i class="fa fa-shopping-bag"></i> Mis pedidos recientes</div>
    <div class="card-body">
      @forelse($pedidos as $p)
        <div class="pedido-row">
          <div>
            <p class="pedido-num"><i class="fa fa-hashtag"></i> Pedido #{{ $p->num_comprobante }}</p>
            <p class="pedido-fecha">{{ $p->fecha_hora }}</p>
          </div>
          <div>
            @php $estado = $p->estado; @endphp
            <span class="badge-estado badge-{{ $estado }}">
              @if($estado=='A') <i class="fa fa-check"></i> Aceptado
              @elseif($estado=='P') <i class="fa fa-clock-o"></i> Pendiente
              @else <i class="fa fa-times"></i> Cancelado
              @endif
            </span>
          </div>
          <div class="pedido-total">
            {{ number_format($p->total_venta, 0, ',', '.') }} Gs.
          </div>
        </div>
      @empty
        <div style="text-align:center; padding:40px; color:#fda4af;">
          <i class="fa fa-shopping-basket" style="font-size:48px; display:block; margin-bottom:12px;"></i>
          <p style="font-weight:700; color:#7f1d3e;">Todavía no realizaste ningún pedido.</p>
          <a href="{{ url('tienda') }}" style="color:#9d174d; font-weight:700;">
            <i class="fa fa-arrow-left"></i> Ir a la tienda
          </a>
        </div>
      @endforelse
    </div>
  </div>
</div>

{{-- ===== TAB: CONTRASEÑA ===== --}}
<div id="tab-password" class="tab-content" style="display:none;">
  <div class="perfil-card">
    <div class="card-head"><i class="fa fa-lock"></i> Cambiar contraseña</div>
    <div class="card-body">

      @if($errors->has('password_actual') && session('tab') == 'password')
        <div class="alert-err"><i class="fa fa-exclamation-circle"></i> {{ $errors->first('password_actual') }}</div>
      @endif

      <form method="POST" action="{{ route('perfil.password') }}" style="max-width: 420px;">
        {{ csrf_field() }}

        <div class="field-group" style="margin-bottom:16px;">
          <label><i class="fa fa-lock"></i> Contraseña actual <span class="req">*</span></label>
          <div class="pwd-wrap">
            <input type="password" name="password_actual" id="pwd0" class="field-input {{ $errors->has('password_actual') ? 'error' : '' }}" required placeholder="Tu contraseña actual">
            <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd0',this)"></i>
          </div>
        </div>

        <div class="field-group" style="margin-bottom:16px;">
          <label><i class="fa fa-lock"></i> Nueva contraseña <span class="req">*</span></label>
          <div class="pwd-wrap">
            <input type="password" name="password" id="pwd1" class="field-input {{ $errors->has('password') ? 'error' : '' }}" required placeholder="Mínimo 6 caracteres">
            <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd1',this)"></i>
          </div>
          @if($errors->has('password') && session('tab') == 'password')
            <p class="field-error">{{ $errors->first('password') }}</p>
          @endif
        </div>

        <div class="field-group" style="margin-bottom:24px;">
          <label><i class="fa fa-lock"></i> Confirmar nueva contraseña <span class="req">*</span></label>
          <div class="pwd-wrap">
            <input type="password" name="password_confirmation" id="pwd2" class="field-input" required placeholder="Repetí la nueva contraseña">
            <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd2',this)"></i>
          </div>
        </div>

        <button type="submit" class="btn-save">
          <i class="fa fa-key"></i> Cambiar contraseña
        </button>
      </form>
    </div>
  </div>
</div>

{{-- ===== ZONA DE PELIGRO: Eliminar cuenta ===== --}}
<div style="margin-top:32px; background:#fff; border-radius:14px;
            border:2px solid #fecdd3; overflow:hidden;">
  <div style="padding:16px 24px; background:#fff1f2; display:flex;
              align-items:center; gap:10px; border-bottom:1px solid #fecdd3;">
    <i class="fa fa-exclamation-triangle" style="color:#e74c3c; font-size:16px;"></i>
    <span style="font-size:14px; font-weight:800; color:#991b1b;">Zona de peligro</span>
  </div>
  <div style="padding:20px 24px; display:flex; align-items:center;
              justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
      <p style="font-size:14px; font-weight:700; color:#2c3e50; margin:0 0 4px;">
        Eliminar mi cuenta
      </p>
      <p style="font-size:13px; color:#888; margin:0;">
        Tu cuenta será desactivada. Podés recuperarla contactándonos dentro de los 30 días.
        Después de ese periodo, tus datos serán eliminados permanentemente.
      </p>
    </div>
    <button type="button" onclick="document.getElementById('modal-eliminar').classList.add('open')"
      style="background:#e74c3c; color:#fff; border:none; border-radius:9px;
             padding:10px 22px; font-size:13px; font-weight:800; cursor:pointer;
             white-space:nowrap; flex-shrink:0;">
      <i class="fa fa-trash"></i> Eliminar cuenta
    </button>
  </div>
</div>

{{-- Modal confirmación eliminar cuenta --}}
<div id="modal-eliminar"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
            z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:14px; width:100%; max-width:420px;
              overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,.2); margin:16px;">

    <div style="background:#e74c3c; color:#fff; padding:16px 20px;
                display:flex; align-items:center; justify-content:space-between;">
      <span style="font-size:15px; font-weight:800;">
        <i class="fa fa-exclamation-triangle"></i> ¿Eliminar tu cuenta?
      </span>
      <button type="button"
        onclick="document.getElementById('modal-eliminar').classList.remove('open')"
        style="background:none; border:none; color:#fff; font-size:20px; cursor:pointer;">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <div style="padding:22px 20px;">
      <p style="font-size:13px; color:#555; margin:0 0 18px; line-height:1.7;">
        Tu cuenta será <strong>desactivada</strong>. Podés recuperarla contactándonos dentro de los 30 días.
        Después de ese periodo, tus datos serán eliminados permanentemente.
        Ingresá tu contraseña para confirmar.
      </p>

      @if($errors->has('password_confirmar') && session('tab') == 'eliminar')
        <div style="background:#fadbd8; border-left:4px solid #e74c3c;
                    border-radius:8px; padding:10px 14px; margin-bottom:14px;
                    font-size:13px; color:#922b21; font-weight:600;">
          <i class="fa fa-exclamation-circle"></i>
          {{ $errors->first('password_confirmar') }}
        </div>
      @endif

      <form method="POST" action="{{ route('perfil.eliminar') }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}

        <div style="margin-bottom:20px;">
          <label style="font-size:12px; font-weight:700; color:#888;
                        text-transform:uppercase; letter-spacing:.4px;
                        display:block; margin-bottom:6px;">
            Contraseña actual
          </label>
          <div style="position:relative;">
            <input type="password" name="password_confirmar" id="pwd-eliminar"
              class="field-input" required placeholder="Ingresá tu contraseña"
              style="border-color:#fca5a5; padding-right:40px;">
            <i class="fa fa-eye pwd-eye"
               onclick="togglePwd('pwd-eliminar', this)"
               style="position:absolute; right:12px; top:50%;
                      transform:translateY(-50%); cursor:pointer; color:#fca5a5;">
            </i>
          </div>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button type="button"
            onclick="document.getElementById('modal-eliminar').classList.remove('open')"
            style="background:#f1f5f9; color:#64748b; border:none; border-radius:8px;
                   padding:9px 20px; font-size:13px; font-weight:700; cursor:pointer;">
            Cancelar
          </button>
          <button type="submit"
            style="background:#e74c3c; color:#fff; border:none; border-radius:8px;
                   padding:9px 22px; font-size:13px; font-weight:800; cursor:pointer;">
            <i class="fa fa-trash"></i> Sí, eliminar cuenta
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

@push('scripts')
<script>
function showTab(tab, btn) {
  event.preventDefault();
  document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
  document.getElementById('tab-' + tab).style.display = 'block';
  btn.classList.add('active');
}

function togglePwd(id, icon) {
  const inp = document.getElementById(id);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  icon.className = 'fa fa-eye' + (show ? '-slash' : '') + ' pwd-eye';
}

// Auto-abrir modal de eliminar si hubo error de contraseña
@if(session('tab') == 'eliminar')
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-eliminar').style.display = 'flex';
  });
@endif

// Auto-abrir tab si viene con errores de contraseña
@if(session('tab') == 'password')
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-password').style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn')[3].classList.add('active');
  });
@endif

// Auto-abrir tab si viene con errores de email
@if(session('tab') == 'email')
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-datos').style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn')[0].classList.add('active');
  });
@endif
</script>
@endpush
@endsection
