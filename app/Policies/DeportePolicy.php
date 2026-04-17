<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Deporte;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeportePolicy
{
    use HandlesAuthorization;

    const resource = 'deportes';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Deporte $deporte)
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

    public function update(User $user, Deporte $deporte)
    {
        return $user->hasPermissionTo("Actualizar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Deporte $deporte)
    {
        return $user->hasPermissionTo("Eliminar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
