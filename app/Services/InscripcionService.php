<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Equipo;
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\Notificacion;
use App\Models\TamanioGrupo;
use App\Models\Torneo;
use App\Models\User;
use App\Notifications\InscripcionCanceladaNotification;
use App\Notifications\InscripcionConfirmadaNotification;
use App\Notifications\InvitacionTorneoNotification;
use App\Notifications\NuevoEquipoInscriptoNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InscripcionService
{
    public function iniciarInscripcion(Jugador $lider, Torneo $torneo, Categoria $categoria): InscripcionEquipo
    {
        if ($torneo->estado !== 'activo') {
            throw new \RuntimeException('Solo puedes inscribirte en torneos activos.');
        }

        $categoriaConPivot = $torneo->categorias()->where('categorias.id', $categoria->id)->first();

        if (! $categoriaConPivot) {
            throw new \RuntimeException('La categoría no pertenece a este torneo.');
        }

        $yaParticipa = InscripcionEquipo::where('torneo_id', $torneo->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where(function ($q) use ($lider) {
                $q->where('lider_jugador_id', $lider->id)
                    ->orWhereHas('invitaciones', function ($q2) use ($lider) {
                        $q2->where('jugador_id', $lider->id)->where('estado', 'aceptada');
                    });
            })
            ->exists();

        if ($yaParticipa) {
            throw new \RuntimeException('Ya estás inscripto en este torneo.');
        }

        $this->validarCondicionesJugador($lider, $categoriaConPivot, $torneo);

        $cuposDisponibles = $this->calcularCuposDisponibles($torneo, $categoriaConPivot);

        if ($cuposDisponibles <= 0) {
            throw new \RuntimeException('No hay cupos disponibles en esta categoría.');
        }

        return DB::transaction(function () use ($lider, $torneo, $categoriaConPivot) {
            $inscripcion = InscripcionEquipo::create([
                'torneo_id' => $torneo->id,
                'categoria_id' => $categoriaConPivot->id,
                'lider_jugador_id' => $lider->id,
                'estado' => 'pendiente',
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            InvitacionJugador::create([
                'inscripcion_equipo_id' => $inscripcion->id,
                'jugador_id' => $lider->id,
                'estado' => 'aceptada',
                'auto_aceptada' => false,
                'token' => Str::random(40),
                'respondido_at' => Carbon::now(),
            ]);

            return $inscripcion;
        });
    }

    public function buscarJugadoresElegibles(Torneo $torneo, Categoria $categoria, string $query): Collection
    {
        $categoriaConPivot = $torneo->categorias()->where('categorias.id', $categoria->id)->first();

        if (! $categoriaConPivot) {
            return collect();
        }

        $jugadoresYaEnEquipo = DB::table('equipo_jugador')
            ->join('equipos', 'equipo_jugador.equipo_id', '=', 'equipos.id')
            ->where('equipos.torneo_id', $torneo->id)
            ->where('equipos.categoria_id', $categoria->id)
            ->whereNull('equipos.deleted_at')
            ->pluck('equipo_jugador.jugador_id')
            ->toArray();

        $jugadoresInvitados = DB::table('invitaciones_jugador')
            ->join('inscripciones_equipo', 'invitaciones_jugador.inscripcion_equipo_id', '=', 'inscripciones_equipo.id')
            ->where('inscripciones_equipo.torneo_id', $torneo->id)
            ->where('inscripciones_equipo.categoria_id', $categoria->id)
            ->where('inscripciones_equipo.estado', 'pendiente')
            ->whereNull('inscripciones_equipo.deleted_at')
            ->pluck('invitaciones_jugador.jugador_id')
            ->toArray();

        $excluidos = array_unique(array_merge($jugadoresYaEnEquipo, $jugadoresInvitados));

        $jugadores = Jugador::whereNotNull('user_id')
            ->whereNotIn('id', $excluidos)
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                    ->orWhere('apellido', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('telefono', 'like', "%{$query}%");
            })
            ->get();

        return $jugadores->filter(function (Jugador $jugador) use ($categoriaConPivot, $torneo) {
            return $this->jugadorCumpleCondiciones($jugador, $categoriaConPivot, $torneo);
        })->values();
    }

    public function validarCondicionesJugador(Jugador $jugador, Categoria $categoriaConPivot, ?Torneo $torneo = null): void
    {
        $generoPermitido = $categoriaConPivot->pivot->genero_permitido ?? null;

        if ($generoPermitido && $generoPermitido !== 'mixto' && $jugador->genero !== $generoPermitido) {
            throw new \RuntimeException('Tu género no cumple con los requisitos de esta categoría.');
        }

        $edadMinima = $categoriaConPivot->pivot->edad_minima ?? null;
        $edadMaxima = $categoriaConPivot->pivot->edad_maxima ?? null;

        if ($edadMinima || $edadMaxima) {
            if (! $jugador->fecha_nacimiento) {
                throw new \RuntimeException('Completá tu fecha de nacimiento en el perfil para inscribirte en esta categoría.');
            }

            $edad = $jugador->fecha_nacimiento->age;

            if ($edadMinima && $edad < $edadMinima) {
                throw new \RuntimeException("Debés tener al menos {$edadMinima} años para esta categoría.");
            }

            if ($edadMaxima && $edad > $edadMaxima) {
                throw new \RuntimeException("Debés tener como máximo {$edadMaxima} años para esta categoría.");
            }
        }

        if ($torneo?->dupr_requerido && ! $jugador->dupr_id) {
            throw new \RuntimeException('Este torneo requiere vincular tu cuenta DUPR. Podés hacerlo desde tu perfil.');
        }

        $ratingMin = $categoriaConPivot->pivot->dupr_rating_min ?? null;
        $ratingMax = $categoriaConPivot->pivot->dupr_rating_max ?? null;

        if ($ratingMin || $ratingMax) {
            $rating = $jugador->rating_doubles;

            if ($rating === null) {
                throw new \RuntimeException('Tu cuenta DUPR no tiene rating registrado aún.');
            }

            if ($ratingMin && $rating < $ratingMin) {
                throw new \RuntimeException("Tu rating DUPR ({$rating}) es menor al mínimo requerido ({$ratingMin}).");
            }

            if ($ratingMax && $rating > $ratingMax) {
                throw new \RuntimeException("Tu rating DUPR ({$rating}) supera el máximo permitido ({$ratingMax}).");
            }
        }
    }

    public function calcularCuposDisponibles(Torneo $torneo, Categoria $categoriaConPivot): int
    {
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            $numeroGrupos = $categoriaConPivot->pivot->numero_grupos ?? 0;
            $tamanioGrupoId = $categoriaConPivot->pivot->tamanio_grupo_id ?? null;
            $tamanioGrupo = $tamanioGrupoId ? TamanioGrupo::find($tamanioGrupoId) : null;
            $cuposCategoria = $numeroGrupos * ($tamanioGrupo ? $tamanioGrupo->tamanio : 0);
        } else {
            $cuposCategoria = $categoriaConPivot->pivot->cupos_categoria ?? 0;
        }

        $equiposConfirmados = $torneo->equipos()
            ->where('categoria_id', $categoriaConPivot->id)
            ->count();

        return max(0, $cuposCategoria - $equiposConfirmados);
    }

    public function enviarInvitacion(InscripcionEquipo $inscripcion, Jugador $jugador): InvitacionJugador
    {
        if ($inscripcion->estado !== 'pendiente') {
            throw new \RuntimeException('La inscripción ya no está activa.');
        }

        if ($inscripcion->invitaciones()->where('jugador_id', $jugador->id)->exists()) {
            throw new \RuntimeException('Este jugador ya fue invitado.');
        }

        $invitacion = InvitacionJugador::create([
            'inscripcion_equipo_id' => $inscripcion->id,
            'jugador_id' => $jugador->id,
            'estado' => 'pendiente',
            'auto_aceptada' => false,
            'token' => Str::random(40),
        ]);

        if ($this->debeAutoAceptar($invitacion)) {
            $invitacion->update([
                'estado' => 'aceptada',
                'auto_aceptada' => true,
                'respondido_at' => Carbon::now(),
            ]);

            $this->verificarYConfirmar($inscripcion->fresh());
        } else {
            $this->notificarInvitacion($invitacion);
        }

        return $invitacion->fresh();
    }

    public function debeAutoAceptar(InvitacionJugador $invitacion): bool
    {
        $jugador = $invitacion->jugador;

        if (! $jugador->auto_aceptar_invitaciones) {
            return false;
        }

        $lider = $invitacion->inscripcionEquipo->lider;

        return DB::table('equipo_jugador as ej1')
            ->join('equipo_jugador as ej2', 'ej1.equipo_id', '=', 'ej2.equipo_id')
            ->join('equipos', 'equipos.id', '=', 'ej1.equipo_id')
            ->where('ej1.jugador_id', $lider->id)
            ->where('ej2.jugador_id', $jugador->id)
            ->whereNull('equipos.deleted_at')
            ->exists();
    }

    public function responderInvitacion(InvitacionJugador $invitacion, bool $aceptar): void
    {
        if ($invitacion->estado !== 'pendiente') {
            throw new \RuntimeException('Esta invitación ya fue respondida.');
        }

        $invitacion->update([
            'estado' => $aceptar ? 'aceptada' : 'rechazada',
            'respondido_at' => Carbon::now(),
        ]);

        if (! $aceptar) {
            $this->cancelarInscripcion($invitacion->inscripcionEquipo, 'jugador');
        } else {
            $this->verificarYConfirmar($invitacion->inscripcionEquipo->fresh());
        }
    }

    public function verificarYConfirmar(InscripcionEquipo $inscripcion): void
    {
        if ($inscripcion->estado !== 'pendiente') {
            return;
        }

        if (! $inscripcion->todasAceptadas()) {
            return;
        }

        DB::transaction(function () use ($inscripcion) {
            $torneo = $inscripcion->torneo;
            $jugadores = $inscripcion->invitaciones()->with('jugador.user')->get()->pluck('jugador');

            $nombreEquipo = $inscripcion->nombre_equipo
                ?? $jugadores->pluck('apellido')->join(' / ');

            $equipo = Equipo::create([
                'nombre' => $nombreEquipo,
                'torneo_id' => $torneo->id,
                'categoria_id' => $inscripcion->categoria_id,
            ]);

            foreach ($jugadores as $index => $jugador) {
                $equipo->jugadores()->attach($jugador->id, ['orden' => $index + 1]);
            }

            $inscripcion->update([
                'estado' => 'confirmada',
                'equipo_id' => $equipo->id,
            ]);

            $this->notificarInscripcionConfirmada($inscripcion, $jugadores);
        });
    }

    public function cancelarInscripcion(InscripcionEquipo $inscripcion, string $canceladoPor): void
    {
        if ($inscripcion->estado === 'cancelada') {
            return;
        }

        $jugadores = $inscripcion->invitaciones()->with('jugador.user')->get()->pluck('jugador');

        $inscripcion->update([
            'estado' => 'cancelada',
            'cancelado_por' => $canceladoPor,
        ]);

        $this->notificarInscripcionCancelada($inscripcion, $jugadores);
    }

    private function notificarInvitacion(InvitacionJugador $invitacion): void
    {
        $jugador = $invitacion->jugador;
        $inscripcion = $invitacion->inscripcionEquipo;

        if ($jugador->user) {
            $jugador->user->notify(new InvitacionTorneoNotification($invitacion));

            $this->crearNotificacionInApp(
                $inscripcion->torneo_id,
                $jugador->user,
                "¡Te invitaron a inscribirte en el torneo {$inscripcion->torneo->nombre}!",
                'invitacion_torneo'
            );
        }
    }

    private function notificarInscripcionConfirmada(InscripcionEquipo $inscripcion, Collection $jugadores): void
    {
        foreach ($jugadores as $jugador) {
            if ($jugador->user) {
                $jugador->user->notify(new InscripcionConfirmadaNotification($inscripcion));

                $this->crearNotificacionInApp(
                    $inscripcion->torneo_id,
                    $jugador->user,
                    "¡Tu equipo quedó inscripto en el torneo {$inscripcion->torneo->nombre}!",
                    'inscripcion_confirmada'
                );
            }
        }

        $organizador = $inscripcion->torneo->organizador;

        if ($organizador) {
            $organizador->notify(new NuevoEquipoInscriptoNotification($inscripcion));

            $this->crearNotificacionInApp(
                $inscripcion->torneo_id,
                $organizador,
                "Nuevo equipo inscripto en {$inscripcion->torneo->nombre}: {$inscripcion->equipo->nombre}",
                'nuevo_equipo_inscripto'
            );
        }
    }

    private function notificarInscripcionCancelada(InscripcionEquipo $inscripcion, Collection $jugadores): void
    {
        foreach ($jugadores as $jugador) {
            if ($jugador->user) {
                $jugador->user->notify(new InscripcionCanceladaNotification($inscripcion));

                $this->crearNotificacionInApp(
                    $inscripcion->torneo_id,
                    $jugador->user,
                    "La inscripción al torneo {$inscripcion->torneo->nombre} fue cancelada.",
                    'inscripcion_cancelada'
                );
            }
        }
    }

    private function crearNotificacionInApp(int $torneoId, User $user, string $mensaje, string $tipo): void
    {
        $notificacion = Notificacion::create([
            'torneo_id' => $torneoId,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'enviado_at' => Carbon::now(),
        ]);

        $notificacion->usuarios()->attach($user->id, ['leida' => false]);
    }

    private function jugadorCumpleCondiciones(Jugador $jugador, Categoria $categoriaConPivot, ?Torneo $torneo = null): bool
    {
        $generoPermitido = $categoriaConPivot->pivot->genero_permitido ?? null;

        if ($generoPermitido && $generoPermitido !== 'mixto' && $jugador->genero !== $generoPermitido) {
            return false;
        }

        $edadMinima = $categoriaConPivot->pivot->edad_minima ?? null;
        $edadMaxima = $categoriaConPivot->pivot->edad_maxima ?? null;

        if ($edadMinima || $edadMaxima) {
            if (! $jugador->fecha_nacimiento) {
                return false;
            }

            $edad = $jugador->fecha_nacimiento->age;

            if ($edadMinima && $edad < $edadMinima) {
                return false;
            }

            if ($edadMaxima && $edad > $edadMaxima) {
                return false;
            }
        }

        if ($torneo?->dupr_requerido && ! $jugador->dupr_id) {
            return false;
        }

        $ratingMin = $categoriaConPivot->pivot->dupr_rating_min ?? null;
        $ratingMax = $categoriaConPivot->pivot->dupr_rating_max ?? null;

        if ($ratingMin || $ratingMax) {
            $rating = $jugador->rating_doubles;

            if ($rating === null) {
                return false;
            }

            if ($ratingMin && $rating < $ratingMin) {
                return false;
            }

            if ($ratingMax && $rating > $ratingMax) {
                return false;
            }
        }

        return true;
    }
}
