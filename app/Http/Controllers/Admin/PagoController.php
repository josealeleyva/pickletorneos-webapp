<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditoReferido;
use App\Models\PagoTorneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = PagoTorneo::with(['torneo', 'organizador']);

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('torneo', function ($tq) use ($buscar) {
                    $tq->where('nombre', 'like', "%{$buscar}%");
                })
                    ->orWhereHas('organizador', function ($oq) use ($buscar) {
                        $oq->where('name', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%");
                    });
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro de mes
        if ($request->filled('mes')) {
            $mes = explode('-', $request->mes);
            if (count($mes) === 2) {
                $query->whereYear('created_at', $mes[0])
                    ->whereMonth('created_at', $mes[1]);
            }
        }

        $pagos = $query->latest()->paginate(15);

        // Métricas
        $totalMesActual = PagoTorneo::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('estado', 'pagado')
            ->sum('monto');

        $totalHistorico = PagoTorneo::where('estado', 'pagado')->sum('monto');

        $torneosGratuitos = PagoTorneo::whereIn('estado', ['gratuito'])->count();
        $torneosPagos = PagoTorneo::where('estado', 'pagado')->count();

        $creditosUsadosMes = CreditoReferido::whereMonth('fecha_uso', now()->month)
            ->whereYear('fecha_uso', now()->year)
            ->where('estado', 'usado')
            ->count();

        // Resumen por método de pago
        $resumenMetodosPago = PagoTorneo::select('metodo_pago', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(monto) as total'))
            ->where('estado', 'pagado')
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get();

        return view('admin.pagos.index', compact(
            'pagos',
            'totalMesActual',
            'totalHistorico',
            'torneosGratuitos',
            'torneosPagos',
            'creditosUsadosMes',
            'resumenMetodosPago'
        ));
    }
}
