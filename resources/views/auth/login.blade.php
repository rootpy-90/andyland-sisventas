@extends('layouts.tienda')
@section('title', 'Iniciar Sesión')
@section('hide_cat_bar', '1')
@section('content')

<style>
  .auth-wrap {
    display: flex; justify-content: center; align-items: flex-start;
    min-height: 60vh; padding: 20px 0 40px;
  }
  .auth-card {
    background: #fff; border-radius: 18px;
    box-shadow: 0 8px 40px rgba(157,23,77,0.13);
    border: 1px solid #fff1f2;
    width: 100%; max-width: 440px; overflow: hidden;
  }
  .auth-header {
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    padding: 32px 28px 28px; text-align: center; color: #fff;
  }
  .auth-header img {
    width: 72px; height: 72px; border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.5);
    object-fit: cover; margin-bottom: 14px; display: block; margin-left: auto; margin-right: auto;
  }
  .auth-header h2 { margin: 0 0 4px; font-size: 22px; font-weight: 900; }
  .auth-header p  { margin: 0; font-size: 13px; color: rgba(255,255,255,0.78); }

  .auth-body { padding: 28px; }

  .field-group { margin-bottom: 18px; }
  .field-group label {
    display: block; font-size: 13px; font-weight: 700;
    color: #555; margin-bottom: 6px;
  }
  .field-group label i { color: #9d174d; margin-right: 5px; }
  .field-input {
    width: 100%; padding: 11px 14px; border-radius: 9px;
    border: 2px solid #fecdd3; font-size: 14px; outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
    color: #333;
  }
  .field-input:focus { border-color: #9d174d; box-shadow: 0 0 0 3px rgba(157,23,77,0.1); }
  .field-input.error { border-color: #e74c3c; }
  .field-error { font-size: 12px; color: #e74c3c; margin-top: 4px; font-weight: 600; }

  .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
  .remember-row input[type=checkbox] { accent-color: #9d174d; width: 16px; height: 16px; }
  .remember-row label { font-size: 13px; color: #888; margin: 0; cursor: pointer; }

  .btn-auth {
    display: block; width: 100%;
    background: linear-gradient(135deg, #9d174d, #7f1d3e);
    color: #fff; border: none; border-radius: 10px;
    padding: 13px; font-size: 15px; font-weight: 800;
    cursor: pointer; transition: opacity 0.2s; letter-spacing: 0.2px;
  }
  .btn-auth:hover { opacity: 0.86; }

  .auth-divider {
    text-align: center; margin: 20px 0; position: relative; color: #ddd; font-size: 13px;
  }
  .auth-divider::before, .auth-divider::after {
    content: ''; position: absolute; top: 50%; width: 40%; height: 1px; background: #fecdd3;
  }
  .auth-divider::before { left: 0; }
  .auth-divider::after  { right: 0; }

  .btn-register-link {
    display: block; width: 100%; text-align: center;
    background: #fff1f2; color: #7f1d3e;
    border: 2px solid #fecdd3; border-radius: 10px;
    padding: 12px; font-size: 14px; font-weight: 700;
    text-decoration: none; transition: background 0.2s;
  }
  .btn-register-link:hover { background: #fecdd3; color: #9d174d; text-decoration: none; }

  .forgot-link {
    display: block; text-align: center; margin-top: 14px;
    font-size: 13px; color: #fda4af; text-decoration: none;
  }
  .forgot-link:hover { color: #9d174d; text-decoration: underline; }
</style>

<div class="auth-wrap">
  <div class="auth-card">

    {{-- HEADER --}}
    <div class="auth-header">
      <img src="{{ asset('img/logo-andyland.png') }}" alt="Andyland PY">
      <h2>¡Bienvenida de vuelta!</h2>
      <p>Iniciá sesión para confirmar tu compra</p>
    </div>

    {{-- BODY --}}
    <div class="auth-body">

      @if($errors->any())
        <div style="background:#fff1f2; border-left:4px solid #9d174d; border-radius:8px; padding:10px 14px; margin-bottom:18px; font-size:13px; color:#9d174d;">
          <i class="fa fa-exclamation-circle"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="field-group">
          <label><i class="fa fa-envelope"></i> Correo electrónico</label>
          <input type="email" name="email" class="field-input {{ $errors->has('email') ? 'error' : '' }}"
            value="{{ old('email') }}" required autofocus placeholder="tu@correo.com">
          @if($errors->has('email'))
            <p class="field-error">{{ $errors->first('email') }}</p>
          @endif
        </div>

        <div class="field-group">
          <label><i class="fa fa-lock"></i> Contraseña</label>
          <input type="password" name="password" class="field-input {{ $errors->has('password') ? 'error' : '' }}"
            required placeholder="••••••••">
          @if($errors->has('password'))
            <p class="field-error">{{ $errors->first('password') }}</p>
          @endif
        </div>

        <div class="remember-row">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label for="remember">Recordar mi sesión</label>
        </div>

        <button type="submit" class="btn-auth">
          <i class="fa fa-sign-in"></i> Iniciar Sesión
        </button>
      </form>

      <a href="{{ route('recuperar.form') }}" class="forgot-link">
        <i class="fa fa-key"></i> ¿Olvidaste tu contraseña?
      </a>

      <div class="auth-divider">¿No tenés cuenta?</div>

      <a href="{{ route('register') }}" class="btn-register-link">
        <i class="fa fa-user-plus"></i> Crear cuenta nueva
      </a>

    </div>
  </div>
</div>
@endsection
