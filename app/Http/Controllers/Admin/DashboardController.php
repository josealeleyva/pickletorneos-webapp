<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\PagoTorneo;
use App\Models\Sugerencia;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas de usuarios
        $totalOrganizadores = User::role(Roles::Organizador->value())->count();
        $organizadoresActivos = User::role(Roles::Organizador->value())
            ->where('cuenta_activa', true)
            ->count();
        $totalJugadores = User::role(Roles::Jugador->value())->count();

        // Métricas de torneos
        $torneosPorEstado = Torneo::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $totalTorneos = Torneo::count();
        $torneosActivos = Torneo::whereIn('estado', ['activo', 'en_curso'])->count();

        // Métricas de pagos
        $ingresosMesActual = PagoTorneo::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('estado', 'pagado')
            ->sum('monto');

        $ingresosTotales = PagoTorneo::where('estado', 'pagado')->sum('monto');

        // Ingresos por mes (últimos 6 meses)
        $ingresosPorMes = PagoTorneo::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
            DB::raw('SUM(monto) as total')
        )
            ->where('estado', 'pagado')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Actividad reciente (últimos 10 eventos)
        $actividadReciente = collect([]);

        // Últimos usuarios registrados
        $ultimosUsuarios = User::latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'tipo' => 'usuario_registrado',
                    'descripcion' => "Nuevo usuario: {$user->nombre} {$user->apellido}",
                    'fecha' => $user->created_at,
                    'icono' => 'user-plus',
                    'color' => 'success',
                ];
            });

        // Últimos torneos creados
        $ultimosTorneos = Torneo::with('organizador')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($torneo) {
                return [
                    'tipo' => 'torneo_creado',
                    'descripcion' => "Torneo creado: {$torneo->nombre}",
                    'fecha' => $torneo->created_at,
                    'icono' => 'trophy',
                    'color' => 'primary',
                ];
            });

        // Últimos pagos
        $ultimosPagos = PagoTorneo::with(['torneo', 'organizador'])
            ->where('estado', 'pagado')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($pago) {
                return [
                    'tipo' => 'pago_procesado',
                    'descripcion' => "Pago de \${$pago->monto} - {$pago->torneo->nombre}",
                    'fecha' => $pago->created_at,
                    'icono' => 'credit-card',
                    'color' => 'info',
                ];
            });

        $actividadReciente = $ultimosUsuarios
            ->concat($ultimosTorneos)
            ->concat($ultimosPagos)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();

        // Sugerencias pendientes
        $sugerenciasPendientes = Sugerencia::pendientes()->count();

        return view('admin.dashboard.index', compact(
            'totalOrganizadores',
            'organizadoresActivos',
            'totalJugadores',
            'torneosPorEstado',
            'totalTorneos',
            'torneosActivos',
            'ingresosMesActual',
            'ingresosTotales',
            'ingresosPorMes',
            'actividadReciente',
            'sugerenciasPendientes'
        ));
    }
}
