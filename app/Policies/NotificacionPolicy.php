<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificacionPolicy
{
    use HandlesAuthorization;

    const resource = 'notificaciones';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Notificacion $notificacion)
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

    public function enviar(User $user, Notificacion $notificacion)
    {
        if ($user->hasPermissionTo("Enviar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $notificacion->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede enviar notificaciones de sus propios torneos');
        }
        return $this->deny('No tiene permiso para enviar ' . self::resource);
    }

    public function delete(User $user, Notificacion $notificacion)
    {
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $notificacion->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar notificaciones de sus propios torneos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
