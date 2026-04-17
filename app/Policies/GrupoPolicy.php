<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Grupo;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrupoPolicy
{
    use HandlesAuthorization;

    const resource = 'grupos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Grupo $grupo)
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

    public function update(User $user, Grupo $grupo)
    {
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $grupo->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar grupos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Grupo $grupo)
    {
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $grupo->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar grupos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
