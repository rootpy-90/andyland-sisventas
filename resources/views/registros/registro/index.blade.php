<!-- ESTE ES MI VISTA-->
@extends ('layouts.admin')
@section ('contenido')


<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3> Listado de Articulos mas vendidos </h3>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Articulo</th>
					<th>Pedidos realizados</th>
					<th>Cantidad total vendida</th>
				</thead>
			   @foreach ($registros as $reg)
				<tr>
					<td>{{ $reg->nombre}}</td>
					<td>{{ $reg->cont_product}}</td>
					<td>{{ $reg->total_product}}</td>	
				</tr>
				@endforeach

			</table>
		</div>
		{{$registros->render()}}
	</div>
</div>



@endsection


