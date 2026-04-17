<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Deporte;
use App\Models\Categoria;
use App\Models\Jugador;
use App\Models\ComplejoDeportivo;
use App\Models\Cancha;
use App\Models\Torneo;
use App\Models\Inscripcion;
use App\Models\Grupo;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Notificacion;
use App\Models\PagoTorneo;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\DeportePolicy;
use App\Policies\CategoriaPolicy;
use App\Policies\JugadorPolicy;
use App\Policies\ComplejoDeportivoPolicy;
use App\Policies\CanchaPolicy;
use App\Policies\TorneoPolicy;
use App\Policies\InscripcionPolicy;
use App\Policies\GrupoPolicy;
use App\Policies\EquipoPolicy;
use App\Policies\PartidoPolicy;
use App\Policies\NotificacionPolicy;
use App\Policies\PagoTorneoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Políticas del sistema base
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,

        // Políticas de Punto de Oro
        Deporte::class => DeportePolicy::class,
        Categoria::class => CategoriaPolicy::class,
        Jugador::class => JugadorPolicy::class,
        ComplejoDeportivo::class => ComplejoDeportivoPolicy::class,
        Cancha::class => CanchaPolicy::class,
        Torneo::class => TorneoPolicy::class,
        Inscripcion::class => InscripcionPolicy::class,
        Grupo::class => GrupoPolicy::class,
        Equipo::class => EquipoPolicy::class,
        Partido::class => PartidoPolicy::class,
        Notificacion::class => NotificacionPolicy::class,
        PagoTorneo::class => PagoTorneoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}