<?php

namespace sisVentas\Http\Controllers\Auth;

use sisVentas\User;
use sisVentas\Persona;
use sisVentas\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/tienda';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'nullable|string|max:100',
            'email'     => 'required|string|email|max:255|unique:users',
            'telefono'  => 'required|string|max:15',
            'direccion' => 'required|string|max:200',
            'ciudad'    => 'required|string|max:100',
            'barrio'    => 'nullable|string|max:100',
            'pais'      => 'nullable|string|max:100',
            'referencia'=> 'nullable|string|max:500',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.unique'       => 'Este correo ya está registrado.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'direccion.required' => 'La dirección de entrega es obligatoria.',
            'ciudad.required'    => 'La ciudad es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);
    }

    protected function create(array $data)
    {
        $persona = new Persona;
        $persona->tipo_persona  = 'Cliente';
        $persona->nombre        = $data['nombre'];
        $persona->apellido      = $data['apellido'] ?? null;
        $persona->email         = $data['email'];
        $persona->telefono      = $data['telefono'];
        $persona->direccion     = $data['direccion'];
        $persona->ciudad        = $data['ciudad'];
        $persona->barrio        = $data['barrio'] ?? null;
        $persona->pais          = $data['pais'] ?? 'Paraguay';
        $persona->referencia    = $data['referencia'] ?? null;
        $persona->tipo_documento = 'CI';
        $persona->save();

        $nombreCompleto = trim($data['nombre'] . ' ' . ($data['apellido'] ?? ''));

        return User::create([
            'name'       => $nombreCompleto,
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
            'idrol'      => 2,
            'idpersona'  => $persona->idpersona,
        ]);
    }
}
