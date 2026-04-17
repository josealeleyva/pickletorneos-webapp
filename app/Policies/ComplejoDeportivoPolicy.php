<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ComplejoDeportivo;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplejoDeportivoPolicy
{
    use HandlesAuthorization;

    const resource = 'complejos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, ComplejoDeportivo $complejo)
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

    public function update(User $user, ComplejoDeportivo $complejo)
    {
        // Organizador solo puede editar sus propios complejos
        if ($user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $complejo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede actualizar sus propios complejos');
        }
        return $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, ComplejoDeportivo $complejo)
    {
        // Organizador solo puede eliminar sus propios complejos
        if ($user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $complejo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede eliminar sus propios complejos');
        }
        return $this->deny('No tiene permiso para eliminar ' . self::resource);
    }
}
