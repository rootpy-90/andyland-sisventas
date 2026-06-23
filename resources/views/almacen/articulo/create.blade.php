@extends('layouts.admin')
@section('page_title', 'Nuevo Producto')
@section('page_subtitle', 'Almacén')
@section('box_title', 'Crear Nuevo Producto')
@section('contenido')

<style>
  .form-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px; }
  .form-card-head { padding:13px 20px; border-bottom:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:800; color:#1e293b; }
  .form-card-head i { color:#be185d; }
  .form-card-body { padding:20px; }

  .fg { display:flex; flex-direction:column; margin-bottom:16px; }
  .fg label { font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; display:flex; align-items:center; gap:5px; }
  .fg label .req { color:#e74c3c; }
  .fg label i { color:#be185d; }

  .finput {
    padding:9px 13px; border:2px solid #e2e8f0; border-radius:8px;
    font-size:14px; color:#1e293b; outline:none; width:100%;
    transition:border-color .18s, box-shadow .18s; background:#fff;
  }
  .finput:focus { border-color:#be185d; box-shadow:0 0 0 3px rgba(190,24,93,0.1); }
  .finput.is-invalid { border-color:#e74c3c; background:#fef2f2; }
  .field-error { font-size:12px; color:#e74c3c; margin-top:4px; font-weight:600; display:flex; align-items:center; gap:4px; }

  select.finput { cursor:pointer; }
  textarea.finput { resize:vertical; min-height:80px; }

  /* Preview imagen */
  .img-preview-wrap { border:2px dashed #e2e8f0; border-radius:10px; padding:20px; text-align:center; cursor:pointer; background:#f8fafc; transition:all .2s; }
  .img-preview-wrap:hover { border-color:#be185d; background:#fff1f2; }
  .img-preview-wrap.has-img { border-style:solid; border-color:#be185d; }
  .img-preview { max-height:160px; max-width:100%; border-radius:8px; display:none; margin:0 auto 10px; }
  .img-placeholder i { font-size:36px; color:#cbd5e1; display:block; margin-bottom:8px; }
  .img-placeholder p { font-size:13px; color:#94a3b8; margin:0; }
  input[type=file] { display:none; }

  /* Badge estado */
  .estado-toggle { display:flex; gap:10px; }
  .estado-opt { flex:1; }
  .estado-opt input { display:none; }
  .estado-opt label {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px; border-radius:8px; border:2px solid #e2e8f0;
    font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; color:#64748b;
  }
  .estado-opt input:checked + label.activo   { background:#d5f5e3; border-color:#16a34a; color:#16a34a; }
  .estado-opt input:checked + label.inactivo { background:#fef2f2; border-color:#e74c3c; color:#e74c3c; }
  .estado-opt label:hover { border-color:#be185d; }

  /* Botones */
  .btn-guardar { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border:none; border-radius:9px; padding:11px 28px; font-size:14px; font-weight:800; cursor:pointer; transition:opacity .2s; display:inline-flex; align-items:center; gap:8px; }
  .btn-guardar:hover { opacity:.86; }
  .btn-cancelar { background:#f1f5f9; color:#64748b; border:none; border-radius:9px; padding:11px 22px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:background .2s; }
  .btn-cancelar:hover { background:#e2e8f0; color:#1e293b; }

  /* Grid */
  .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
  @media(max-width:768px) { .grid-2,.grid-3 { grid-template-columns:1fr; } }
</style>

{{-- Errores globales --}}
@if($errors->any())
<div style="background:#fef2f2; border-left:4px solid #e74c3c; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
  <p style="font-size:13px; font-weight:700; color:#991b1b; margin:0 0 6px;"><i class="fa fa-exclamation-circle"></i> Corregí los siguientes errores:</p>
  <ul style="margin:0; padding-left:18px; font-size:13px; color:#991b1b;">
    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
  </ul>
</div>
@endif

{!! Form::open(['url' => 'almacen/articulo', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true, 'id' => 'form-producto']) !!}

<div class="grid-2" style="align-items:start;">

  {{-- ══ COLUMNA IZQUIERDA ══ --}}
  <div>

    {{-- Información básica --}}
    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-info-circle"></i> Información del producto</div>
      <div class="form-card-body">

        <div class="fg">
          <label><i class="fa fa-tag"></i> Nombre <span class="req">*</span></label>
          <input type="text" name="nombre" class="finput {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
            value="{{ old('nombre') }}" placeholder="Nombre del producto..." required>
          @if($errors->has('nombre'))
            <span class="field-error"><i class="fa fa-times-circle"></i> {{ $errors->first('nombre') }}</span>
          @endif
        </div>

        <div class="grid-2">
          <div class="fg">
            <label><i class="fa fa-barcode"></i> Código <span class="req">*</span></label>
            <input type="text" name="codigo" class="finput {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
              value="{{ old('codigo') }}" placeholder="Ej: ART-001" required>
            @if($errors->has('codigo'))
              <span class="field-error"><i class="fa fa-times-circle"></i> {{ $errors->first('codigo') }}</span>
            @endif
          </div>

          <div class="fg">
            <label><i class="fa fa-th-large"></i> Categoría <span class="req">*</span></label>
            <select name="idcategoria" class="finput" required>
              <option value="">— Seleccioná —</option>
              @foreach($categorias as $cat)
                <option value="{{ $cat->idcategoria }}" {{ old('idcategoria') == $cat->idcategoria ? 'selected' : '' }}>
                  {{ $cat->nombre }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="fg">
          <label><i class="fa fa-align-left"></i> Descripción</label>
          <textarea name="descripcion" class="finput" placeholder="Descripción del producto...">{{ old('descripcion') }}</textarea>
        </div>

      </div>
    </div>

    {{-- Precios y stock --}}
    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-money"></i> Precio y Stock</div>
      <div class="form-card-body">
        <div class="grid-2">

          <div class="fg">
            <label><i class="fa fa-money"></i> Precio (Gs.) <span class="req">*</span></label>
            <input type="number" name="precio" class="finput {{ $errors->has('precio') ? 'is-invalid' : '' }}"
              value="{{ old('precio') }}" placeholder="Ej: 150000" min="0" step="1" required>
            @if($errors->has('precio'))
              <span class="field-error"><i class="fa fa-times-circle"></i> {{ $errors->first('precio') }}</span>
            @endif
          </div>

          <div class="fg">
            <label><i class="fa fa-cubes"></i> Stock inicial <span class="req">*</span></label>
            <input type="number" name="stock" class="finput {{ $errors->has('stock') ? 'is-invalid' : '' }}"
              value="{{ old('stock', 0) }}" placeholder="0" min="0" step="1" required>
            @if($errors->has('stock'))
              <span class="field-error"><i class="fa fa-times-circle"></i> {{ $errors->first('stock') }}</span>
            @endif
          </div>

        </div>

        <div class="fg">
          <label><i class="fa fa-clock-o"></i> Tiempo de entrega estimado</label>
          <input type="text" name="tiempo_entrega" class="finput"
            value="{{ old('tiempo_entrega', 'Disponible de inmediato') }}"
            placeholder="Ej: 3-5 días hábiles">
          <span style="font-size:11px; color:#94a3b8; margin-top:4px;">Este tiempo se muestra al cliente en la tienda y en el checkout.</span>
        </div>

        <div class="fg">
          <label><i class="fa fa-toggle-on"></i> Estado del producto</label>
          <div class="estado-toggle">
            <div class="estado-opt">
              <input type="radio" name="estado" id="est-activo" value="Activo" {{ old('estado', 'Activo') === 'Activo' ? 'checked' : '' }}>
              <label for="est-activo" class="activo"><i class="fa fa-check-circle"></i> Activo</label>
            </div>
            <div class="estado-opt">
              <input type="radio" name="estado" id="est-inactivo" value="Inactivo" {{ old('estado') === 'Inactivo' ? 'checked' : '' }}>
              <label for="est-inactivo" class="inactivo"><i class="fa fa-ban"></i> Inactivo</label>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ══ COLUMNA DERECHA ══ --}}
  <div>

    {{-- Imagen --}}
    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-image"></i> Imagen del producto</div>
      <div class="form-card-body">
        <div class="img-preview-wrap" id="drop-zone" onclick="document.getElementById('input-imagen').click()">
          <img id="img-preview" class="img-preview" src="" alt="Preview">
          <div class="img-placeholder" id="img-placeholder">
            <i class="fa fa-cloud-upload"></i>
            <p>Hacé clic o arrastrá la imagen aquí</p>
            <p style="font-size:11px; color:#cbd5e1; margin-top:4px;">JPG, PNG · Máx. 5MB</p>
          </div>
          <div id="img-nombre" style="font-size:12px; color:#be185d; font-weight:700; display:none; margin-top:6px;"></div>
        </div>
        <input type="file" id="input-imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp"
          onchange="previewImagen(this)">
        <button type="button" onclick="document.getElementById('input-imagen').click()"
          style="width:100%; margin-top:10px; background:#f1f5f9; border:none; border-radius:8px; padding:8px; font-size:13px; font-weight:700; color:#64748b; cursor:pointer;">
          <i class="fa fa-folder-open"></i> Seleccionar archivo
        </button>
      </div>
    </div>

    {{-- Resumen antes de guardar --}}
    <div class="form-card" id="resumen-card" style="display:none;">
      <div class="form-card-head"><i class="fa fa-eye"></i> Vista previa</div>
      <div class="form-card-body" style="font-size:13px; color:#555; line-height:1.8;">
        <p><b>Nombre:</b> <span id="prev-nombre">—</span></p>
        <p><b>Código:</b> <span id="prev-codigo">—</span></p>
        <p><b>Precio:</b> <span id="prev-precio">—</span> Gs.</p>
        <p><b>Stock:</b> <span id="prev-stock">—</span> unid.</p>
        <p><b>Entrega:</b> <span id="prev-entrega">—</span></p>
      </div>
    </div>

  </div>

</div>

{{-- Botones --}}
<div style="display:flex; gap:12px; align-items:center; padding-top:8px;">
  <button type="submit" class="btn-guardar">
    <i class="fa fa-save"></i> Guardar Producto
  </button>
  <a href="{{ url('almacen/articulo') }}" class="btn-cancelar">
    <i class="fa fa-times"></i> Cancelar
  </a>
</div>

{!! Form::close() !!}

@push('scripts')
<script>
/* ── Preview de imagen ── */
function previewImagen(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const reader = new FileReader();
  reader.onload = function(e) {
    const img  = document.getElementById('img-preview');
    const ph   = document.getElementById('img-placeholder');
    const nom  = document.getElementById('img-nombre');
    const zone = document.getElementById('drop-zone');
    img.src = e.target.result;
    img.style.display = 'block';
    ph.style.display  = 'none';
    nom.textContent   = file.name;
    nom.style.display = 'block';
    zone.classList.add('has-img');
  };
  reader.readAsDataURL(file);
}

/* ── Drag & Drop ── */
const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.style.borderColor='#be185d'; });
dropZone.addEventListener('dragleave', e => { dropZone.style.borderColor=''; });
dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.style.borderColor = '';
  const file = e.dataTransfer.files[0];
  if (!file) return;
  const dt = new DataTransfer();
  dt.items.add(file);
  document.getElementById('input-imagen').files = dt.files;
  previewImagen(document.getElementById('input-imagen'));
});

/* ── Vista previa en tiempo real ── */
function actualizarPreview() {
  const nombre  = document.querySelector('[name=nombre]').value;
  const codigo  = document.querySelector('[name=codigo]').value;
  const precio  = document.querySelector('[name=precio]').value;
  const stock   = document.querySelector('[name=stock]').value;
  const entrega = document.querySelector('[name=tiempo_entrega]').value;

  if (nombre || codigo || precio) {
    document.getElementById('resumen-card').style.display = 'block';
    document.getElementById('prev-nombre').textContent  = nombre  || '—';
    document.getElementById('prev-codigo').textContent  = codigo  || '—';
    document.getElementById('prev-precio').textContent  = precio ? Number(precio).toLocaleString('es-PY') : '—';
    document.getElementById('prev-stock').textContent   = stock   || '0';
    document.getElementById('prev-entrega').textContent = entrega || '—';
  }
}

['nombre','codigo','precio','stock','tiempo_entrega'].forEach(function(n) {
  const el = document.querySelector('[name=' + n + ']');
  if (el) el.addEventListener('input', actualizarPreview);
});
</script>
@endpush

@endsection
