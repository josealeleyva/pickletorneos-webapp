<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Torneo;
use App\Models\PagoTorneo;

class VerificarPagoTorneo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener el torneo del route parameter
        $torneo = $request->route('torneo');

        if ($torneo instanceof Torneo) {
            // Verificar si existe un pago asociado
            $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();

            if (!$pago) {
                // Si no existe registro de pago, redirigir a index
                return redirect()
                    ->route('torneos.index')
                    ->with('error', 'Este torneo no tiene información de pago.');
            }

            // Si el pago está pendiente, solo permitir acceso a checkout y delete
            if ($pago->estado === 'pendiente') {
                // Permitir acceso a rutas de pago y eliminación
                $allowedRoutes = [
                    'pagos.checkout',
                    'pagos.success',
                    'pagos.failure',
                    'pagos.pending',
                    'torneos.destroy',
                ];

                $currentRoute = $request->route()->getName();

                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()
                        ->route('torneos.index')
                        ->with('error', 'Debes completar el pago antes de acceder a este torneo.');
                }
            }

            // Si el pago está pagado o es gratuito, permitir acceso completo
            if (in_array($pago->estado, ['pagado', 'gratuito'])) {
                return $next($request);
            }

            // Si el pago está cancelado o vencido, solo permitir eliminar
            if (in_array($pago->estado, ['cancelado', 'vencido'])) {
                $currentRoute = $request->route()->getName();

                if ($currentRoute !== 'torneos.destroy') {
                    return redirect()
                        ->route('torneos.index')
                        ->with('error', 'Este torneo tiene un pago cancelado o vencido. Por favor, contacta a soporte o elimina el torneo.');
                }
            }
        }

        return $next($request);
    }
}
