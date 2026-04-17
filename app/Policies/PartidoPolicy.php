<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Partido;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartidoPolicy
{
    use HandlesAuthorization;

    const resource = 'partidos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Partido $partido)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo("Crear " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para crear ' . self::resource);
    }

    public function update(User $user, Partido $partido)
    {
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            $organizadorId = $partido->grupo
                ? $partido->grupo->torneo->organizador_id
                : $partido->llave->torneo->organizador_id;

            return ($user->hasRole('Superadministrador') || $organizadorId === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar partidos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Partido $partido)
    {
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            $organizadorId = $partido->grupo
                ? $partido->grupo->torneo->organizador_id
                : $partido->llave->torneo->organizador_id;

            return ($user->hasRole('Superadministrador') || $organizadorId === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar partidos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
