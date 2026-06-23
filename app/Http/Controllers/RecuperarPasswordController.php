<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;

class RecuperarPasswordController extends Controller
{
    /**
     * Mensaje opaco único para TODOS los fallos de verificación.
     * Nunca revelar si el email existe, si el CI es incorrecto, etc.
     */
    private const MSG_FALLO_OPACO =
        'Si los datos ingresados son correctos y pertenecen a una cuenta registrada, podrás continuar con la recuperación.';

    // ──────────────────────────────────────────────────────────
    // PASO 1 — Formulario
    // ──────────────────────────────────────────────────────────
    public function showForm()
    {
        return view('auth.recuperar_password');
    }

    // ──────────────────────────────────────────────────────────
    // PASO 1 — POST: verificar identidad
    // ──────────────────────────────────────────────────────────
    public function verificar(Request $request)
    {
        // Validación de formato (no revela existencia)
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email|max:150',
            'num_documento' => 'required|string|regex:/^[0-9]{6,8}$/',
        ], [
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'Ingresá un correo válido.',
            'num_documento.required'    => 'El número de CI es obligatorio.',
            'num_documento.regex'       => 'El CI debe tener entre 6 y 8 dígitos numéricos.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = trim($request->email);
        $ci    = trim($request->num_documento);

        /*
         * ── CONSULTA ÚNICA con JOIN ──────────────────────────
         * Busca email + CI en una sola query.
         * Esto elimina el timing attack: ambos casos (email
         * inexistente y CI incorrecto) ejecutan exactamente
         * la misma cantidad de work en la BD.
         *
         * NUNCA hacer dos queries separadas:
         *   1. buscar por email  → revela si existe (timing)
         *   2. verificar CI      → solo se ejecuta si email existe
         */
        $resultado = DB::table('users as u')
            ->join('persona as p', 'u.idpersona', '=', 'p.idpersona')
            ->where('u.email',         $email)   // PDO binding — no interpolación
            ->where('p.num_documento', $ci)       // PDO binding — no interpolación
            ->select('u.id')
            ->first();

        /*
         * ── RESPUESTA OPACA ──────────────────────────────────
         * Mismo mensaje para TODOS los fallos:
         *   - Email no existe
         *   - Email existe pero CI incorrecto
         *   - Ambos incorrectos
         *
         * El atacante no puede distinguir cuál falló.
         */
        if (!$resultado) {
            // Log interno para auditoría de seguridad (nunca al cliente)
            Log::warning('Recuperación de contraseña fallida', [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp'  => \Carbon\Carbon::now()->toDateTimeString(),
                // NO loguear email ni CI para proteger privacidad
            ]);

            return redirect()->back()
                ->with('error', self::MSG_FALLO_OPACO)
                ->withInput(['email' => $email]); // Repoblar solo el email (no el CI)
        }

        // Éxito: guardar en sesión y avanzar al paso 2
        session([
            'reset_user_id'  => $resultado->id,
            'reset_verified' => true,
        ]);

        return redirect()->route('recuperar.nueva');
    }

    // ──────────────────────────────────────────────────────────
    // PASO 2 — Formulario nueva contraseña
    // ──────────────────────────────────────────────────────────
    public function showNueva()
    {
        // Verificar que llegó por el flujo correcto
        if (!session('reset_verified') || !session('reset_user_id')) {
            return redirect()->route('recuperar.form')
                ->with('error', 'Primero verificá tu identidad.');
        }

        return view('auth.nueva_password');
    }

    // ──────────────────────────────────────────────────────────
    // PASO 2 — POST: guardar nueva contraseña
    // ──────────────────────────────────────────────────────────
    public function guardar(Request $request)
    {
        // Doble verificación de sesión (no saltar el paso 1)
        if (!session('reset_verified') || !session('reset_user_id')) {
            return redirect()->route('recuperar.form')
                ->with('error', 'Sesión expirada. Intentá de nuevo.');
        }

        $validator = Validator::make($request->all(), [
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $userId = session('reset_user_id');

        // Verificar que el usuario aún existe antes de actualizar
        $userExiste = DB::table('users')->where('id', $userId)->exists();
        if (!$userExiste) {
            session()->forget(['reset_user_id', 'reset_verified']);
            return redirect()->route('recuperar.form')
                ->with('error', 'Ocurrió un error. Intentá nuevamente.');
        }

        DB::table('users')
            ->where('id', $userId)
            ->update(['password' => Hash::make($request->password)]);

        // Limpiar sesión de recuperación inmediatamente
        session()->forget(['reset_user_id', 'reset_verified']);

        Log::info('Contraseña recuperada exitosamente', [
            'user_id'   => $userId,
            'ip'        => $request->ip(),
            'timestamp' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('login')
            ->with('status', '¡Contraseña actualizada correctamente! Ya podés iniciar sesión.');
    }
}
