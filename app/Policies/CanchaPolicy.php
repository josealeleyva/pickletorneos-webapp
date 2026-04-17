<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cancha;
use Illuminate\Auth\Access\HandlesAuthorization;

class CanchaPolicy
{
    use HandlesAuthorization;

    const resource = 'canchas';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Cancha $cancha)
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

    public function update(User $user, Cancha $cancha)
    {
        // Organizador solo puede editar canchas de sus complejos
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $cancha->complejo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar canchas de sus propios complejos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Cancha $cancha)
    {
        // Organizador solo puede eliminar canchas de sus complejos
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $cancha->complejo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar canchas de sus propios complejos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
