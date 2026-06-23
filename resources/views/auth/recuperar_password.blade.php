@extends('layouts.tienda')
@section('title', 'Recuperar Contraseña')
@section('hide_cat_bar', '1')
@section('content')

<style>
  .auth-wrap { display:flex; justify-content:center; align-items:flex-start; min-height:60vh; padding:20px 0 40px; }
  .auth-card { background:#fff; border-radius:18px; box-shadow:0 8px 40px rgba(157,23,77,0.12); border:1px solid #fecdd3; width:100%; max-width:460px; overflow:hidden; }

  .auth-header { background:linear-gradient(135deg, #9d174d, #7f1d3e); padding:30px 28px; text-align:center; color:#fff; }
  .auth-header .icon { font-size:44px; margin-bottom:12px; display:block; }
  .auth-header h2 { margin:0 0 4px; font-size:22px; font-weight:900; }
  .auth-header p  { margin:0; font-size:13px; color:rgba(255,255,255,.78); }

  /* Pasos */
  .steps { display:flex; padding:16px 20px; border-bottom:1px solid #fff1f2; }
  .step { flex:1; display:flex; flex-direction:column; align-items:center; position:relative; }
  .step:not(:last-child)::after { content:''; position:absolute; top:14px; left:calc(50% + 14px); width:calc(100% - 28px); height:2px; background:#fecdd3; }
  .step.active:not(:last-child)::after { background:#9d174d; }
  .step-circle { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; margin-bottom:5px; z-index:1; }
  .step.active .step-circle { background:#9d174d; color:#fff; box-shadow:0 0 0 4px rgba(157,23,77,0.15); }
  .step.idle   .step-circle { background:#f5f5f5; color:#ccc; border:2px solid #eee; }
  .step-label { font-size:10px; font-weight:700; color:#aaa; text-transform:uppercase; }
  .step.active .step-label { color:#9d174d; }

  .auth-body { padding:26px 28px; }

  /* ── Campos ── */
  .field-group { margin-bottom:20px; position:relative; }

  .field-group label {
    display:flex; align-items:center; gap:6px;
    font-size:12px; font-weight:700; color:#555;
    text-transform:uppercase; letter-spacing:.4px; margin-bottom:7px;
  }
  .field-group label i { color:#9d174d; }

  .field-wrap { position:relative; }

  .field-input {
    width:100%; padding:11px 42px 11px 14px;
    border-radius:9px; border:2px solid #fecdd3;
    font-size:14px; outline:none; color:#333;
    transition:border-color .18s, box-shadow .18s;
  }
  .field-input:focus  { border-color:#9d174d; box-shadow:0 0 0 3px rgba(157,23,77,0.1); }
  .field-input.valid  { border-color:#16a34a; background:#f0fdf4; }
  .field-input.invalid{ border-color:#e74c3c; background:#fef2f2; }

  /* Ícono de estado dentro del input */
  .field-icon {
    position:absolute; right:13px; top:50%; transform:translateY(-50%);
    font-size:16px; display:none;
  }
  .field-icon.ok  { color:#16a34a; display:block; }
  .field-icon.err { color:#e74c3c; display:block; }

  /* Mensaje de error/éxito debajo del campo */
  .field-msg {
    font-size:12px; font-weight:600; margin-top:5px;
    display:none; align-items:center; gap:5px;
  }
  .field-msg.show-err  { display:flex; color:#e74c3c; }
  .field-msg.show-ok   { display:flex; color:#16a34a; }

  /* Hint debajo del label */
  .field-hint { font-size:11px; color:#aaa; margin-bottom:6px; }

  /* ── Botón ── */
  .btn-auth {
    display:block; width:100%;
    background:linear-gradient(135deg, #9d174d, #7f1d3e);
    color:#fff; border:none; border-radius:10px;
    padding:13px; font-size:15px; font-weight:800;
    cursor:pointer; transition:opacity .2s; margin-top:6px;
  }
  .btn-auth:hover    { opacity:.86; }
  .btn-auth:disabled { background:linear-gradient(135deg,#ccc,#aaa); cursor:not-allowed; opacity:1; }

  .back-link { display:block; text-align:center; margin-top:16px; font-size:13px; color:#aaa; text-decoration:none; }
  .back-link:hover { color:#9d174d; }
</style>

<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-header">
      <span class="icon"><i class="fa fa-lock"></i></span>
      <h2>Recuperar contraseña</h2>
      <p>Verificá tu identidad para restablecer el acceso</p>
    </div>

    {{-- Pasos --}}
    <div class="steps">
      <div class="step active">
        <div class="step-circle">1</div>
        <span class="step-label">Verificar</span>
      </div>
      <div class="step idle">
        <div class="step-circle">2</div>
        <span class="step-label">Nueva clave</span>
      </div>
    </div>

    <div class="auth-body">

      {{-- Mensaje del servidor (opaco — solo para fallo de BD) --}}
      @if(session('error'))
        <div style="background:#fef2f2; border-left:4px solid #e74c3c; border-radius:8px; padding:11px 14px; margin-bottom:20px; font-size:13px; color:#991b1b; font-weight:600;">
          <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ route('recuperar.verificar') }}" id="form-recuperar" novalidate>
        {{ csrf_field() }}

        {{-- ── CAMPO 1: EMAIL ── --}}
        <div class="field-group">
          <label for="email">
            <i class="fa fa-envelope"></i> Correo electrónico
          </label>
          <p class="field-hint">El correo con el que te registraste en la tienda.</p>
          <div class="field-wrap">
            <input
              type="email"
              id="email"
              name="email"
              class="field-input {{ $errors->has('email') ? 'invalid' : '' }}"
              value="{{ old('email') }}"
              placeholder="tu@correo.com"
              autocomplete="email"
              maxlength="150"
            >
            <i class="fa field-icon" id="icon-email"></i>
          </div>
          {{-- Error del servidor --}}
          @if($errors->has('email'))
            <div class="field-msg show-err">
              <i class="fa fa-times-circle"></i> {{ $errors->first('email') }}
            </div>
          @endif
          {{-- Error del cliente (JS) --}}
          <div class="field-msg" id="msg-email"></div>
        </div>

        {{-- ── CAMPO 2: CI ── --}}
        <div class="field-group">
          <label for="num_documento">
            <i class="fa fa-id-card"></i> Número de Cédula de Identidad
          </label>
          <p class="field-hint">Solo dígitos, entre 6 y 8 caracteres. Sin puntos ni guiones.</p>
          <div class="field-wrap">
            <input
              type="text"
              id="num_documento"
              name="num_documento"
              class="field-input {{ $errors->has('num_documento') ? 'invalid' : '' }}"
              value="{{ old('num_documento') }}"
              placeholder="Ej: 4540123"
              inputmode="numeric"
              maxlength="8"
            >
            <i class="fa field-icon" id="icon-ci"></i>
          </div>
          {{-- Error del servidor --}}
          @if($errors->has('num_documento'))
            <div class="field-msg show-err">
              <i class="fa fa-times-circle"></i> {{ $errors->first('num_documento') }}
            </div>
          @endif
          {{-- Error del cliente (JS) --}}
          <div class="field-msg" id="msg-ci"></div>
        </div>

        <button type="submit" class="btn-auth" id="btn-verificar" disabled>
          <i class="fa fa-check-circle"></i> Verificar identidad
        </button>
      </form>

      <a href="{{ route('login') }}" class="back-link">
        <i class="fa fa-arrow-left"></i> Volver al inicio de sesión
      </a>

    </div>
  </div>
</div>

@push('scripts')
<script>
/* ════════════════════════════════════════════════════
   VALIDACIÓN CLIENTE — Tiempo real por campo
   Nota: esta validación es solo UX.
   El servidor valida de forma independiente.
   ════════════════════════════════════════════════════ */

const REGEX_EMAIL = /^[a-zA-Z0-9._%+\-]{1,64}@[a-zA-Z0-9.\-]{1,253}\.[a-zA-Z]{2,10}$/;
const REGEX_CI    = /^[0-9]{6,8}$/;

const inputEmail = document.getElementById('email');
const inputCI    = document.getElementById('num_documento');
const btnSubmit  = document.getElementById('btn-verificar');

let emailValido = false;
let ciValido    = false;

/* ── Función genérica de estado visual ── */
function setEstado(inputEl, iconEl, msgEl, esValido, msgError, msgOk) {
  inputEl.classList.remove('valid', 'invalid');
  iconEl.classList.remove('ok', 'err', 'fa-check-circle', 'fa-times-circle');
  msgEl.classList.remove('show-ok', 'show-err');

  if (esValido) {
    inputEl.classList.add('valid');
    iconEl.classList.add('ok', 'fa-check-circle');
    msgEl.innerHTML = '<i class="fa fa-check-circle"></i> ' + msgOk;
    msgEl.classList.add('show-ok');
  } else {
    inputEl.classList.add('invalid');
    iconEl.classList.add('err', 'fa-times-circle');
    msgEl.innerHTML = '<i class="fa fa-times-circle"></i> ' + msgError;
    msgEl.classList.add('show-err');
  }

  actualizarBoton();
}

/* ── Validar email ── */
function validarEmail() {
  const val   = inputEmail.value.trim();
  const iconEl = document.getElementById('icon-email');
  const msgEl  = document.getElementById('msg-email');

  if (val.length === 0) {
    // Campo vacío — resetear sin mostrar error todavía
    inputEmail.classList.remove('valid', 'invalid');
    iconEl.className = 'fa field-icon';
    msgEl.className  = 'field-msg';
    emailValido = false;
    actualizarBoton();
    return;
  }

  emailValido = REGEX_EMAIL.test(val);
  setEstado(
    inputEmail, iconEl, msgEl,
    emailValido,
    'Ingresá un correo válido (ej: nombre@dominio.com)',
    'Formato de correo correcto'
  );
}

/* ── Validar CI ── */
function validarCI() {
  const val    = inputCI.value.trim();
  const iconEl = document.getElementById('icon-ci');
  const msgEl  = document.getElementById('msg-ci');

  if (val.length === 0) {
    inputCI.classList.remove('valid', 'invalid');
    iconEl.className = 'fa field-icon';
    msgEl.className  = 'field-msg';
    ciValido = false;
    actualizarBoton();
    return;
  }

  // Bloquear letras en tiempo real
  if (/[^0-9]/.test(val)) {
    ciValido = false;
    setEstado(
      inputCI, iconEl, msgEl,
      false,
      'La CI solo puede contener dígitos (0-9), sin letras ni símbolos',
      ''
    );
    return;
  }

  ciValido = REGEX_CI.test(val);
  setEstado(
    inputCI, iconEl, msgEl,
    ciValido,
    val.length < 6
      ? 'La CI debe tener al menos 6 dígitos'
      : 'La CI debe tener entre 6 y 8 dígitos',
    'CI válida'
  );
}

/* ── Habilitar / deshabilitar botón ── */
function actualizarBoton() {
  btnSubmit.disabled = !(emailValido && ciValido);
}

/* ── Bloquear pegado de letras en CI ── */
inputCI.addEventListener('input', function() {
  // Eliminar cualquier caracter no numérico mientras escribe
  this.value = this.value.replace(/[^0-9]/g, '');
  validarCI();
});

inputCI.addEventListener('paste', function(e) {
  e.preventDefault();
  const pegado = (e.clipboardData || window.clipboardData).getData('text');
  this.value = pegado.replace(/[^0-9]/g, '').slice(0, 8);
  validarCI();
});

/* ── Eventos de validación ── */
inputEmail.addEventListener('input',  validarEmail);
inputEmail.addEventListener('blur',   validarEmail);
inputCI.addEventListener('blur',      validarCI);

/* ── Validación al enviar ── */
document.getElementById('form-recuperar').addEventListener('submit', function(e) {
  validarEmail();
  validarCI();

  if (!emailValido || !ciValido) {
    e.preventDefault();
    if (!emailValido) inputEmail.focus();
    else if (!ciValido) inputCI.focus();
  }
});

/* ── Si hay errores del servidor, mostrar estado visual ── */
@if($errors->has('email'))
  inputEmail.classList.add('invalid');
  document.getElementById('icon-email').classList.add('err', 'fa-times-circle');
@endif

@if($errors->has('num_documento'))
  inputCI.classList.add('invalid');
  document.getElementById('icon-ci').classList.add('err', 'fa-times-circle');
@endif

/* ── Si el campo ya tiene valor guardado (old()), validarlo ── */
if (inputEmail.value) validarEmail();
if (inputCI.value)    validarCI();
</script>
@endpush

@endsection
