@extends('layouts.admin')
@section('page_title', 'Editar Producto')
@section('page_subtitle', 'Almacén')
@section('box_title', 'Editar: '.$articulo->nombre)
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

  .finput { padding:9px 13px; border:2px solid #e2e8f0; border-radius:8px; font-size:14px; color:#1e293b; outline:none; width:100%; transition:border-color .18s, box-shadow .18s; background:#fff; }
  .finput:focus { border-color:#be185d; box-shadow:0 0 0 3px rgba(190,24,93,0.1); }
  .finput.is-invalid { border-color:#e74c3c; background:#fef2f2; }
  .field-error { font-size:12px; color:#e74c3c; margin-top:4px; font-weight:600; display:flex; align-items:center; gap:4px; }
  textarea.finput { resize:vertical; min-height:80px; }

  .img-actual { border:2px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px; text-align:center; background:#f8fafc; padding:12px; }
  .img-actual img { max-height:160px; max-width:100%; border-radius:8px; object-fit:contain; }
  .img-actual p { font-size:11px; color:#94a3b8; margin:8px 0 0; }

  .img-preview-wrap { border:2px dashed #e2e8f0; border-radius:10px; padding:16px; text-align:center; cursor:pointer; background:#f8fafc; transition:all .2s; }
  .img-preview-wrap:hover { border-color:#be185d; background:#fff1f2; }
  .img-preview { max-height:120px; max-width:100%; border-radius:8px; display:none; margin:0 auto 8px; }
  input[type=file] { display:none; }

  .estado-toggle { display:flex; gap:10px; }
  .estado-opt { flex:1; }
  .estado-opt input { display:none; }
  .estado-opt label { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; color:#64748b; }
  .estado-opt input:checked + label.activo   { background:#d5f5e3; border-color:#16a34a; color:#16a34a; }
  .estado-opt input:checked + label.inactivo { background:#fef2f2; border-color:#e74c3c; color:#e74c3c; }

  .btn-guardar { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border:none; border-radius:9px; padding:11px 28px; font-size:14px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:opacity .2s; }
  .btn-guardar:hover { opacity:.86; }
  .btn-cancelar { background:#f1f5f9; color:#64748b; border:none; border-radius:9px; padding:11px 22px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:background .2s; }
  .btn-cancelar:hover { background:#e2e8f0; color:#1e293b; }

  .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  @media(max-width:768px) { .grid-2 { grid-template-columns:1fr; } }

  .badge-stock { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
</style>

@if($errors->any())
<div style="background:#fef2f2; border-left:4px solid #e74c3c; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
  <p style="font-size:13px; font-weight:700; color:#991b1b; margin:0 0 6px;"><i class="fa fa-exclamation-circle"></i> Corregí los siguientes errores:</p>
  <ul style="margin:0; padding-left:18px; font-size:13px; color:#991b1b;">
    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
  </ul>
</div>
@endif

{!! Form::model($articulo, ['method' => 'PATCH', 'route' => ['articulo.update', $articulo->idarticulo], 'files' => true, 'id' => 'form-producto']) !!}

<div class="grid-2" style="align-items:start;">

  {{-- ══ COLUMNA IZQUIERDA ══ --}}
  <div>

    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-info-circle"></i> Información del producto</div>
      <div class="form-card-body">

        <div class="fg">
          <label><i class="fa fa-tag"></i> Nombre <span class="req">*</span></label>
          <input type="text" name="nombre" class="finput {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
            value="{{ old('nombre', $articulo->nombre) }}" required>
          @if($errors->has('nombre'))
            <span class="field-error"><i class="fa fa-times-circle"></i> {{ $errors->first('nombre') }}</span>
          @endif
        </div>

        <div class="grid-2">
          <div class="fg">
            <label><i class="fa fa-barcode"></i> Código <span class="req">*</span></label>
            <input type="text" name="codigo" class="finput {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
              value="{{ old('codigo', $articulo->codigo) }}" required>
          </div>
          <div class="fg">
            <label><i class="fa fa-th-large"></i> Categoría</label>
            <select name="idcategoria" class="finput">
              @foreach($categorias as $cat)
                <option value="{{ $cat->idcategoria }}"
                  {{ old('idcategoria', $articulo->idcategoria) == $cat->idcategoria ? 'selected' : '' }}>
                  {{ $cat->nombre }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="fg">
          <label><i class="fa fa-align-left"></i> Descripción</label>
          <textarea name="descripcion" class="finput">{{ old('descripcion', $articulo->descripcion) }}</textarea>
        </div>

      </div>
    </div>

    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-money"></i> Precio y Stock</div>
      <div class="form-card-body">
        <div class="grid-2">
          <div class="fg">
            <label><i class="fa fa-money"></i> Precio (Gs.) <span class="req">*</span></label>
            <input type="number" name="precio" class="finput"
              value="{{ old('precio', $articulo->precio) }}" min="0" step="1" required>
          </div>
          <div class="fg">
            <label>
              <i class="fa fa-cubes"></i> Stock actual
              @if($articulo->stock <= 0)
                <span class="badge-stock" style="background:#fef2f2; color:#e74c3c; font-size:10px;">Sin stock</span>
              @elseif($articulo->stock <= 5)
                <span class="badge-stock" style="background:#fef9e7; color:#b7770d; font-size:10px;">Bajo</span>
              @endif
            </label>
            <input type="number" name="stock" class="finput"
              value="{{ old('stock', $articulo->stock) }}" min="0" step="1" required>
          </div>
        </div>

        <div class="fg">
          <label><i class="fa fa-clock-o"></i> Tiempo de entrega estimado</label>
          <input type="text" name="tiempo_entrega" class="finput"
            value="{{ old('tiempo_entrega', $articulo->tiempo_entrega ?? 'Disponible de inmediato') }}">
        </div>

        <div class="fg">
          <label><i class="fa fa-toggle-on"></i> Estado</label>
          <div class="estado-toggle">
            <div class="estado-opt">
              <input type="radio" name="estado" id="est-activo" value="Activo"
                {{ old('estado', $articulo->estado) === 'Activo' ? 'checked' : '' }}>
              <label for="est-activo" class="activo"><i class="fa fa-check-circle"></i> Activo</label>
            </div>
            <div class="estado-opt">
              <input type="radio" name="estado" id="est-inactivo" value="Inactivo"
                {{ old('estado', $articulo->estado) === 'Inactivo' ? 'checked' : '' }}>
              <label for="est-inactivo" class="inactivo"><i class="fa fa-ban"></i> Inactivo</label>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ══ COLUMNA DERECHA ══ --}}
  <div>
    <div class="form-card">
      <div class="form-card-head"><i class="fa fa-image"></i> Imagen del producto</div>
      <div class="form-card-body">

        {{-- Imagen actual --}}
        @if($articulo->imagen)
        <div class="img-actual">
          <p style="font-size:11px; color:#94a3b8; margin:0 0 8px; font-weight:700; text-transform:uppercase;">Imagen actual</p>
          <img src="{{ asset('imagenes/articulos/'.rawurlencode($articulo->imagen)) }}"
               onerror="this.style.display='none'" alt="{{ $articulo->nombre }}">
          <p>{{ $articulo->imagen }}</p>
        </div>
        @endif

        {{-- Nueva imagen --}}
        <div class="img-preview-wrap" onclick="document.getElementById('input-imagen').click()">
          <img id="img-preview" class="img-preview" src="" alt="">
          <div id="img-placeholder">
            <i class="fa fa-cloud-upload" style="font-size:28px; color:#cbd5e1; display:block; margin-bottom:6px;"></i>
            <p style="font-size:13px; color:#94a3b8; margin:0;">
              {{ $articulo->imagen ? 'Hacé clic para cambiar la imagen' : 'Hacé clic para agregar una imagen' }}
            </p>
            <p style="font-size:11px; color:#cbd5e1; margin:4px 0 0;">JPG, PNG · Máx. 5MB</p>
          </div>
          <div id="img-nombre" style="font-size:12px; color:#be185d; font-weight:700; display:none; margin-top:6px;"></div>
        </div>
        <input type="file" id="input-imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp"
          onchange="previewImagen(this)">
      </div>
    </div>
  </div>

</div>

{{-- Botones --}}
<div style="display:flex; gap:12px; align-items:center; padding-top:8px;">
  <button type="submit" class="btn-guardar">
    <i class="fa fa-save"></i> Guardar Cambios
  </button>
  <a href="{{ url('almacen/articulo') }}" class="btn-cancelar">
    <i class="fa fa-times"></i> Cancelar
  </a>
</div>

{!! Form::close() !!}

@push('scripts')
<script>
function previewImagen(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const reader = new FileReader();
  reader.onload = function(e) {
    const img = document.getElementById('img-preview');
    const ph  = document.getElementById('img-placeholder');
    const nom = document.getElementById('img-nombre');
    img.src = e.target.result;
    img.style.display = 'block';
    ph.style.display  = 'none';
    nom.textContent   = file.name;
    nom.style.display = 'block';
  };
  reader.readAsDataURL(file);
}
</script>
@endpush

@endsection
