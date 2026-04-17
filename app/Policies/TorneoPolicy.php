<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Torneo;
use Illuminate\Auth\Access\HandlesAuthorization;

class TorneoPolicy
{
    use HandlesAuthorization;

    const resource = 'torneos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Torneo $torneo)
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

    public function update(User $user, Torneo $torneo)
    {
        // Organizador solo puede editar sus propios torneos
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar sus propios torneos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Torneo $torneo)
    {
        // Organizador solo puede eliminar sus propios torneos
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar sus propios torneos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }

    public function publicar(User $user, Torneo $torneo)
    {
        if ($user->hasPermissionTo("Publicar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede publicar sus propios torneos');
        }
        return $this->deny('No tiene permiso para publicar ' . self::resource);
    }

    public function finalizar(User $user, Torneo $torneo)
    {
        if ($user->hasPermissionTo("Finalizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede finalizar sus propios torneos');
        }
        return $this->deny('No tiene permiso para finalizar ' . self::resource);
    }

    public function cancelar(User $user, Torneo $torneo)
    {
        if ($user->hasPermissionTo("Cancelar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede cancelar sus propios torneos');
        }
        return $this->deny('No tiene permiso para cancelar ' . self::resource);
    }

    public function cargarResultado(User $user, Torneo $torneo)
    {
        if ($user->hasPermissionTo("Cargar resultado " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede cargar resultados en sus propios torneos');
        }
        return $this->deny('No tiene permiso para cargar resultados en ' . self::resource);
    }
}
