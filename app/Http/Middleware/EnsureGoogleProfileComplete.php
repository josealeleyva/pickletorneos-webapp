<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGoogleProfileComplete
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (session('google_pending') && ! $request->routeIs('auth.completar-perfil*')) {
            return redirect()->route('auth.completar-perfil');
        }

        return $next($request);
    }
}
