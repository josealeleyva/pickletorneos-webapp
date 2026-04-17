<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipoPlantilla;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipoPlantillaController extends Controller
{
    /**
     * Autocomplete de equipos plantilla para el organizador
     * Solo se usa para deportes que requieren nombre de equipo (ej: Fútbol)
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
            'deporte_id' => 'required|exists:deportes,id',
        ]);

        $equipos = EquipoPlantilla::where('organizador_id', Auth::id())
            ->where('deporte_id', $request->deporte_id)
            ->where('nombre', 'LIKE', $request->q . '%')
            ->orderBy('veces_usado', 'desc')
            ->orderBy('ultimo_uso', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'jugadores' => $equipo->ultima_formacion ?? [],
                    'veces_usado' => $equipo->veces_usado,
                    'ultimo_uso' => $equipo->ultimo_uso ? $equipo->ultimo_uso->format('d/m/Y') : null,
                ];
            });

        return response()->json($equipos);
    }

    /**
     * Obtener detalles de jugadores de una plantilla
     * Para precargar en el Select2
     */
    public function jugadores(Request $request)
    {
        $request->validate([
            'plantilla_id' => 'required|exists:equipo_plantillas,id',
        ]);

        $plantilla = EquipoPlantilla::where('id', $request->plantilla_id)
            ->where('organizador_id', Auth::id())
            ->firstOrFail();

        if (empty($plantilla->ultima_formacion)) {
            return response()->json([]);
        }

        $jugadores = Jugador::whereIn('id', $plantilla->ultima_formacion)
            ->get()
            ->map(function ($jugador) {
                return [
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'apellido' => $jugador->apellido,
                    'dni' => $jugador->dni,
                    'nombre_completo' => $jugador->nombre_completo,
                ];
            });

        return response()->json($jugadores);
    }
}
