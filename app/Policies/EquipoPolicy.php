<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipo;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipoPolicy
{
    use HandlesAuthorization;

    const resource = 'equipos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Equipo $equipo)
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

    public function update(User $user, Equipo $equipo)
    {
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $equipo->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar equipos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Equipo $equipo)
    {
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $equipo->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar equipos de sus propios torneos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
