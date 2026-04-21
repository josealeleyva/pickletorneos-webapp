<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Llave;
use App\Models\Partido;
use App\Models\ResultadoTentativo;
use App\Models\Torneo;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        $proximosPartidos = collect();
        $torneosActivos = collect();
        $torneosHistorial = collect();

        if ($jugador) {
            $equipoIds = $jugador->equipos()->pluck('equipos.id');

            // Próximos partidos: con fecha futura, ordenados cronológicamente
            $proximosPartidos = Partido::where(function ($q) use ($equipoIds) {
                $q->whereIn('equipo1_id', $equipoIds)
                    ->orWhereIn('equipo2_id', $equipoIds);
            })
                ->whereNotNull('fecha_hora')
                ->whereNull('equipo_ganador_id')
                ->with(['equipo1', 'equipo2', 'cancha', 'equipo1.torneo', 'equipo2.torneo', 'resultadoTentativo'])
                ->orderBy('fecha_hora')
                ->limit(10)
                ->get();

            // Torneos activos donde el jugador tiene equipo inscripto
            $torneosActivos = Torneo::whereIn('id',
                \App\Models\Equipo::whereIn('id', $equipoIds)->pluck('torneo_id')
            )
                ->whereIn('estado', ['activo', 'en_curso'])
                ->with('deporte', 'complejo')
                ->get();

            // Torneos finalizados
            $torneosHistorial = Torneo::whereIn('id',
                \App\Models\Equipo::whereIn('id', $equipoIds)->pluck('torneo_id')
            )
                ->where('estado', 'finalizado')
                ->with('deporte', 'complejo')
                ->orderByDesc('updated_at')
                ->get();
        }

        return view('jugador.dashboard', compact(
            'jugador',
            'proximosPartidos',
            'torneosActivos',
            'torneosHistorial'
        ));
    }

    public function torneos()
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        $torneosActivos = collect();
        $torneosHistorial = collect();
        $torneosExplorar = collect();

        if ($jugador) {
            $equipoIds = $jugador->equipos()->pluck('equipos.id');
            $torneoIdsJugador = \App\Models\Equipo::whereIn('id', $equipoIds)->pluck('torneo_id');

            $torneosActivos = Torneo::whereIn('id', $torneoIdsJugador)
                ->whereIn('estado', ['activo', 'en_curso'])
                ->with('deporte', 'complejo')
                ->get();

            $torneosHistorial = Torneo::whereIn('id', $torneoIdsJugador)
                ->where('estado', 'finalizado')
                ->with('deporte', 'complejo')
                ->orderByDesc('updated_at')
                ->get();
        }

        // Calcular posición final para cada torneo del historial
        $posicionesFinal = collect();
        if ($jugador && $torneosHistorial->isNotEmpty()) {
            $equipoIds = $jugador->equipos()->pluck('equipos.id');
            foreach ($torneosHistorial as $torneo) {
                $posicionesFinal[$torneo->id] = $this->getPosicionFinal($torneo, $equipoIds);
            }
        }

        // Todos los torneos públicos activos para explorar
        $torneosExplorar = Torneo::whereIn('estado', ['activo', 'en_curso'])
            ->with('deporte', 'complejo', 'categorias')
            ->orderByDesc('created_at')
            ->get();

        return view('jugador.torneos', compact(
            'jugador',
            'torneosActivos',
            'torneosHistorial',
            'torneosExplorar',
            'posicionesFinal'
        ));
    }

    public function inscripciones()
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        $invitacionesPendientes = collect();
        $historialInvitaciones = collect();
        $inscripcionesPendientes = collect();
        $inscripcionesConfirmadas = collect();

        if ($jugador) {
            $invitacionesPendientes = InvitacionJugador::with([
                'inscripcionEquipo.torneo',
                'inscripcionEquipo.categoria',
                'inscripcionEquipo.lider',
            ])
                ->where('jugador_id', $jugador->id)
                ->where('estado', 'pendiente')
                ->get()
                ->filter(fn ($inv) => $inv->inscripcionEquipo !== null && $inv->inscripcionEquipo->torneo !== null);

            $historialInvitaciones = InvitacionJugador::with([
                'inscripcionEquipo.torneo',
                'inscripcionEquipo.categoria',
                'inscripcionEquipo.lider',
            ])
                ->where('jugador_id', $jugador->id)
                ->where('estado', '!=', 'pendiente')
                ->orderByDesc('respondido_at')
                ->limit(10)
                ->get()
                ->filter(fn ($inv) => $inv->inscripcionEquipo !== null && $inv->inscripcionEquipo->torneo !== null);

            $inscripcionesPendientes = InscripcionEquipo::with([
                'torneo',
                'categoria',
                'invitaciones.jugador',
            ])
                ->where('lider_jugador_id', $jugador->id)
                ->where('estado', 'pendiente')
                ->get();

            $inscripcionesConfirmadas = InscripcionEquipo::with([
                'torneo',
                'categoria',
                'equipo',
            ])
                ->where('lider_jugador_id', $jugador->id)
                ->where('estado', 'confirmada')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();
        }

        return view('jugador.inscripciones', compact(
            'jugador',
            'invitacionesPendientes',
            'historialInvitaciones',
            'inscripcionesPendientes',
            'inscripcionesConfirmadas'
        ));
    }

    public function partidos()
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        $pendientesConfirmacion = collect();
        $esperandoRival = collect();
        $sinResultado = collect();
        $equipoIds = collect();

        if ($jugador) {
            $equipoIds = $jugador->equipos()->pluck('equipos.id');

            $tentativos = ResultadoTentativo::whereHas('partido', function ($q) use ($equipoIds) {
                $q->where(function ($q2) use ($equipoIds) {
                    $q2->whereIn('equipo1_id', $equipoIds)
                        ->orWhereIn('equipo2_id', $equipoIds);
                });
            })->with([
                'partido.equipo1.torneo',
                'partido.equipo2',
                'partido.cancha',
                'propuestoPorEquipo',
                'equipoGanador',
            ])->get();

            $pendientesConfirmacion = $tentativos->filter(
                fn ($t) => ! $equipoIds->contains($t->propuesto_por_equipo_id)
            );

            $esperandoRival = $tentativos->filter(
                fn ($t) => $equipoIds->contains($t->propuesto_por_equipo_id)
            );

            $sinResultado = Partido::where(function ($q) use ($equipoIds) {
                $q->whereIn('equipo1_id', $equipoIds)
                    ->orWhereIn('equipo2_id', $equipoIds);
            })
                ->whereNotNull('fecha_hora')
                ->where('fecha_hora', '<', now())
                ->whereNull('equipo_ganador_id')
                ->whereDoesntHave('resultadoTentativo')
                ->with(['equipo1.torneo', 'equipo1.jugadores', 'equipo2', 'equipo2.jugadores', 'cancha'])
                ->orderByDesc('fecha_hora')
                ->get();
        }

        return view('jugador.partidos', compact(
            'jugador',
            'pendientesConfirmacion',
            'esperandoRival',
            'sinResultado',
            'equipoIds'
        ));
    }

    private function getPosicionFinal(Torneo $torneo, Collection $equipoIds): string
    {
        // Check bracket-based position first (elimination formats)
        $finalLlave = Llave::where('torneo_id', $torneo->id)
            ->where('ronda', 'Final')
            ->with('partido')
            ->first();

        if ($finalLlave && $finalLlave->partido) {
            $partido = $finalLlave->partido;
            $miEquipoEnFinal = $equipoIds->intersect(
                collect([$finalLlave->equipo1_id, $finalLlave->equipo2_id])->filter()
            )->isNotEmpty();

            if ($miEquipoEnFinal) {
                if ($partido->equipo_ganador_id && $equipoIds->contains($partido->equipo_ganador_id)) {
                    return 'Campeón';
                }

                return '2do puesto';
            }

            // Check semifinal
            $semifinalLlaves = Llave::where('torneo_id', $torneo->id)
                ->where('ronda', 'Semifinal')
                ->get();

            $miEquipoEnSemi = $semifinalLlaves->filter(function ($llave) use ($equipoIds) {
                return $equipoIds->intersect(
                    collect([$llave->equipo1_id, $llave->equipo2_id])->filter()
                )->isNotEmpty();
            })->isNotEmpty();

            if ($miEquipoEnSemi) {
                return 'Semifinalista';
            }
        }

        return 'Participante';
    }
}
