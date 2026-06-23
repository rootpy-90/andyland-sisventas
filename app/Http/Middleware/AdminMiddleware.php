<?php

namespace sisVentas\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rol = \DB::table('roles')->where('idrol', auth()->user()->idrol)->first();

        if (!$rol || !$rol->es_admin) {
            return redirect('tienda');
        }

        return $next($request);
    }
}
