@extends('layouts.tienda')
@section('title', 'Nueva Contraseña')
@section('hide_cat_bar', '1')
@section('content')

<style>
  .auth-wrap { display:flex; justify-content:center; align-items:flex-start; min-height:60vh; padding:20px 0 40px; }
  .auth-card { background:#fff; border-radius:18px; box-shadow:0 8px 40px rgba(157,23,77,0.12); border:1px solid #fecdd3; width:100%; max-width:460px; overflow:hidden; }

  .auth-header { background:linear-gradient(135deg, #9d174d, #7f1d3e); padding:30px 28px; text-align:center; color:#fff; }
  .auth-header .icon { font-size:44px; margin-bottom:12px; display:block; opacity:.9; }
  .auth-header h2 { margin:0 0 4px; font-size:22px; font-weight:900; }
  .auth-header p  { margin:0; font-size:13px; color:rgba(255,255,255,.78); }

  /* Pasos */
  .steps { display:flex; padding:16px 20px; border-bottom:1px solid #fff1f2; gap:0; }
  .step { flex:1; display:flex; flex-direction:column; align-items:center; position:relative; }
  .step:not(:last-child)::after { content:''; position:absolute; top:14px; left:calc(50% + 14px); width:calc(100% - 28px); height:2px; }
  .step.done::after   { background:#9d174d; }
  .step.active::after { background:#fecdd3; }
  .step-circle { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; margin-bottom:5px; z-index:1; }
  .step.done   .step-circle { background:#9d174d; color:#fff; }
  .step.active .step-circle { background:#9d174d; color:#fff; box-shadow:0 0 0 4px rgba(157,23,77,0.15); }
  .step.idle   .step-circle { background:#f5f5f5; color:#ccc; border:2px solid #eee; }
  .step-label { font-size:10px; font-weight:700; color:#aaa; text-transform:uppercase; }
  .step.done .step-label, .step.active .step-label { color:#9d174d; }

  .auth-body { padding:26px 28px; }

  .field-group { margin-bottom:18px; }
  .field-group label { display:block; font-size:12px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px; }
  .field-group label i { color:#9d174d; margin-right:4px; }

  .pwd-wrap { position:relative; }
  .field-input { width:100%; padding:11px 14px; border-radius:9px; border:2px solid #fecdd3; font-size:14px; outline:none; transition:border-color .18s, box-shadow .18s; color:#333; }
  .field-input:focus { border-color:#9d174d; box-shadow:0 0 0 3px rgba(157,23,77,0.1); }
  .field-input.error { border-color:#e74c3c; }
  .field-error { font-size:12px; color:#e74c3c; margin-top:4px; font-weight:600; }
  .pwd-eye { position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#fda4af; font-size:15px; }
  .pwd-eye:hover { color:#9d174d; }

  /* Indicador fuerza */
  .pwd-strength { margin-top:6px; }
  .strength-bar { height:4px; border-radius:2px; background:#eee; overflow:hidden; }
  .strength-fill { height:100%; border-radius:2px; transition:width .3s, background .3s; width:0%; }
  .strength-label { font-size:11px; font-weight:700; margin-top:3px; }

  .btn-auth { display:block; width:100%; background:linear-gradient(135deg, #9d174d, #7f1d3e); color:#fff; border:none; border-radius:10px; padding:13px; font-size:15px; font-weight:800; cursor:pointer; transition:opacity .2s; }
  .btn-auth:hover { opacity:.86; }
  .btn-auth:disabled { background:linear-gradient(135deg,#ccc,#aaa); cursor:not-allowed; }

  .success-check { text-align:center; padding:10px 0 6px; color:#16a34a; font-size:13px; font-weight:700; display:none; }
</style>

<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-header">
      <span class="icon"><i class="fa fa-key"></i></span>
      <h2>Nueva contraseña</h2>
      <p>Identidad verificada — elegí tu nueva clave de acceso</p>
    </div>

    {{-- Pasos --}}
    <div class="steps">
      <div class="step done">
        <div class="step-circle"><i class="fa fa-check"></i></div>
        <span class="step-label">Verificado</span>
      </div>
      <div class="step active">
        <div class="step-circle">2</div>
        <span class="step-label">Nueva clave</span>
      </div>
    </div>

    <div class="auth-body">

      @if($errors->any())
        <div style="background:#fef2f2; border-left:4px solid #e74c3c; border-radius:8px; padding:10px 14px; margin-bottom:18px; font-size:13px; color:#991b1b; font-weight:600;">
          <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
      @endif

      <div style="background:#f0fdf4; border-left:3px solid #16a34a; border-radius:8px; padding:11px 14px; font-size:12px; color:#166534; margin-bottom:20px;">
        <i class="fa fa-check-circle"></i> <strong>Identidad verificada.</strong> Ahora elegí una contraseña segura.
      </div>

      <form method="POST" action="{{ route('recuperar.guardar') }}" id="form-nueva">
        {{ csrf_field() }}

        <div class="field-group">
          <label><i class="fa fa-lock"></i> Nueva contraseña</label>
          <div class="pwd-wrap">
            <input type="password" name="password" id="pwd1" class="field-input {{ $errors->has('password') ? 'error' : '' }}"
              required placeholder="Mínimo 6 caracteres" oninput="checkStrength(this.value)">
            <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd1', this)"></i>
          </div>
          <div class="pwd-strength">
            <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
            <p class="strength-label" id="strength-label" style="color:#aaa;">Ingresá una contraseña</p>
          </div>
          @if($errors->has('password'))
            <p class="field-error">{{ $errors->first('password') }}</p>
          @endif
        </div>

        <div class="field-group">
          <label><i class="fa fa-lock"></i> Confirmar nueva contraseña</label>
          <div class="pwd-wrap">
            <input type="password" name="password_confirmation" id="pwd2" class="field-input"
              required placeholder="Repetí la contraseña" oninput="checkMatch()">
            <i class="fa fa-eye pwd-eye" onclick="togglePwd('pwd2', this)"></i>
          </div>
          <div class="success-check" id="match-ok">
            <i class="fa fa-check-circle"></i> Las contraseñas coinciden
          </div>
        </div>

        <button type="submit" class="btn-auth" id="btn-submit" disabled>
          <i class="fa fa-save"></i> Guardar nueva contraseña
        </button>
      </form>

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

function checkStrength(val) {
  const fill  = document.getElementById('strength-fill');
  const label = document.getElementById('strength-label');
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { pct:'0%',   color:'#eee',     text:'Ingresá una contraseña', tc:'#aaa' },
    { pct:'25%',  color:'#e74c3c',  text:'Muy débil',   tc:'#e74c3c' },
    { pct:'50%',  color:'#e67e22',  text:'Débil',       tc:'#e67e22' },
    { pct:'70%',  color:'#f39c12',  text:'Regular',     tc:'#b7770d' },
    { pct:'88%',  color:'#27ae60',  text:'Buena',       tc:'#1e8449' },
    { pct:'100%', color:'#16a34a',  text:'Muy segura',  tc:'#166534' },
  ];
  const l = levels[score] || levels[0];
  fill.style.width      = l.pct;
  fill.style.background = l.color;
  label.textContent     = l.text;
  label.style.color     = l.tc;
  checkMatch();
}

function checkMatch() {
  const p1  = document.getElementById('pwd1').value;
  const p2  = document.getElementById('pwd2').value;
  const ok  = document.getElementById('match-ok');
  const btn = document.getElementById('btn-submit');
  const match = p1.length >= 6 && p2.length > 0 && p1 === p2;
  ok.style.display  = match ? 'block' : 'none';
  btn.disabled      = !match;
}
</script>
@endpush

@endsection
