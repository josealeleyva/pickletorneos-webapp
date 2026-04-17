<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirigir superadmin al panel admin
                if ($user->hasRole(Roles::Superadmin->value())) {
                    return redirect()->route('admin.dashboard');
                }

                // Redirigir otros usuarios al dashboard común
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
