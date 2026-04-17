<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jugador;
use Illuminate\Auth\Access\HandlesAuthorization;

class JugadorPolicy
{
    use HandlesAuthorization;

    const resource = 'jugadores';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Jugador $jugador)
    {
        if (!$user->hasPermissionTo("Ver " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para ver ' . self::resource);
        }

        // Solo puede ver sus propios jugadores
        return $jugador->organizador_id === $user->id
            ? $this->allow()
            : $this->deny('No puede ver jugadores de otros organizadores');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo("Crear " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para crear ' . self::resource);
    }

    public function update(User $user, Jugador $jugador)
    {
        if (!$user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para actualizar ' . self::resource);
        }

        // Solo puede actualizar sus propios jugadores
        return $jugador->organizador_id === $user->id
            ? $this->allow()
            : $this->deny('No puede actualizar jugadores de otros organizadores');
    }

    public function delete(User $user, Jugador $jugador)
    {
        if (!$user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para eliminar ' . self::resource);
        }

        // Solo puede eliminar sus propios jugadores
        if ($jugador->organizador_id !== $user->id) {
            return $this->deny('No puede eliminar jugadores de otros organizadores');
        }

        // Verificar que no esté inscrito en ningún torneo
        $tieneInscripciones = $jugador->inscripciones()->exists();

        if ($tieneInscripciones) {
            return $this->deny('No puede eliminar un jugador que está inscrito en torneos');
        }

        // Verificar que no esté en ningún equipo
        $tieneEquipos = $jugador->equipos()->exists();

        if ($tieneEquipos) {
            return $this->deny('No puede eliminar un jugador que pertenece a equipos');
        }

        return $this->allow();
    }
}
