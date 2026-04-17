<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    const resource = 'categorias';

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo("Ver " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para ver ' . self::resource);
    }

    public function view(User $user, Categoria $categoria)
    {
        if (!$user->hasPermissionTo("Ver " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para ver ' . self::resource);
        }

        // Solo puede ver sus propias categorías
        return $categoria->organizador_id === $user->id
            ? $this->allow()
            : $this->deny('No puede ver categorías de otros organizadores');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo("Crear " . self::resource, 'web')
            ? $this->allow()
            : $this->deny('No tiene permiso para crear ' . self::resource);
    }

    public function update(User $user, Categoria $categoria)
    {
        if (!$user->hasPermissionTo("Actualizar " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para actualizar ' . self::resource);
        }

        // Solo puede actualizar sus propias categorías
        return $categoria->organizador_id === $user->id
            ? $this->allow()
            : $this->deny('No puede actualizar categorías de otros organizadores');
    }

    public function delete(User $user, Categoria $categoria)
    {
        if (!$user->hasPermissionTo("Eliminar " . self::resource, 'web')) {
            return $this->deny('No tiene permiso para eliminar ' . self::resource);
        }

        // Solo puede eliminar sus propias categorías
        if ($categoria->organizador_id !== $user->id) {
            return $this->deny('No puede eliminar categorías de otros organizadores');
        }

        // Verificar que no esté en uso en torneos activos
        $enUsoEnTorneos = $categoria->torneos()
            ->whereIn('estado', ['borrador', 'activo'])
            ->exists();

        return !$enUsoEnTorneos
            ? $this->allow()
            : $this->deny('No puede eliminar una categoría que está siendo usada en torneos activos');
    }
}
