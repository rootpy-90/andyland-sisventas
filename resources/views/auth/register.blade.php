@extends('layouts.tienda')
@section('title', 'Crear cuenta')
@section('hide_cat_bar', '1')
@section('content')

<style>
  .auth-wrap {
    display: flex; justify-content: center;
    padding: 20px 0 50px;
  }
  .auth-card {
    background: #fff; border-radius: 18px;
    box-shadow: 0 8px 40px rgba(157,23,77,0.13);
    border: 1px solid #fff1f2;
    width: 100%; max-width: 700px; overflow: hidden;
  }
  .auth-header {
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    padding: 28px; text-align: center; color: #fff;
    display: flex; align-items: center; gap: 18px; justify-content: center;
  }
  .auth-header img {
    width: 60px; height: 60px; border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.5); object-fit: cover; flex-shrink: 0;
  }
  .auth-header .htext h2 { margin: 0 0 3px; font-size: 22px; font-weight: 900; text-align: left; }
  .auth-header .htext p  { margin: 0; font-size: 13px; color: rgba(255,255,255,0.78); text-align: left; }

  .auth-body { padding: 28px 28px 32px; }

  /* Section title */
  .section-label {
    font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
    color: #9d174d; margin: 22px 0 14px; display: flex; align-items: center; gap: 8px;
  }
  .section-label::after {
    content: ''; flex: 1; height: 1px; background: #fff1f2;
  }

  /* Grid fields */
  .fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .fields-grid .full { grid-column: 1 / -1; }
  @media (max-width: 580px) { .fields-grid { grid-template-columns: 1fr; } }

  .field-group { display: flex; flex-direction: column; }
  .field-group label {
    font-size: 13px; font-weight: 700; color: #555; margin-bottom: 5px;
  }
  .field-group label i { color: #9d174d; margin-right: 4px; }
  .field-group label .req { color: #9d174d; }
  .field-input {
    padding: 10px 13px; border-radius: 9px;
    border: 2px solid #fecdd3; font-size: 14px; outline: none;
    transition: border-color 0.18s, box-shadow 0.18s; color: #333; width: 100%;
  }
  .field-input:focus { border-color: #9d174d; box-shadow: 0 0 0 3px rgba(157,23,77,0.1); }
  .field-input.error { border-color: #e74c3c; }
  .field-error { font-size: 12px; color: #e74c3c; margin-top: 3px; font-weight: 600; }
  .field-hint  { font-size: 11px; color: #bbb; margin-top: 3px; }

  /* Password strength */
  .pwd-wrap { position: relative; }
  .pwd-eye {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    cursor: pointer; color: #fda4af; font-size: 15px; user-select: none;
  }
  .pwd-eye:hover { color: #9d174d; }

  /* Submit */
  .btn-auth {
    display: block; width: 100%;
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    color: #fff; border: none; border-radius: 10px;
    padding: 14px; font-size: 16px; font-weight: 800;
    cursor: pointer; transition: opacity 0.2s; margin-top: 24px;
    letter-spacing: 0.2px;
  }
  .btn-auth:hover { opacity: 0.86; }

  .login-link-row {
    text-align: center; margin-top: 18px; font-size: 13px; color: #aaa;
  }
  .login-link-row a { color: #9d174d; font-weight: 700; text-decoration: none; }
  .login-link-row a:hover { text-decoration: underline; }

  /* Error banner */
  .error-banner {
    background: #fff1f2; border-left: 4px solid #9d174d; border-radius: 8px;
    padding: 10px 14px; margin-bottom: 20px; font-size: 13px; color: #9d174d;
  }
</style>

<div class="auth-wrap">
  <div class="auth-card">

    {{-- HEADER --}}
    <div class="auth-header">
      <img src="{{ asset('img/logo-andyland.png') }}" alt="Andyland PY">
      <div class="htext">
        <h2>Crear cuenta nueva</h2>
        <p>Completá tus datos para registrarte y comprar</p>
      </div>
    </div>

    {{-- BODY --}}
    <div class="auth-body">

      @if($errors->any())
        <div class="error-banner">
          <i class="fa fa-exclamation-circle"></i>
          <strong>Revisá los campos marcados:</strong>
          <ul style="margin:6px 0 0 16px; padding:0;">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        {{-- DATOS PERSONALES --}}
        <p class="section-label"><i class="fa fa-user"></i> Datos personales</p>
        <div class="fields-grid">

          <div class="field-group">
            <label><i class="fa fa-id-card"></i> Nombre <span class="req">*</span></label>
            <input type="text" name="nombre" class="field-input {{ $errors->has('nombre') ? 'error' : '' }}"
              value="{{ old('nombre') }}" required placeholder="Tu nombre">
            @if($errors->has('nombre'))
              <p class="field-error">{{ $errors->first('nombre') }}</p>
            @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-id-card"></i> Apellido</label>
            <input type="text" name="apellido" class="field-input"
              value="{{ old('apellido') }}" placeholder="Tu apellido">
          </div>

          <div class="field-group">
            <label><i class="fa fa-envelope"></i> Correo electrónico <span class="req">*</span></label>
            <input type="email" name="email" class="field-input {{ $errors->has('email') ? 'error' : '' }}"
              value="{{ old('email') }}" required placeholder="tu@correo.com">
            @if($errors->has('email'))
              <p class="field-error">{{ $errors->first('email') }}</p>
            @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-phone"></i> Teléfono / WhatsApp <span class="req">*</span></label>
            <input type="text" name="telefono" class="field-input {{ $errors->has('telefono') ? 'error' : '' }}"
              value="{{ old('telefono') }}" required placeholder="Ej: 0981123456">
            @if($errors->has('telefono'))
              <p class="field-error">{{ $errors->first('telefono') }}</p>
            @endif
          </div>

        </div>

        {{-- DIRECCIÓN --}}
        <p class="section-label"><i class="fa fa-map-marker"></i> Dirección de entrega</p>
        <div class="fields-grid">

          <div class="field-group full">
            <label><i class="fa fa-home"></i> Dirección <span class="req">*</span></label>
            <input type="text" name="direccion" class="field-input {{ $errors->has('direccion') ? 'error' : '' }}"
              value="{{ old('direccion') }}" required placeholder="Calle, número de casa...">
            @if($errors->has('direccion'))
              <p class="field-error">{{ $errors->first('direccion') }}</p>
            @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-building"></i> Ciudad <span class="req">*</span></label>
            <input type="text" name="ciudad" class="field-input {{ $errors->has('ciudad') ? 'error' : '' }}"
              value="{{ old('ciudad') }}" required placeholder="Ej: Asunción">
            @if($errors->has('ciudad'))
              <p class="field-error">{{ $errors->first('ciudad') }}</p>
            @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-map"></i> Barrio</label>
            <input type="text" name="barrio" class="field-input"
              value="{{ old('barrio') }}" placeholder="Ej: Villa Morra">
          </div>

          <div class="field-group">
            <label><i class="fa fa-globe"></i> País</label>
            <input type="text" name="pais" class="field-input"
              value="{{ old('pais', 'Paraguay') }}" placeholder="Paraguay">
          </div>

          <div class="field-group">
            <label><i class="fa fa-info-circle"></i> Referencia</label>
            <input type="text" name="referencia" class="field-input"
              value="{{ old('referencia') }}" placeholder="Ej: Casa color azul, frente a la plaza">
            <span class="field-hint">Ayuda a encontrar tu domicilio más fácil</span>
          </div>

        </div>

        {{-- CONTRASEÑA --}}
        <p class="section-label"><i class="fa fa-lock"></i> Contraseña de acceso</p>
        <div class="fields-grid">

          <div class="field-group">
            <label><i class="fa fa-lock"></i> Contraseña <span class="req">*</span></label>
            <div class="pwd-wrap">
              <input type="password" name="password" id="pwd" class="field-input {{ $errors->has('password') ? 'error' : '' }}"
                required placeholder="Mínimo 6 caracteres">
              <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd', this)"></i>
            </div>
            @if($errors->has('password'))
              <p class="field-error">{{ $errors->first('password') }}</p>
            @endif
          </div>

          <div class="field-group">
            <label><i class="fa fa-lock"></i> Confirmar contraseña <span class="req">*</span></label>
            <div class="pwd-wrap">
              <input type="password" name="password_confirmation" id="pwd2" class="field-input" required placeholder="Repetí tu contraseña">
              <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd2', this)"></i>
            </div>
          </div>

        </div>

        <p style="font-size:12px; color:#bbb; margin-top:14px;">
          <span style="color:#9d174d;">*</span> Campos obligatorios
        </p>

        <button type="submit" class="btn-auth">
          <i class="fa fa-user-plus"></i> Crear mi cuenta
        </button>

      </form>

      <div class="login-link-row">
        ¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciar sesión</a>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
function togglePwd(id, icon) {
  const inp = document.getElementById(id);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  icon.className = 'fa fa-eye' + (show ? '-slash' : '') + ' pwd-eye';
}
</script>
@endpush
@endsection
