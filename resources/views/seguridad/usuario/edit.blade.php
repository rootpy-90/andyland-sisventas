@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Usuario: {{ $usuario->name}}</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
				@endforeach
				</ul>
			</div>
			@endif

			{!!Form::model($usuario,['method'=>'PATCH','route'=>['usuario.update',$usuario->id]])!!}
			{{Form::token()}}

			{{-- Nombre --}}
			<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
				<label class="col-md-4 control-label">Nombre</label>
				<div class="col-md-6">
					<input type="text" class="form-control" name="name"
						value="{{ $usuario->name }}" required>
					@if($errors->has('name'))
						<span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
					@endif
				</div>
			</div>

			{{-- Email: solo lectura --}}
			<div class="form-group">
				<label class="col-md-4 control-label">E-Mail</label>
				<div class="col-md-6">
					<input type="email" class="form-control" value="{{ $usuario->email }}"
						disabled style="background:#f9f9f9; color:#aaa;">
					<span class="help-block" style="font-size:11px;">El correo no se puede modificar.</span>
				</div>
			</div>

			{{-- Rol --}}
			<div class="form-group">
				<label class="col-md-4 control-label">Rol</label>
				<div class="col-md-6">
					<select name="idrol" class="form-control" required>
						@foreach(DB::table('roles')->get() as $rol)
							<option value="{{ $rol->idrol }}"
								{{ $usuario->idrol == $rol->idrol ? 'selected' : '' }}>
								{{ $rol->nombre }}
							</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-6 col-md-offset-4" style="display:flex; gap:10px;">
					<button class="btn btn-primary" type="submit">Guardar</button>
					<a href="{{ url('seguridad/usuario') }}" class="btn btn-default">Cancelar</a>
				</div>
			</div>

			{!!Form::close()!!}

		</div>
	</div>
@endsection
