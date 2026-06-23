@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Gestión de Funcionarios <a href="usuario/create"><button class="btn btn-success">Nuevo Funcionario</button></a></h3>
		<p style="font-size:13px; color:#888; margin-top:-8px;">Los clientes se registran desde la tienda pública.</p>
		@include('seguridad.usuario.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Nombre</th>
					<th>Email</th>
					<th>Rol</th>
					<th>Opciones</th>
				</thead>
			   @foreach ($usuarios as $usu)
				<tr>
					<td>{{ $usu->id}}</td>
					<td>{{ $usu->name}}</td>
					<td>{{ $usu->email}}</td>
					<td>
						@php
							$rolInfo = DB::table('roles')->where('idrol', $usu->idrol)->first();
						@endphp
						@if($rolInfo && $rolInfo->es_admin)
							<span class="label" style="background:#be185d; padding:4px 10px; border-radius:10px; font-size:11px; font-weight:700;">
								<i class="fa fa-shield"></i> {{ $rolInfo->nombre }}
							</span>
						@else
							<span class="label" style="background:#1a6fa3; padding:4px 10px; border-radius:10px; font-size:11px; font-weight:700;">
								<i class="fa fa-user"></i> Cliente
							</span>
						@endif
					</td>
					<td>
						<a href="{{URL::action('UsuarioController@edit',$usu->id)}}"><button class="btn btn-info">Editar</button></a>
						 <a href="" data-target="#modal-delete-{{$usu->id}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('seguridad.usuario.modal')
				@endforeach
			</table>
		</div>
		{{$usuarios->render()}}
	</div>
</div>

@endsection
