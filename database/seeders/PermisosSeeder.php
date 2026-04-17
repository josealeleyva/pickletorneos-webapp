<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superadmin = Role::findByName('Superadministrador');

        // Definir todos los recursos y sus acciones
        $recursos = [
            'users' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Actualizar rol',
                'Eliminar',
                'Activar',
                'Desactivar',
            ],
            'roles' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'permisos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar',
                'Asignar',
                'Quitar'
            ],
            'jugadores' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'deportes' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'categorias' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'complejos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'canchas' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'formatos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'tamaños grupos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'avances grupos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'torneos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar',
                'Publicar',
                'Finalizar',
                'Cancelar',
                'Cargar resultado',
            ],
            'inscripciones' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar',
                'Aprobar',
                'Rechazar',
            ],
            'grupos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'equipos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'llaves' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'partidos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'juegos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Eliminar'
            ],
            'notificaciones' => [
                'Ver',
                'Crear',
                'Enviar',
                'Eliminar'
            ],
            'pagos' => [
                'Ver',
                'Crear',
                'Actualizar',
                'Verificar',
            ],
            'actividades' => [
                'Ver',
                'Crear',
            ],
        ];

        // Crear todos los permisos y asignarlos al Superadministrador
        foreach ($recursos as $recurso => $acciones) {
            $permisos = collect();

            foreach ($acciones as $accion) {
                $nombre = "{$accion} {$recurso}";
                if (!Permission::where('name', $nombre)->exists()) {
                    $permisos->push(Permission::create([
                        'name' => $nombre,
                        'group' => $recurso,
                    ]));
                } else {
                    $permisos->push(Permission::where('name', $nombre)->first());
                }
            }

            $superadmin->givePermissionTo($permisos);
        }

        // ========================================
        // PERMISOS PARA ORGANIZADOR
        // ========================================
        $organizador = Role::findByName('Organizador');
        $permisosOrganizador = [
            // Gestión de torneos (sus propios torneos)
            'Ver torneos',
            'Crear torneos',
            'Actualizar torneos',
            'Eliminar torneos',
            'Publicar torneos',
            'Finalizar torneos',
            'Cancelar torneos',
            'Cargar resultado torneos',

            // Gestión de jugadores
            'Ver jugadores',
            'Crear jugadores',
            'Actualizar jugadores',
            'Eliminar jugadores',

            // Gestión de complejos y canchas
            'Ver complejos',
            'Crear complejos',
            'Actualizar complejos',
            'Eliminar complejos',

            'Ver canchas',
            'Crear canchas',
            'Actualizar canchas',
            'Eliminar canchas',

            // Gestión de grupos, equipos, llaves y partidos
            'Ver grupos',
            'Crear grupos',
            'Actualizar grupos',
            'Eliminar grupos',

            'Ver equipos',
            'Crear equipos',
            'Actualizar equipos',
            'Eliminar equipos',

            'Ver llaves',
            'Crear llaves',
            'Actualizar llaves',
            'Eliminar llaves',

            'Ver partidos',
            'Crear partidos',
            'Actualizar partidos',
            'Eliminar partidos',

            'Ver juegos',
            'Crear juegos',
            'Actualizar juegos',
            'Eliminar juegos',

            // Inscripciones
            'Ver inscripciones',
            'Crear inscripciones',
            'Aprobar inscripciones',
            'Rechazar inscripciones',

            // Notificaciones
            'Ver notificaciones',
            'Crear notificaciones',
            'Enviar notificaciones',

            // Ver datos de referencia (solo lectura)
            'Ver deportes',
            'Ver formatos',
            'Ver tamaños grupos',
            'Ver avances grupos',

            // Gestión de categorías propias
            'Ver categorias',
            'Crear categorias',
            'Actualizar categorias',
            'Eliminar categorias',

            // Pagos y actividades
            'Ver pagos',
            'Ver actividades',
            'Crear actividades',
        ];

        foreach ($permisosOrganizador as $nombrePermiso) {
            $permiso = Permission::where('name', $nombrePermiso)->first();
            if ($permiso) {
                $organizador->givePermissionTo($permiso);
            }
        }

        // ========================================
        // PERMISOS PARA JUGADOR
        // ========================================
        $jugador = Role::findByName('Jugador');
        $permisosJugador = [
            // Ver información de torneos en los que participa
            'Ver torneos',
            'Ver grupos',
            'Ver equipos',
            'Ver llaves',
            'Ver partidos',
            'Ver juegos',
            'Ver inscripciones',

            // Notificaciones
            'Ver notificaciones',

            // Ver datos de referencia
            'Ver deportes',
            'Ver categorias',
            'Ver complejos',
            'Ver canchas',
        ];

        foreach ($permisosJugador as $nombrePermiso) {
            $permiso = Permission::where('name', $nombrePermiso)->first();
            if ($permiso) {
                $jugador->givePermissionTo($permiso);
            }
        }
    }
}
