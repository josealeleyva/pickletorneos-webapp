<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inscripcion;
use Illuminate\Auth\Access\HandlesAuthorization;

class InscripcionPolicy
{
    use HandlesAuthorization;

    const resource = 'inscripciones';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Inscripcion $inscripcion)
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

    public function update(User $user, Inscripcion $inscripcion)
    {
        return $user->hasPermissionTo("Actualizar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function delete(User $user, Inscripcion $inscripcion)
    {
        return $user->hasPermissionTo("Eliminar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para eliminar ' . self::resource);
    }

    public function aprobar(User $user, Inscripcion $inscripcion)
    {
        if ($user->hasPermissionTo("Aprobar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $inscripcion->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede aprobar inscripciones de sus propios torneos');
        }
        return $this->deny('No tiene permiso para aprobar ' . self::resource);
    }

    public function rechazar(User $user, Inscripcion $inscripcion)
    {
        if ($user->hasPermissionTo("Rechazar " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $inscripcion->torneo->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede rechazar inscripciones de sus propios torneos');
        }
        return $this->deny('No tiene permiso para rechazar ' . self::resource);
    }
}
