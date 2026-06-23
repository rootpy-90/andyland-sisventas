<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use sisVentas\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use sisVentas\Http\Requests\UsuarioFormRequest;
use sisVentas\User;
use DB;

class UsuarioController extends Controller
{
    public function __construct()
  	{
        $this->middleware('auth');
  	}

  	public function index(Request $request)
  	{
  		if ($request)
  		{
  			$query=trim($request->get('searchText'));
  			$usuarios=DB::table('users')->where('name','LIKE','%'.$query.'%')
  			->orderBy('id','desc')
  			->paginate(7);
  			return view('seguridad.usuario.index',["usuarios"=>$usuarios,"searchText"=>$query]);
  		}
  	}

  	public function create()
  	{
  		return view("seguridad.usuario.create");
  	}

  	public function store(UsuarioFormRequest $request)
  	{
  		// Crear registro en persona primero
  		$idpersona = DB::table('persona')->insertGetId([
  			'tipo_persona'   => 'Natural',
  			'nombre'         => $request->get('name'),
  			'tipo_documento' => 'DNI',
  			'num_documento'  => '0000000',
  			'email'          => $request->get('email'),
  			'direccion'      => '',
  			'telefono'       => '',
  		]);

  		$usuario = new User;
  		$usuario->name      = $request->get('name');
   		$usuario->email     = $request->get('email');
  		$usuario->password  = bcrypt($request->get('password'));
  		$usuario->idrol     = $request->get('idrol', 2);
  		$usuario->idpersona = $idpersona;
  		$usuario->save();

		return Redirect::to('seguridad/usuario');
  	}

  	public function edit($id)
  	{
  		return view("seguridad.usuario.edit",["usuario"=>User::findOrFail($id)]);
  	}

  	public function update(Request $request, $id)
  	{
  		$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
  			'name'  => 'required|string|max:255',
  			'idrol' => 'required|integer',
  		]);

  		if ($validator->fails()) {
  			return Redirect::back()->withErrors($validator)->withInput();
  		}

  		$usuario = User::findOrFail($id);
  		$usuario->name  = $request->get('name');
  		$usuario->idrol = $request->get('idrol');
  		$usuario->update();

		return Redirect::to('seguridad/usuario');
  	}

  	public function destroy($id)
  	{
  		$usuarios=DB::table('users')->where('id','=',$id)->delete();
  		return Redirect::to('seguridad/usuario');
  	}
}
