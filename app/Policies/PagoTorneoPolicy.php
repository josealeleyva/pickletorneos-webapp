<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PagoTorneo;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagoTorneoPolicy
{
    use HandlesAuthorization;

    const resource = 'pagos';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, PagoTorneo $pago)
    {
        if ($user->hasPermissionTo("Ver " . self::resource, 'web')) {
            return ($user->hasRole('Superadministrador') || $pago->organizador_id === $user->id)
                ? $this->allow()
                : $this->deny('Solo puede ver sus propios pagos');
        }
        return $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo("Crear " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para crear ' . self::resource);
    }

    public function update(User $user, PagoTorneo $pago)
    {
        return $user->hasPermissionTo("Actualizar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para actualizar ' . self::resource);
    }

    public function verificar(User $user, PagoTorneo $pago)
    {
        return $user->hasPermissionTo("Verificar " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para verificar ' . self::resource);
    }
}
