<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deporte;
use App\Models\Torneo;
use Illuminate\Http\Request;

class TorneoController extends Controller
{
    public function index(Request $request)
    {
        $query = Torneo::with(['organizador', 'deporte', 'pago']);

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro de deporte
        if ($request->filled('deporte_id')) {
            $query->where('deporte_id', $request->deporte_id);
        }

        $torneos = $query->latest()->paginate(15);

        // Calcular estadísticas por estado
        $estadisticas = [
            'total' => Torneo::count(),
            'borrador' => Torneo::where('estado', 'borrador')->count(),
            'activo' => Torneo::where('estado', 'activo')->count(),
            'en_curso' => Torneo::where('estado', 'en_curso')->count(),
            'finalizado' => Torneo::where('estado', 'finalizado')->count(),
        ];

        return view('admin.torneos.index', compact('torneos', 'estadisticas'));
    }

    /**
     * Ver detalles de un torneo (vista admin)
     */
    public function show(Torneo $torneo)
    {
        // Cargar relaciones estándar
        $torneo->load([
            'organizador',
            'deporte',
            'complejo',
            'formato',
            'categorias',
            'pago',
            'equipos.jugadores',
            'grupos',
            'llaves',
        ]);

        // Obtener partidos usando el método del modelo (que retorna Query Builder)
        $totalPartidos = $torneo->partidos()->count();
        $partidosJugados = $torneo->partidos()->whereNotNull('sets_equipo1')->count();
        $partidosPendientes = $torneo->partidos()->whereNull('sets_equipo1')->count();

        // Estadísticas del torneo
        $stats = [
            'total_equipos' => $torneo->equipos()->count(),
            'total_partidos' => $totalPartidos,
            'partidos_jugados' => $partidosJugados,
            'partidos_pendientes' => $partidosPendientes,
        ];

        return view('admin.torneos.show', compact('torneo', 'stats'));
    }
}
