# Sistema de Inscripción de Jugadores a Torneos - Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permitir a jugadores registrados inscribirse autónomamente a torneos, formando equipos e invitando compañeros que deben aceptar antes de que el equipo quede confirmado.

**Architecture:** Se crean dos nuevas tablas (`inscripciones_equipo` e `invitaciones_jugador`) que gestionan el flujo de inscripción con estados bien definidos. Un servicio central (`InscripcionService`) encapsula toda la lógica de negocio. La vista pública del torneo expone el botón de inscripción; las notificaciones llegan por email e in-app.

**Tech Stack:** Laravel 10, PHP 8.3, MySQL, Laravel Notifications (mail + in-app vía modelo Notificacion), queued jobs para expiración, Tailwind CSS responsive.

---

## Mapa de archivos

### Nuevos
- `database/migrations/..._create_inscripciones_equipo_table.php`
- `database/migrations/..._create_invitaciones_jugador_table.php`
- `database/migrations/..._add_auto_aceptar_to_jugadores_table.php`
- `app/Models/InscripcionEquipo.php`
- `app/Models/InvitacionJugador.php`
- `app/Services/InscripcionService.php`
- `app/Http/Controllers/InscripcionController.php`
- `app/Http/Controllers/InvitacionController.php`
- `app/Notifications/InvitacionTorneoNotification.php`
- `app/Notifications/InscripcionConfirmadaNotification.php`
- `app/Notifications/InscripcionCanceladaNotification.php`
- `app/Notifications/NuevoEquipoInscriptoNotification.php`
- `app/Jobs/ProcesarInscripcionesExpiradas.php`
- `resources/views/inscripciones/crear.blade.php`
- `resources/views/inscripciones/invitacion.blade.php`
- `tests/Feature/InscripcionServiceTest.php`
- `tests/Feature/InscripcionControllerTest.php`
- `tests/Feature/InvitacionControllerTest.php`

### Modificados
- `app/Models/Jugador.php` — agregar `auto_aceptar_invitaciones`
- `app/Models/Torneo.php` — agregar relación `inscripcionesEquipo()`
- `routes/web.php` — registrar nuevas rutas
- `app/Console/Kernel.php` — schedule del job de expiración
- `resources/views/torneos/public.blade.php` — botón de inscribirse
- `resources/views/jugador/perfil.blade.php` — toggle auto-aceptar
- `app/Http/Controllers/Jugador/PerfilController.php` — guardar auto_aceptar
- `app/Http/Controllers/TorneoEquipoController.php` — notificar jugadores al eliminar equipo

---

## Task 1: Migraciones

**Files:**
- Create: `database/migrations/2026_04_04_100000_create_inscripciones_equipo_table.php`
- Create: `database/migrations/2026_04_04_100001_create_invitaciones_jugador_table.php`
- Create: `database/migrations/2026_04_04_100002_add_auto_aceptar_to_jugadores_table.php`

- [ ] **Step 1: Crear migración `inscripciones_equipo`**

```bash
php artisan make:migration create_inscripciones_equipo_table --no-interaction
```

Editar el archivo generado:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('lider_jugador_id')->constrained('jugadores');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->dateTime('expires_at')->nullable();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->enum('cancelado_por', ['organizador', 'jugador', 'expiracion'])->nullable();
            $table->string('nombre_equipo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_equipo');
    }
};
```

- [ ] **Step 2: Crear migración `invitaciones_jugador`**

```bash
php artisan make:migration create_invitaciones_jugador_table --no-interaction
```

Editar:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitaciones_jugador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_equipo_id')->constrained('inscripciones_equipo')->cascadeOnDelete();
            $table->foreignId('jugador_id')->constrained('jugadores');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->boolean('auto_aceptada')->default(false);
            $table->string('token')->unique();
            $table->dateTime('respondido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitaciones_jugador');
    }
};
```

- [ ] **Step 3: Crear migración `add_auto_aceptar_to_jugadores`**

```bash
php artisan make:migration add_auto_aceptar_to_jugadores_table --no-interaction
```

Editar:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->boolean('auto_aceptar_invitaciones')->default(false)->after('organizador_id');
        });
    }

    public function down(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('auto_aceptar_invitaciones');
        });
    }
};
```

- [ ] **Step 4: Ejecutar migraciones**

```bash
php artisan migrate --no-interaction
```

Esperado: 3 nuevas tablas creadas sin errores.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/
git commit -m "feat: migraciones inscripciones_equipo, invitaciones_jugador y auto_aceptar jugadores"
```

---

## Task 2: Modelos InscripcionEquipo e InvitacionJugador

**Files:**
- Create: `app/Models/InscripcionEquipo.php`
- Create: `app/Models/InvitacionJugador.php`
- Modify: `app/Models/Jugador.php`
- Modify: `app/Models/Torneo.php`

- [ ] **Step 1: Crear modelo InscripcionEquipo**

```bash
php artisan make:model InscripcionEquipo --no-interaction
```

Reemplazar el contenido de `app/Models/InscripcionEquipo.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionEquipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscripciones_equipo';

    protected $fillable = [
        'torneo_id',
        'categoria_id',
        'lider_jugador_id',
        'estado',
        'expires_at',
        'equipo_id',
        'cancelado_por',
        'nombre_equipo',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function torneo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    public function categoria(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function lider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'lider_jugador_id');
    }

    public function equipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function invitaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvitacionJugador::class);
    }

    public function estaExpirada(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function todasAceptadas(): bool
    {
        return $this->invitaciones()->where('estado', '!=', 'aceptada')->doesntExist();
    }
}
```

- [ ] **Step 2: Crear modelo InvitacionJugador**

```bash
php artisan make:model InvitacionJugador --no-interaction
```

Reemplazar `app/Models/InvitacionJugador.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitacionJugador extends Model
{
    use HasFactory;

    protected $table = 'invitaciones_jugador';

    protected $fillable = [
        'inscripcion_equipo_id',
        'jugador_id',
        'estado',
        'auto_aceptada',
        'token',
        'respondido_at',
    ];

    protected $casts = [
        'auto_aceptada' => 'boolean',
        'respondido_at' => 'datetime',
    ];

    public function inscripcionEquipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InscripcionEquipo::class);
    }

    public function jugador(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Jugador::class);
    }
}
```

- [ ] **Step 3: Actualizar modelo Jugador**

En `app/Models/Jugador.php`, agregar `'auto_aceptar_invitaciones'` al array `$fillable` y el cast:

```php
// En $fillable, agregar después de 'organizador_id':
'auto_aceptar_invitaciones',
```

```php
// En $casts, agregar:
'auto_aceptar_invitaciones' => 'boolean',
```

También agregar la relación al final del archivo antes del cierre de clase:

```php
public function invitaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(InvitacionJugador::class);
}
```

- [ ] **Step 4: Actualizar modelo Torneo**

En `app/Models/Torneo.php`, agregar después de la relación `inscripciones()`:

```php
public function inscripcionesEquipo(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(InscripcionEquipo::class);
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Models/
git commit -m "feat: modelos InscripcionEquipo e InvitacionJugador"
```

---

## Task 3: InscripcionService — validaciones y búsqueda de jugadores

**Files:**
- Create: `app/Services/InscripcionService.php`
- Create: `tests/Feature/InscripcionServiceTest.php`

- [ ] **Step 1: Crear test que falla**

```bash
php artisan make:test InscripcionServiceTest --no-interaction
```

Reemplazar `tests/Feature/InscripcionServiceTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\InscripcionEquipo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use App\Services\InscripcionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscripcionServiceTest extends TestCase
{
    use RefreshDatabase;

    private InscripcionService $service;
    private Torneo $torneo;
    private Categoria $categoria;
    private Jugador $lider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InscripcionService();

        $deporte = Deporte::create(['nombre' => 'Padel', 'slug' => 'padel']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);

        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $this->torneo = Torneo::create([
            'nombre' => 'Torneo Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => 1,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Masculino',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo->categorias()->attach($this->categoria->id, [
            'cupos_categoria' => 8,
            'edad_minima' => 18,
            'edad_maxima' => 50,
            'genero_permitido' => 'masculino',
        ]);

        $userLider = User::factory()->create();
        $userLider->assignRole('Jugador');
        $this->lider = Jugador::factory()->create([
            'user_id' => $userLider->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);
    }

    public function test_iniciar_inscripcion_crea_inscripcion_con_estado_pendiente(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $this->assertDatabaseHas('inscripciones_equipo', [
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
            'lider_jugador_id' => $this->lider->id,
            'estado' => 'pendiente',
        ]);

        $this->assertNotNull($inscripcion->expires_at);
        $this->assertTrue($inscripcion->expires_at->isFuture());
    }

    public function test_iniciar_inscripcion_crea_invitacion_del_lider_como_aceptada(): void
    {
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $this->assertDatabaseHas('invitaciones_jugador', [
            'jugador_id' => $this->lider->id,
            'estado' => 'aceptada',
        ]);
    }

    public function test_no_puede_inscribirse_en_torneo_no_activo(): void
    {
        $this->torneo->update(['estado' => 'borrador']);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_si_no_cumple_edad(): void
    {
        $userJoven = User::factory()->create();
        $jugadorJoven = Jugador::factory()->create([
            'user_id' => $userJoven->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(16),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($jugadorJoven, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_si_no_cumple_genero(): void
    {
        $userFem = User::factory()->create();
        $jugadorFem = Jugador::factory()->create([
            'user_id' => $userFem->id,
            'genero' => 'femenino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($jugadorFem, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_sin_cupos_disponibles(): void
    {
        $this->torneo->categorias()->updateExistingPivot($this->categoria->id, ['cupos_categoria' => 0]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    }

    public function test_buscar_jugadores_elegibles_filtra_por_condiciones_del_torneo(): void
    {
        $userElegible = User::factory()->create();
        $jugadorElegible = Jugador::factory()->create([
            'user_id' => $userElegible->id,
            'nombre' => 'Carlos',
            'apellido' => 'García',
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(30),
        ]);

        $userNoElegible = User::factory()->create();
        Jugador::factory()->create([
            'user_id' => $userNoElegible->id,
            'nombre' => 'María',
            'apellido' => 'García',
            'genero' => 'femenino',
            'fecha_nacimiento' => Carbon::now()->subYears(30),
        ]);

        $resultado = $this->service->buscarJugadoresElegibles($this->torneo, $this->categoria, 'García');

        $this->assertCount(1, $resultado);
        $this->assertEquals($jugadorElegible->id, $resultado->first()->id);
    }

    public function test_buscar_jugadores_excluye_al_lider(): void
    {
        $resultado = $this->service->buscarJugadoresElegibles($this->torneo, $this->categoria, $this->lider->apellido);

        $ids = $resultado->pluck('id');
        $this->assertNotContains($this->lider->id, $ids);
    }
}
```

- [ ] **Step 2: Ejecutar test para confirmar que falla**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --no-interaction
```

Esperado: FAIL — clase `InscripcionService` no existe.

- [ ] **Step 3: Crear InscripcionService con iniciarInscripcion y buscarJugadoresElegibles**

Crear `app/Services/InscripcionService.php`:

```php
<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Equipo;
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\Notificacion;
use App\Models\TamanioGrupo;
use App\Models\Torneo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InscripcionService
{
    public function iniciarInscripcion(Jugador $lider, Torneo $torneo, Categoria $categoria): InscripcionEquipo
    {
        if ($torneo->estado !== 'activo') {
            throw new \RuntimeException('Solo puedes inscribirte en torneos activos.');
        }

        $categoriaConPivot = $torneo->categorias()->where('categorias.id', $categoria->id)->first();

        if (! $categoriaConPivot) {
            throw new \RuntimeException('La categoría no pertenece a este torneo.');
        }

        $this->validarCondicionesJugador($lider, $categoriaConPivot);

        $cuposDisponibles = $this->calcularCuposDisponibles($torneo, $categoriaConPivot);

        if ($cuposDisponibles <= 0) {
            throw new \RuntimeException('No hay cupos disponibles en esta categoría.');
        }

        return DB::transaction(function () use ($lider, $torneo, $categoriaConPivot) {
            $inscripcion = InscripcionEquipo::create([
                'torneo_id' => $torneo->id,
                'categoria_id' => $categoriaConPivot->id,
                'lider_jugador_id' => $lider->id,
                'estado' => 'pendiente',
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            InvitacionJugador::create([
                'inscripcion_equipo_id' => $inscripcion->id,
                'jugador_id' => $lider->id,
                'estado' => 'aceptada',
                'auto_aceptada' => false,
                'token' => Str::random(40),
                'respondido_at' => Carbon::now(),
            ]);

            return $inscripcion;
        });
    }

    public function buscarJugadoresElegibles(Torneo $torneo, Categoria $categoria, string $query): Collection
    {
        $categoriaConPivot = $torneo->categorias()->where('categorias.id', $categoria->id)->first();

        $jugadoresYaEnEquipo = DB::table('equipo_jugador')
            ->join('equipos', 'equipo_jugador.equipo_id', '=', 'equipos.id')
            ->where('equipos.torneo_id', $torneo->id)
            ->where('equipos.categoria_id', $categoria->id)
            ->whereNull('equipos.deleted_at')
            ->pluck('equipo_jugador.jugador_id')
            ->toArray();

        $jugadoresInvitados = DB::table('invitaciones_jugador')
            ->join('inscripciones_equipo', 'invitaciones_jugador.inscripcion_equipo_id', '=', 'inscripciones_equipo.id')
            ->where('inscripciones_equipo.torneo_id', $torneo->id)
            ->where('inscripciones_equipo.categoria_id', $categoria->id)
            ->where('inscripciones_equipo.estado', 'pendiente')
            ->whereNull('inscripciones_equipo.deleted_at')
            ->pluck('invitaciones_jugador.jugador_id')
            ->toArray();

        $excluidos = array_unique(array_merge($jugadoresYaEnEquipo, $jugadoresInvitados));

        $jugadores = Jugador::whereNotNull('user_id')
            ->whereNotIn('id', $excluidos)
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                    ->orWhere('apellido', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('telefono', 'like', "%{$query}%");
            })
            ->get();

        if (! $categoriaConPivot) {
            return $jugadores;
        }

        return $jugadores->filter(function (Jugador $jugador) use ($categoriaConPivot) {
            return $this->jugadorCumpleCondiciones($jugador, $categoriaConPivot);
        })->values();
    }

    public function validarCondicionesJugador(Jugador $jugador, $categoriaConPivot): void
    {
        $generoPermitido = $categoriaConPivot->pivot->genero_permitido ?? null;

        if ($generoPermitido && $generoPermitido !== 'mixto' && $jugador->genero !== $generoPermitido) {
            throw new \RuntimeException("Tu género no cumple con los requisitos de esta categoría.");
        }

        $edad = $jugador->fecha_nacimiento ? $jugador->fecha_nacimiento->age : null;

        if ($edad !== null) {
            $edadMinima = $categoriaConPivot->pivot->edad_minima ?? null;
            $edadMaxima = $categoriaConPivot->pivot->edad_maxima ?? null;

            if ($edadMinima && $edad < $edadMinima) {
                throw new \RuntimeException("Debes tener al menos {$edadMinima} años para esta categoría.");
            }

            if ($edadMaxima && $edad > $edadMaxima) {
                throw new \RuntimeException("Debes tener como máximo {$edadMaxima} años para esta categoría.");
            }
        }
    }

    public function calcularCuposDisponibles(Torneo $torneo, $categoriaConPivot): int
    {
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            $numeroGrupos = $categoriaConPivot->pivot->numero_grupos ?? 0;
            $tamanioGrupoId = $categoriaConPivot->pivot->tamanio_grupo_id ?? null;
            $tamanioGrupo = $tamanioGrupoId ? TamanioGrupo::find($tamanioGrupoId) : null;
            $cuposCategoria = $numeroGrupos * ($tamanioGrupo ? $tamanioGrupo->tamanio : 0);
        } else {
            $cuposCategoria = $categoriaConPivot->pivot->cupos_categoria ?? 0;
        }

        $equiposConfirmados = $torneo->equipos()
            ->where('categoria_id', $categoriaConPivot->id)
            ->count();

        return max(0, $cuposCategoria - $equiposConfirmados);
    }

    private function jugadorCumpleCondiciones(Jugador $jugador, $categoriaConPivot): bool
    {
        $generoPermitido = $categoriaConPivot->pivot->genero_permitido ?? null;

        if ($generoPermitido && $generoPermitido !== 'mixto' && $jugador->genero !== $generoPermitido) {
            return false;
        }

        $edad = $jugador->fecha_nacimiento ? $jugador->fecha_nacimiento->age : null;

        if ($edad !== null) {
            $edadMinima = $categoriaConPivot->pivot->edad_minima ?? null;
            $edadMaxima = $categoriaConPivot->pivot->edad_maxima ?? null;

            if ($edadMinima && $edad < $edadMinima) {
                return false;
            }

            if ($edadMaxima && $edad > $edadMaxima) {
                return false;
            }
        }

        return true;
    }
}
```

- [ ] **Step 4: Ejecutar tests y confirmar que pasan**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --no-interaction
```

Esperado: todos los tests pasan.

- [ ] **Step 5: Formatear código**

```bash
vendor/bin/pint --dirty
```

- [ ] **Step 6: Commit**

```bash
git add app/Services/InscripcionService.php tests/Feature/InscripcionServiceTest.php
git commit -m "feat: InscripcionService - iniciarInscripcion y buscarJugadoresElegibles"
```

---

## Task 4: InscripcionService — invitaciones, auto-aceptar y confirmación

**Files:**
- Modify: `app/Services/InscripcionService.php`
- Modify: `tests/Feature/InscripcionServiceTest.php`

- [ ] **Step 1: Agregar tests para enviarInvitacion, auto-aceptar y verificarYConfirmar**

En `tests/Feature/InscripcionServiceTest.php`, agregar los siguientes métodos al final de la clase (antes del cierre `}`):

```php
public function test_enviar_invitacion_crea_registro_pendiente(): void
{
    $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

    $userInvitado = User::factory()->create();
    $jugadorInvitado = Jugador::factory()->create([
        'user_id' => $userInvitado->id,
        'genero' => 'masculino',
        'fecha_nacimiento' => Carbon::now()->subYears(28),
    ]);

    $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

    $this->assertDatabaseHas('invitaciones_jugador', [
        'inscripcion_equipo_id' => $inscripcion->id,
        'jugador_id' => $jugadorInvitado->id,
        'estado' => 'pendiente',
    ]);

    $this->assertNotEmpty($invitacion->token);
}

public function test_debe_auto_aceptar_cuando_flag_activo_y_han_jugado_juntos(): void
{
    $userInvitado = User::factory()->create();
    $jugadorInvitado = Jugador::factory()->create([
        'user_id' => $userInvitado->id,
        'auto_aceptar_invitaciones' => true,
        'genero' => 'masculino',
        'fecha_nacimiento' => Carbon::now()->subYears(28),
    ]);

    // Simular que han jugado juntos en un equipo
    $equipo = \App\Models\Equipo::create([
        'nombre' => 'Equipo anterior',
        'torneo_id' => $this->torneo->id,
        'categoria_id' => $this->categoria->id,
    ]);
    $equipo->jugadores()->attach($this->lider->id, ['orden' => 1]);
    $equipo->jugadores()->attach($jugadorInvitado->id, ['orden' => 2]);

    $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

    $this->assertEquals('aceptada', $invitacion->fresh()->estado);
    $this->assertTrue($invitacion->fresh()->auto_aceptada);
}

public function test_no_auto_acepta_si_nunca_han_jugado_juntos(): void
{
    $userInvitado = User::factory()->create();
    $jugadorInvitado = Jugador::factory()->create([
        'user_id' => $userInvitado->id,
        'auto_aceptar_invitaciones' => true,
        'genero' => 'masculino',
        'fecha_nacimiento' => Carbon::now()->subYears(28),
    ]);

    $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

    $this->assertEquals('pendiente', $invitacion->fresh()->estado);
}

public function test_verificar_y_confirmar_crea_equipo_cuando_todos_aceptan(): void
{
    $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

    $userInvitado = User::factory()->create();
    $jugadorInvitado = Jugador::factory()->create([
        'user_id' => $userInvitado->id,
        'genero' => 'masculino',
        'fecha_nacimiento' => Carbon::now()->subYears(28),
    ]);

    $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);
    $invitacion->update(['estado' => 'aceptada', 'respondido_at' => now()]);

    $this->service->verificarYConfirmar($inscripcion->fresh());

    $inscripcion->refresh();
    $this->assertEquals('confirmada', $inscripcion->estado);
    $this->assertNotNull($inscripcion->equipo_id);

    $this->assertDatabaseHas('equipos', [
        'torneo_id' => $this->torneo->id,
        'categoria_id' => $this->categoria->id,
    ]);
}

public function test_cancelar_inscripcion_actualiza_estado(): void
{
    $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

    $this->service->cancelarInscripcion($inscripcion, 'jugador');

    $inscripcion->refresh();
    $this->assertEquals('cancelada', $inscripcion->estado);
    $this->assertEquals('jugador', $inscripcion->cancelado_por);
}
```

- [ ] **Step 2: Ejecutar tests para confirmar que fallan**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --filter="test_enviar_invitacion|test_debe_auto|test_no_auto|test_verificar|test_cancelar" --no-interaction
```

Esperado: FAIL — métodos no existen.

- [ ] **Step 3: Agregar métodos al InscripcionService**

En `app/Services/InscripcionService.php`, agregar los siguientes métodos antes del cierre de clase:

```php
public function enviarInvitacion(InscripcionEquipo $inscripcion, Jugador $jugador): InvitacionJugador
{
    if ($inscripcion->estado !== 'pendiente') {
        throw new \RuntimeException('La inscripción ya no está activa.');
    }

    if ($inscripcion->invitaciones()->where('jugador_id', $jugador->id)->exists()) {
        throw new \RuntimeException('Este jugador ya fue invitado.');
    }

    $invitacion = InvitacionJugador::create([
        'inscripcion_equipo_id' => $inscripcion->id,
        'jugador_id' => $jugador->id,
        'estado' => 'pendiente',
        'auto_aceptada' => false,
        'token' => Str::random(40),
    ]);

    if ($this->debeAutoAceptar($invitacion)) {
        $invitacion->update([
            'estado' => 'aceptada',
            'auto_aceptada' => true,
            'respondido_at' => Carbon::now(),
        ]);

        $this->verificarYConfirmar($inscripcion->fresh());
    } else {
        $this->notificarInvitacion($invitacion);
    }

    return $invitacion->fresh();
}

public function debeAutoAceptar(InvitacionJugador $invitacion): bool
{
    $jugador = $invitacion->jugador;

    if (! $jugador->auto_aceptar_invitaciones) {
        return false;
    }

    $lider = $invitacion->inscripcionEquipo->lider;

    return DB::table('equipo_jugador as ej1')
        ->join('equipo_jugador as ej2', 'ej1.equipo_id', '=', 'ej2.equipo_id')
        ->where('ej1.jugador_id', $lider->id)
        ->where('ej2.jugador_id', $jugador->id)
        ->exists();
}

public function responderInvitacion(InvitacionJugador $invitacion, bool $aceptar): void
{
    if ($invitacion->estado !== 'pendiente') {
        throw new \RuntimeException('Esta invitación ya fue respondida.');
    }

    $invitacion->update([
        'estado' => $aceptar ? 'aceptada' : 'rechazada',
        'respondido_at' => Carbon::now(),
    ]);

    if (! $aceptar) {
        $this->cancelarInscripcion($invitacion->inscripcionEquipo, 'jugador');
    } else {
        $this->verificarYConfirmar($invitacion->inscripcionEquipo->fresh());
    }
}

public function verificarYConfirmar(InscripcionEquipo $inscripcion): void
{
    if ($inscripcion->estado !== 'pendiente') {
        return;
    }

    if (! $inscripcion->todasAceptadas()) {
        return;
    }

    DB::transaction(function () use ($inscripcion) {
        $torneo = $inscripcion->torneo;
        $jugadores = $inscripcion->invitaciones()->with('jugador')->get()->pluck('jugador');

        $nombreEquipo = $inscripcion->nombre_equipo
            ?? $jugadores->pluck('apellido')->join(' / ');

        $equipo = Equipo::create([
            'nombre' => $nombreEquipo,
            'torneo_id' => $torneo->id,
            'categoria_id' => $inscripcion->categoria_id,
        ]);

        foreach ($jugadores as $index => $jugador) {
            $equipo->jugadores()->attach($jugador->id, ['orden' => $index + 1]);
        }

        $inscripcion->update([
            'estado' => 'confirmada',
            'equipo_id' => $equipo->id,
        ]);

        $this->notificarInscripcionConfirmada($inscripcion, $jugadores);
    });
}

public function cancelarInscripcion(InscripcionEquipo $inscripcion, string $canceladoPor): void
{
    if ($inscripcion->estado === 'cancelada') {
        return;
    }

    $jugadores = $inscripcion->invitaciones()->with('jugador')->get()->pluck('jugador');

    $inscripcion->update([
        'estado' => 'cancelada',
        'cancelado_por' => $canceladoPor,
    ]);

    $this->notificarInscripcionCancelada($inscripcion, $jugadores);
}

private function notificarInvitacion(InvitacionJugador $invitacion): void
{
    $jugador = $invitacion->jugador;
    $inscripcion = $invitacion->inscripcionEquipo;

    if ($jugador->user) {
        $jugador->user->notify(
            new \App\Notifications\InvitacionTorneoNotification($invitacion)
        );

        $this->crearNotificacionInApp(
            $inscripcion->torneo_id,
            $jugador->user,
            "¡Te invitaron a inscribirte en el torneo {$inscripcion->torneo->nombre}!",
            'invitacion_torneo'
        );
    }
}

private function notificarInscripcionConfirmada(InscripcionEquipo $inscripcion, $jugadores): void
{
    foreach ($jugadores as $jugador) {
        if ($jugador->user) {
            $jugador->user->notify(
                new \App\Notifications\InscripcionConfirmadaNotification($inscripcion)
            );

            $this->crearNotificacionInApp(
                $inscripcion->torneo_id,
                $jugador->user,
                "¡Tu equipo quedó inscripto en el torneo {$inscripcion->torneo->nombre}!",
                'inscripcion_confirmada'
            );
        }
    }

    $organizador = $inscripcion->torneo->organizador;
    if ($organizador) {
        $organizador->notify(
            new \App\Notifications\NuevoEquipoInscriptoNotification($inscripcion)
        );

        $this->crearNotificacionInApp(
            $inscripcion->torneo_id,
            $organizador,
            "Nuevo equipo inscripto en {$inscripcion->torneo->nombre}: {$inscripcion->equipo->nombre}",
            'nuevo_equipo_inscripto'
        );
    }
}

private function notificarInscripcionCancelada(InscripcionEquipo $inscripcion, $jugadores): void
{
    foreach ($jugadores as $jugador) {
        if ($jugador->user) {
            $jugador->user->notify(
                new \App\Notifications\InscripcionCanceladaNotification($inscripcion)
            );

            $this->crearNotificacionInApp(
                $inscripcion->torneo_id,
                $jugador->user,
                "La inscripción al torneo {$inscripcion->torneo->nombre} fue cancelada.",
                'inscripcion_cancelada'
            );
        }
    }
}

private function crearNotificacionInApp(int $torneoId, \App\Models\User $user, string $mensaje, string $tipo): void
{
    $notificacion = \App\Models\Notificacion::create([
        'torneo_id' => $torneoId,
        'mensaje' => $mensaje,
        'tipo' => $tipo,
        'enviado_at' => Carbon::now(),
    ]);

    $notificacion->usuarios()->attach($user->id, ['leida' => false]);
}
```

- [ ] **Step 4: Ejecutar todos los tests del servicio**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --no-interaction
```

Esperado: todos los tests pasan (algunos pueden fallar por clases de notificaciones que aún no existen — se crean en el próximo task).

- [ ] **Step 5: Formatear**

```bash
vendor/bin/pint --dirty
```

- [ ] **Step 6: Commit**

```bash
git add app/Services/InscripcionService.php tests/Feature/InscripcionServiceTest.php
git commit -m "feat: InscripcionService - invitaciones, auto-aceptar, confirmación y cancelación"
```

---

## Task 5: Notificaciones por email

**Files:**
- Create: `app/Notifications/InvitacionTorneoNotification.php`
- Create: `app/Notifications/InscripcionConfirmadaNotification.php`
- Create: `app/Notifications/InscripcionCanceladaNotification.php`
- Create: `app/Notifications/NuevoEquipoInscriptoNotification.php`

- [ ] **Step 1: Crear InvitacionTorneoNotification**

```bash
php artisan make:notification InvitacionTorneoNotification --no-interaction
```

Reemplazar `app/Notifications/InvitacionTorneoNotification.php`:

```php
<?php

namespace App\Notifications;

use App\Models\InvitacionJugador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitacionTorneoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InvitacionJugador $invitacion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inscripcion = $this->invitacion->inscripcionEquipo;
        $lider = $inscripcion->lider;
        $torneo = $inscripcion->torneo;
        $url = route('inscripciones.invitacion.mostrar', $this->invitacion->token);

        return (new MailMessage)
            ->subject("¡{$lider->nombre_completo} te invita al torneo {$torneo->nombre}!")
            ->greeting("Hola {$notifiable->name},")
            ->line("{$lider->nombre_completo} te invitó a formar equipo en el torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$inscripcion->categoria->nombre}")
            ->line("Complejo: {$torneo->complejo->nombre}")
            ->action('Ver invitación', $url)
            ->line('La invitación expira en 10 minutos. Si el tiempo vence pero hay cupos disponibles, igual se confirmará.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitacion_id' => $this->invitacion->id,
            'torneo_id' => $this->invitacion->inscripcionEquipo->torneo_id,
        ];
    }
}
```

- [ ] **Step 2: Crear InscripcionConfirmadaNotification**

```bash
php artisan make:notification InscripcionConfirmadaNotification --no-interaction
```

Reemplazar `app/Notifications/InscripcionConfirmadaNotification.php`:

```php
<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionConfirmadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InscripcionEquipo $inscripcion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $torneo = $this->inscripcion->torneo;
        $equipo = $this->inscripcion->equipo;
        $url = route('torneos.public', $torneo->id);

        return (new MailMessage)
            ->subject("¡Tu equipo quedó inscripto en {$torneo->nombre}!")
            ->greeting("¡Felicitaciones {$notifiable->name}!")
            ->line("Tu equipo **{$equipo->nombre}** quedó inscripto exitosamente en el torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$this->inscripcion->categoria->nombre}")
            ->action('Ver torneo', $url)
            ->line('El organizador te contactará con los detalles del torneo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inscripcion_id' => $this->inscripcion->id,
            'torneo_id' => $this->inscripcion->torneo_id,
        ];
    }
}
```

- [ ] **Step 3: Crear InscripcionCanceladaNotification**

```bash
php artisan make:notification InscripcionCanceladaNotification --no-interaction
```

Reemplazar `app/Notifications/InscripcionCanceladaNotification.php`:

```php
<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionCanceladaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InscripcionEquipo $inscripcion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $torneo = $this->inscripcion->torneo;

        $motivo = match ($this->inscripcion->cancelado_por) {
            'organizador' => 'el organizador eliminó el equipo del torneo.',
            'jugador' => 'un jugador rechazó la invitación.',
            'expiracion' => 'el tiempo de inscripción venció y no había cupos disponibles.',
            default => 'la inscripción fue cancelada.',
        };

        return (new MailMessage)
            ->subject("Inscripción cancelada — {$torneo->nombre}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu inscripción al torneo **{$torneo->nombre}** fue cancelada porque {$motivo}")
            ->line('Podés intentar inscribirte nuevamente si hay cupos disponibles.')
            ->action('Ver torneo', route('torneos.public', $torneo->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inscripcion_id' => $this->inscripcion->id,
            'torneo_id' => $this->inscripcion->torneo_id,
            'cancelado_por' => $this->inscripcion->cancelado_por,
        ];
    }
}
```

- [ ] **Step 4: Crear NuevoEquipoInscriptoNotification**

```bash
php artisan make:notification NuevoEquipoInscriptoNotification --no-interaction
```

Reemplazar `app/Notifications/NuevoEquipoInscriptoNotification.php`:

```php
<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoEquipoInscriptoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InscripcionEquipo $inscripcion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $torneo = $this->inscripcion->torneo;
        $equipo = $this->inscripcion->equipo;
        $url = route('torneos.equipos.index', $torneo->id);

        return (new MailMessage)
            ->subject("Nuevo equipo inscripto en {$torneo->nombre}")
            ->greeting("Hola {$notifiable->name},")
            ->line("El equipo **{$equipo->nombre}** se inscribió en tu torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$this->inscripcion->categoria->nombre}")
            ->action('Ver equipos', $url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inscripcion_id' => $this->inscripcion->id,
            'torneo_id' => $this->inscripcion->torneo_id,
            'equipo_id' => $this->inscripcion->equipo_id,
        ];
    }
}
```

- [ ] **Step 5: Ejecutar tests del servicio completos**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --no-interaction
```

Esperado: todos los tests pasan.

- [ ] **Step 6: Formatear y commit**

```bash
vendor/bin/pint --dirty
git add app/Notifications/
git commit -m "feat: notificaciones email para invitacion, confirmacion y cancelacion de inscripcion"
```

---

## Task 6: Job de expiración de inscripciones

**Files:**
- Create: `app/Jobs/ProcesarInscripcionesExpiradas.php`
- Modify: `app/Console/Kernel.php`

- [ ] **Step 1: Crear el job**

```bash
php artisan make:job ProcesarInscripcionesExpiradas --no-interaction
```

Reemplazar `app/Jobs/ProcesarInscripcionesExpiradas.php`:

```php
<?php

namespace App\Jobs;

use App\Models\InscripcionEquipo;
use App\Services\InscripcionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarInscripcionesExpiradas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(InscripcionService $service): void
    {
        $inscripcionesExpiradas = InscripcionEquipo::where('estado', 'pendiente')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['torneo.formato', 'torneo.equipos', 'categoria', 'invitaciones.jugador.user'])
            ->get();

        foreach ($inscripcionesExpiradas as $inscripcion) {
            $torneo = $inscripcion->torneo;
            $categoriaConPivot = $torneo->categorias()
                ->where('categorias.id', $inscripcion->categoria_id)
                ->first();

            if (! $categoriaConPivot) {
                $service->cancelarInscripcion($inscripcion, 'expiracion');
                continue;
            }

            $cuposDisponibles = $service->calcularCuposDisponibles($torneo, $categoriaConPivot);

            if ($cuposDisponibles > 0) {
                // Hay cupo: confirmar igual
                $service->verificarYConfirmar($inscripcion);

                if ($inscripcion->fresh()->estado !== 'confirmada') {
                    // No todas aceptaron, cancelar
                    $service->cancelarInscripcion($inscripcion->fresh(), 'expiracion');
                }
            } else {
                $service->cancelarInscripcion($inscripcion, 'expiracion');
            }

            Log::info("Inscripción expirada procesada", ['inscripcion_id' => $inscripcion->id]);
        }
    }
}
```

- [ ] **Step 2: Agregar al schedule en Kernel.php**

En `app/Console/Kernel.php`, dentro del método `schedule()`, agregar después de los jobs existentes:

```php
// Procesar inscripciones expiradas (cada 5 minutos)
$schedule->job(new \App\Jobs\ProcesarInscripcionesExpiradas())
    ->everyFiveMinutes()
    ->name('procesar-inscripciones-expiradas')
    ->onOneServer();
```

- [ ] **Step 3: Formatear y commit**

```bash
vendor/bin/pint --dirty
git add app/Jobs/ProcesarInscripcionesExpiradas.php app/Console/Kernel.php
git commit -m "feat: job ProcesarInscripcionesExpiradas con schedule cada 5 minutos"
```

---

## Task 7: InscripcionController y rutas

**Files:**
- Create: `app/Http/Controllers/InscripcionController.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/InscripcionControllerTest.php`

- [ ] **Step 1: Crear test del controlador**

```bash
php artisan make:test InscripcionControllerTest --no-interaction
```

Reemplazar `tests/Feature/InscripcionControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\InscripcionEquipo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscripcionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $userJugador;
    private Jugador $jugador;
    private Torneo $torneo;
    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        $deporte = Deporte::create(['nombre' => 'Padel', 'slug' => 'padel']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo = Torneo::create([
            'nombre' => 'Torneo Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Masculino',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo->categorias()->attach($this->categoria->id, [
            'cupos_categoria' => 8,
            'edad_minima' => null,
            'edad_maxima' => null,
            'genero_permitido' => null,
        ]);

        $this->userJugador = User::factory()->create();
        $this->userJugador->assignRole('Jugador');
        $this->jugador = Jugador::factory()->create([
            'user_id' => $this->userJugador->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);
    }

    public function test_jugador_puede_ver_formulario_de_inscripcion(): void
    {
        $response = $this->actingAs($this->userJugador)
            ->get(route('torneos.inscripciones.crear', $this->torneo));

        $response->assertOk();
    }

    public function test_jugador_puede_iniciar_inscripcion(): void
    {
        $response = $this->actingAs($this->userJugador)
            ->post(route('torneos.inscripciones.store', $this->torneo), [
                'categoria_id' => $this->categoria->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inscripciones_equipo', [
            'torneo_id' => $this->torneo->id,
            'lider_jugador_id' => $this->jugador->id,
            'estado' => 'pendiente',
        ]);
    }

    public function test_usuario_sin_perfil_jugador_no_puede_inscribirse(): void
    {
        $userSinJugador = User::factory()->create();

        $response = $this->actingAs($userSinJugador)
            ->post(route('torneos.inscripciones.store', $this->torneo), [
                'categoria_id' => $this->categoria->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseEmpty('inscripciones_equipo');
    }

    public function test_buscar_jugadores_devuelve_json(): void
    {
        $userElegible = User::factory()->create();
        Jugador::factory()->create([
            'user_id' => $userElegible->id,
            'nombre' => 'Carlos',
            'apellido' => 'García',
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $response = $this->actingAs($this->userJugador)
            ->get(route('torneos.inscripciones.buscar', [
                'torneo' => $this->torneo->id,
                'categoria_id' => $this->categoria->id,
                'q' => 'García',
            ]));

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_lider_puede_cancelar_inscripcion(): void
    {
        $inscripcion = InscripcionEquipo::create([
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
            'lider_jugador_id' => $this->jugador->id,
            'estado' => 'pendiente',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($this->userJugador)
            ->delete(route('inscripciones.cancelar', $inscripcion));

        $response->assertRedirect();
        $this->assertEquals('cancelada', $inscripcion->fresh()->estado);
    }
}
```

- [ ] **Step 2: Ejecutar test para confirmar que falla**

```bash
php artisan test tests/Feature/InscripcionControllerTest.php --no-interaction
```

Esperado: FAIL — rutas y controlador no existen.

- [ ] **Step 3: Crear InscripcionController**

```bash
php artisan make:controller InscripcionController --no-interaction
```

Reemplazar `app/Http/Controllers/InscripcionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\InscripcionEquipo;
use App\Models\Torneo;
use App\Services\InscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function __construct(private InscripcionService $inscripcionService) {}

    public function crear(Torneo $torneo)
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', 'Necesitás tener un perfil de jugador para inscribirte.');
        }

        $categorias = $torneo->categorias()
            ->withPivot(['cupos_categoria', 'numero_grupos', 'tamanio_grupo_id', 'edad_minima', 'edad_maxima', 'genero_permitido'])
            ->get();

        $maxJugadores = $torneo->deporte->getMaxJugadores();
        $requiereNombre = $torneo->deporte->requiereNombreEquipo();

        return view('inscripciones.crear', compact('torneo', 'categorias', 'jugador', 'maxJugadores', 'requiereNombre'));
    }

    public function store(Request $request, Torneo $torneo)
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', 'Necesitás tener un perfil de jugador para inscribirte.');
        }

        $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre_equipo' => ['nullable', 'string', 'max:100'],
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        try {
            $inscripcion = $this->inscripcionService->iniciarInscripcion($jugador, $torneo, $categoria);

            if ($request->filled('nombre_equipo')) {
                $inscripcion->update(['nombre_equipo' => $request->nombre_equipo]);
            }

            return redirect()->route('inscripciones.invitar', $inscripcion)
                ->with('success', '¡Reserva creada! Invitá a tus compañeros (tenés 10 minutos).');
        } catch (\RuntimeException $e) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', $e->getMessage());
        }
    }

    public function buscarJugadores(Request $request, Torneo $torneo)
    {
        $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'q' => ['required', 'string', 'min:2'],
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        $jugadores = $this->inscripcionService->buscarJugadoresElegibles(
            $torneo,
            $categoria,
            $request->q
        );

        return response()->json(
            $jugadores->map(fn($j) => [
                'id' => $j->id,
                'nombre_completo' => $j->nombre_completo,
                'foto' => $j->foto ? asset('storage/'.$j->foto) : null,
            ])
        );
    }

    public function invitar(Request $request, InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $request->validate([
            'jugador_id' => ['required', 'exists:jugadores,id'],
        ]);

        $jugador = \App\Models\Jugador::findOrFail($request->jugador_id);

        try {
            $this->inscripcionService->enviarInvitacion($inscripcion, $jugador);

            return redirect()->back()->with('success', "Invitación enviada a {$jugador->nombre_completo}.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function mostrarInvitaciones(InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $inscripcion->load(['invitaciones.jugador', 'torneo', 'categoria']);
        $maxJugadores = $inscripcion->torneo->deporte->getMaxJugadores();

        return view('inscripciones.crear', [
            'torneo' => $inscripcion->torneo,
            'inscripcion' => $inscripcion,
            'categorias' => $inscripcion->torneo->categorias()->withPivot(['cupos_categoria', 'numero_grupos', 'tamanio_grupo_id', 'edad_minima', 'edad_maxima', 'genero_permitido'])->get(),
            'jugador' => Auth::user()->jugador,
            'maxJugadores' => $maxJugadores,
            'requiereNombre' => $inscripcion->torneo->deporte->requiereNombreEquipo(),
        ]);
    }

    public function cancelar(InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $this->inscripcionService->cancelarInscripcion($inscripcion, 'jugador');

        return redirect()->route('torneos.public', $inscripcion->torneo_id)
            ->with('success', 'Inscripción cancelada.');
    }

    private function autorizarLider(InscripcionEquipo $inscripcion): void
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador || $inscripcion->lider_jugador_id !== $jugador->id) {
            abort(403);
        }
    }
}
```

- [ ] **Step 4: Registrar rutas en routes/web.php**

En `routes/web.php`, dentro del grupo `Route::middleware('auth')`, agregar antes del cierre del grupo:

```php
// Inscripciones de jugadores a torneos
Route::get('/torneos/{torneo}/inscribirse', [InscripcionController::class, 'crear'])->name('torneos.inscripciones.crear');
Route::post('/torneos/{torneo}/inscribirse', [InscripcionController::class, 'store'])->name('torneos.inscripciones.store');
Route::get('/torneos/{torneo}/inscribirse/buscar', [InscripcionController::class, 'buscarJugadores'])->name('torneos.inscripciones.buscar');
Route::get('/inscripciones/{inscripcion}/invitar', [InscripcionController::class, 'mostrarInvitaciones'])->name('inscripciones.invitar');
Route::post('/inscripciones/{inscripcion}/invitar', [InscripcionController::class, 'invitar'])->name('inscripciones.invitar.post');
Route::delete('/inscripciones/{inscripcion}', [InscripcionController::class, 'cancelar'])->name('inscripciones.cancelar');
```

También agregar el import al inicio del archivo:

```php
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\InvitacionController;
```

- [ ] **Step 5: Ejecutar tests del controlador**

```bash
php artisan test tests/Feature/InscripcionControllerTest.php --no-interaction
```

Esperado: todos los tests pasan.

- [ ] **Step 6: Formatear y commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/InscripcionController.php routes/web.php tests/Feature/InscripcionControllerTest.php
git commit -m "feat: InscripcionController y rutas de inscripcion"
```

---

## Task 8: InvitacionController

**Files:**
- Create: `app/Http/Controllers/InvitacionController.php`
- Create: `tests/Feature/InvitacionControllerTest.php`

- [ ] **Step 1: Crear test del controlador de invitaciones**

```bash
php artisan make:test InvitacionControllerTest --no-interaction
```

Reemplazar `tests/Feature/InvitacionControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvitacionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $userLider;
    private User $userInvitado;
    private Jugador $jugadorLider;
    private Jugador $jugadorInvitado;
    private InscripcionEquipo $inscripcion;
    private InvitacionJugador $invitacion;

    protected function setUp(): void
    {
        parent::setUp();

        $deporte = Deporte::create(['nombre' => 'Padel', 'slug' => 'padel']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'organizador_id' => $organizador->id,
        ]);

        $torneo = Torneo::create([
            'nombre' => 'Torneo Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Masculino',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $torneo->categorias()->attach($categoria->id, ['cupos_categoria' => 8]);

        $this->userLider = User::factory()->create();
        $this->userLider->assignRole('Jugador');
        $this->jugadorLider = Jugador::factory()->create(['user_id' => $this->userLider->id]);

        $this->userInvitado = User::factory()->create();
        $this->userInvitado->assignRole('Jugador');
        $this->jugadorInvitado = Jugador::factory()->create(['user_id' => $this->userInvitado->id]);

        $this->inscripcion = InscripcionEquipo::create([
            'torneo_id' => $torneo->id,
            'categoria_id' => $categoria->id,
            'lider_jugador_id' => $this->jugadorLider->id,
            'estado' => 'pendiente',
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->invitacion = InvitacionJugador::create([
            'inscripcion_equipo_id' => $this->inscripcion->id,
            'jugador_id' => $this->jugadorInvitado->id,
            'estado' => 'pendiente',
            'token' => Str::random(40),
        ]);
    }

    public function test_jugador_puede_ver_su_invitacion(): void
    {
        $response = $this->actingAs($this->userInvitado)
            ->get(route('inscripciones.invitacion.mostrar', $this->invitacion->token));

        $response->assertOk();
    }

    public function test_jugador_puede_aceptar_invitacion(): void
    {
        // Crear también la invitación del líder como aceptada para que "todos acepten"
        InvitacionJugador::create([
            'inscripcion_equipo_id' => $this->inscripcion->id,
            'jugador_id' => $this->jugadorLider->id,
            'estado' => 'aceptada',
            'token' => Str::random(40),
            'respondido_at' => now(),
        ]);

        $response = $this->actingAs($this->userInvitado)
            ->post(route('inscripciones.invitacion.aceptar', $this->invitacion->token));

        $response->assertRedirect();
        $this->assertEquals('aceptada', $this->invitacion->fresh()->estado);
    }

    public function test_jugador_puede_rechazar_invitacion(): void
    {
        $response = $this->actingAs($this->userInvitado)
            ->post(route('inscripciones.invitacion.rechazar', $this->invitacion->token));

        $response->assertRedirect();
        $this->assertEquals('rechazada', $this->invitacion->fresh()->estado);
        $this->assertEquals('cancelada', $this->inscripcion->fresh()->estado);
    }

    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get(route('inscripciones.invitacion.mostrar', $this->invitacion->token));

        $response->assertRedirect('/login');
    }

    public function test_otro_jugador_no_puede_responder_invitacion_ajena(): void
    {
        $otroUser = User::factory()->create();

        $response = $this->actingAs($otroUser)
            ->post(route('inscripciones.invitacion.aceptar', $this->invitacion->token));

        $response->assertForbidden();
    }
}
```

- [ ] **Step 2: Ejecutar test para confirmar que falla**

```bash
php artisan test tests/Feature/InvitacionControllerTest.php --no-interaction
```

Esperado: FAIL.

- [ ] **Step 3: Crear InvitacionController**

```bash
php artisan make:controller InvitacionController --no-interaction
```

Reemplazar `app/Http/Controllers/InvitacionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\InvitacionJugador;
use App\Services\InscripcionService;
use Illuminate\Support\Facades\Auth;

class InvitacionController extends Controller
{
    public function __construct(private InscripcionService $inscripcionService) {}

    public function mostrar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)
            ->with(['inscripcionEquipo.torneo', 'inscripcionEquipo.categoria', 'inscripcionEquipo.lider', 'inscripcionEquipo.invitaciones.jugador'])
            ->firstOrFail();

        if (! Auth::check()) {
            session(['invitacion_token_pendiente' => $token]);
            return redirect()->route('login')->with('info', 'Iniciá sesión para responder la invitación.');
        }

        $jugador = Auth::user()->jugador;

        if (! $jugador || $invitacion->jugador_id !== $jugador->id) {
            abort(403, 'Esta invitación no te pertenece.');
        }

        return view('inscripciones.invitacion', compact('invitacion'));
    }

    public function aceptar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)->firstOrFail();

        $this->autorizarInvitado($invitacion);

        try {
            $this->inscripcionService->responderInvitacion($invitacion, true);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $torneo = $invitacion->inscripcionEquipo->torneo;
        return redirect()->route('torneos.public', $torneo->id)
            ->with('success', '¡Aceptaste la invitación! Cuando todos confirmen, el equipo quedará inscripto.');
    }

    public function rechazar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)->firstOrFail();

        $this->autorizarInvitado($invitacion);

        try {
            $this->inscripcionService->responderInvitacion($invitacion, false);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('landing')
            ->with('info', 'Rechazaste la invitación. La inscripción fue cancelada.');
    }

    private function autorizarInvitado(InvitacionJugador $invitacion): void
    {
        $jugador = Auth::user()?->jugador;

        if (! $jugador || $invitacion->jugador_id !== $jugador->id) {
            abort(403);
        }
    }
}
```

- [ ] **Step 4: Agregar rutas del InvitacionController en routes/web.php**

Dentro del grupo `Route::middleware('auth')`, agregar junto a las rutas de inscripciones:

```php
// Respuesta a invitaciones
Route::get('/inscripciones/invitacion/{token}', [InvitacionController::class, 'mostrar'])->name('inscripciones.invitacion.mostrar');
Route::post('/inscripciones/invitacion/{token}/aceptar', [InvitacionController::class, 'aceptar'])->name('inscripciones.invitacion.aceptar');
Route::post('/inscripciones/invitacion/{token}/rechazar', [InvitacionController::class, 'rechazar'])->name('inscripciones.invitacion.rechazar');
```

**Nota importante:** La ruta `mostrar` debe ir fuera del middleware `auth` para permitir la redirección al login con el token guardado en sesión. Agregar también en las rutas públicas:

```php
// Invitación a inscripción (redirige a login si no autenticado)
Route::get('/inscripciones/invitacion/{token}', [InvitacionController::class, 'mostrar'])->name('inscripciones.invitacion.mostrar');
```

Y remover la misma del grupo `auth`.

- [ ] **Step 5: Ejecutar tests**

```bash
php artisan test tests/Feature/InvitacionControllerTest.php --no-interaction
```

Esperado: todos pasan.

- [ ] **Step 6: Formatear y commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/InvitacionController.php routes/web.php tests/Feature/InvitacionControllerTest.php
git commit -m "feat: InvitacionController para aceptar/rechazar invitaciones con token"
```

---

## Task 9: Vista de inscripción (crear.blade.php)

**Files:**
- Create: `resources/views/inscripciones/crear.blade.php`

- [ ] **Step 1: Crear directorio y vista**

```bash
mkdir -p resources/views/inscripciones
```

Crear `resources/views/inscripciones/crear.blade.php`:

```blade
@extends('layouts.jugador')

@section('title', 'Inscribirse al torneo')
@section('page-title', 'Inscribirse — ' . $torneo->nombre)

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-0 py-4 md:py-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info del torneo --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <h2 class="text-lg font-bold text-gray-900">{{ $torneo->nombre }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $torneo->deporte->nombre }} · {{ $torneo->complejo->nombre }}</p>
    </div>

    @if(!isset($inscripcion))
    {{-- PASO 1: Seleccionar categoría --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-4">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Paso 1: Seleccioná la categoría</h3>

        <form action="{{ route('torneos.inscripciones.store', $torneo) }}" method="POST">
            @csrf

            <div class="space-y-3 mb-4">
                @foreach($categorias as $cat)
                <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 transition">
                    <input type="radio" name="categoria_id" value="{{ $cat->id }}" class="mt-1" required>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">{{ $cat->nombre }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($cat->pivot->edad_minima || $cat->pivot->edad_maxima)
                                Edad:
                                @if($cat->pivot->edad_minima) +{{ $cat->pivot->edad_minima }} @endif
                                @if($cat->pivot->edad_maxima) hasta {{ $cat->pivot->edad_maxima }} @endif
                                ·
                            @endif
                            @if($cat->pivot->genero_permitido)
                                {{ ucfirst($cat->pivot->genero_permitido) }} ·
                            @endif
                            {{ $cat->pivot->cupos_categoria ?? '?' }} cupos
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            @if($requiereNombre)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del equipo</label>
                <input type="text" name="nombre_equipo" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Ej: Los Campeones">
            </div>
            @endif

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
                Continuar e invitar compañeros
            </button>
        </form>
    </div>

    @else
    {{-- PASO 2: Invitar compañeros --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-4">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Paso 2: Invitá a tus compañeros</h3>

        @php
            $totalInvitados = $inscripcion->invitaciones->count();
            $minutosRestantes = $inscripcion->expires_at ? max(0, now()->diffInMinutes($inscripcion->expires_at, false)) : 0;
        @endphp

        <p class="text-sm text-gray-500 mb-4">
            Necesitás {{ $maxJugadores }} jugadores en total · Tiempo restante:
            <span class="font-semibold text-orange-600">{{ $minutosRestantes }} min</span>
        </p>

        {{-- Jugadores ya invitados --}}
        <div class="space-y-2 mb-4">
            @foreach($inscripcion->invitaciones as $inv)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                        {{ substr($inv->jugador->apellido, 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ $inv->jugador->nombre_completo }}</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $inv->estado === 'aceptada' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $inv->estado === 'aceptada' ? 'Confirmado' : 'Pendiente' }}
                </span>
            </div>
            @endforeach
        </div>

        @if($totalInvitados < $maxJugadores)
        {{-- Buscador de jugadores --}}
        <div x-data="buscadorJugadores({{ $torneo->id }}, {{ $inscripcion->categoria_id }})" class="border-t pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar jugador</label>
            <div class="relative">
                <input type="text" x-model="query" @input.debounce.400ms="buscar()"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Nombre, apellido, email o teléfono (mín. 2 caracteres)">

                <div x-show="resultados.length > 0" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                    <template x-for="j in resultados" :key="j.id">
                        <button type="button" @click="seleccionar(j)"
                                class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 text-left text-sm">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 flex-shrink-0"
                                 x-text="j.nombre_completo.charAt(0)"></div>
                            <span x-text="j.nombre_completo"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div x-show="seleccionado" class="mt-3 p-3 bg-indigo-50 rounded-lg flex items-center justify-between">
                <span class="text-sm font-medium text-indigo-900" x-text="seleccionado?.nombre_completo"></span>
                <form :action="`/inscripciones/{{ $inscripcion->id }}/invitar`" method="POST">
                    @csrf
                    <input type="hidden" name="jugador_id" :value="seleccionado?.id">
                    <button type="submit" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-medium transition">
                        Enviar invitación
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Cancelar --}}
        <form action="{{ route('inscripciones.cancelar', $inscripcion) }}" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Cancelar la inscripción?')"
                    class="w-full text-sm text-red-600 hover:text-red-800 py-2 font-medium transition">
                Cancelar inscripción
            </button>
        </form>
    </div>
    @endif

</div>

@push('scripts')
<script>
function buscadorJugadores(torneoId, categoriaId) {
    return {
        query: '',
        resultados: [],
        seleccionado: null,
        async buscar() {
            if (this.query.length < 2) { this.resultados = []; return; }
            const res = await fetch(`/torneos/${torneoId}/inscribirse/buscar?categoria_id=${categoriaId}&q=${encodeURIComponent(this.query)}`);
            this.resultados = await res.json();
        },
        seleccionar(j) {
            this.seleccionado = j;
            this.resultados = [];
            this.query = j.nombre_completo;
        }
    }
}
</script>
@endpush
@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/inscripciones/crear.blade.php
git commit -m "feat: vista de inscripcion - seleccion de categoria e invitacion de companeros"
```

---

## Task 10: Vista de respuesta a invitación

**Files:**
- Create: `resources/views/inscripciones/invitacion.blade.php`

- [ ] **Step 1: Crear la vista**

Crear `resources/views/inscripciones/invitacion.blade.php`:

```blade
@extends('layouts.jugador')

@section('title', 'Invitación a torneo')
@section('page-title', 'Invitación')

@section('content')
<div class="max-w-md mx-auto px-4 py-6">

    @php
        $inscripcion = $invitacion->inscripcionEquipo;
        $torneo = $inscripcion->torneo;
        $lider = $inscripcion->lider;
    @endphp

    @if($invitacion->estado !== 'pendiente')
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-600 font-medium">
                Esta invitación ya fue
                {{ $invitacion->estado === 'aceptada' ? 'aceptada' : 'rechazada' }}.
            </p>
            <a href="{{ route('torneos.public', $torneo->id) }}"
               class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                Ver torneo
            </a>
        </div>
    @elseif($inscripcion->estado === 'cancelada')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">Esta inscripción fue cancelada.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">¡Te invitaron!</h2>
                <p class="text-white/80 text-sm mt-1">{{ $lider->nombre_completo }} quiere jugar con vos</p>
            </div>

            <div class="p-6">
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Torneo</span>
                        <span class="font-medium text-gray-900">{{ $torneo->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Deporte</span>
                        <span class="font-medium text-gray-900">{{ $torneo->deporte->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Categoría</span>
                        <span class="font-medium text-gray-900">{{ $inscripcion->categoria->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Complejo</span>
                        <span class="font-medium text-gray-900">{{ $torneo->complejo->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Fecha</span>
                        <span class="font-medium text-gray-900">
                            {{ $torneo->fecha_inicio?->format('d/m/Y') }}
                            @if($torneo->fecha_fin && $torneo->fecha_fin != $torneo->fecha_inicio)
                                al {{ $torneo->fecha_fin->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Resto del equipo --}}
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Equipo</p>
                    <div class="space-y-2">
                        @foreach($inscripcion->invitaciones as $inv)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                    {{ substr($inv->jugador->apellido, 0, 1) }}
                                </div>
                                <span class="{{ $inv->jugador_id === $lider->id ? 'font-semibold' : '' }} text-gray-900">
                                    {{ $inv->jugador->nombre_completo }}
                                    @if($inv->jugador_id === $lider->id) <span class="text-xs text-gray-400">(líder)</span> @endif
                                </span>
                            </div>
                            <span class="text-xs {{ $inv->estado === 'aceptada' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $inv->estado === 'aceptada' ? '✓' : 'Pendiente' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <form action="{{ route('inscripciones.invitacion.rechazar', $invitacion->token) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('¿Rechazar la invitación? La inscripción se cancelará.')"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition">
                            Rechazar
                        </button>
                    </form>

                    <form action="{{ route('inscripciones.invitacion.aceptar', $invitacion->token) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            Aceptar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/inscripciones/invitacion.blade.php
git commit -m "feat: vista de respuesta a invitacion de torneo"
```

---

## Task 11: Botón de inscribirse en vista pública del torneo

**Files:**
- Modify: `resources/views/torneos/public.blade.php`

- [ ] **Step 1: Agregar sección de inscripción**

En `resources/views/torneos/public.blade.php`, buscar el bloque `@auth` que está al principio (con la barra de jugador). Después de la sección de descripción del torneo o cerca del final del `<body>`, agregar la sección de inscripción para jugadores.

Buscar el bloque que muestra los detalles del torneo (cerca de `fecha_inicio`) y agregar el siguiente bloque después de la descripción y antes del cierre del contenedor principal. Buscar exactamente este patrón en la vista y agregar justo antes del cierre `</body>`:

Primero identificar la ubicación exacta en el archivo:

```bash
grep -n "fecha_inicio\|precio_inscripcion\|equipos\|inscripciones" resources/views/torneos/public.blade.php | head -20
```

Luego agregar en el lugar apropiado (sección de info del torneo) el siguiente componente. Si el torneo es `activo`, mostrar la card de inscripción. Buscá el bloque donde se muestra el estado del torneo y su info, y agregá después:

```blade
{{-- Card de inscripción para jugadores --}}
@if($torneo->estado === 'activo')
    @auth
        @if(auth()->user()->hasRole('Jugador') && auth()->user()->jugador)
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 sm:p-6 mb-6">
            <h3 class="text-base font-semibold text-indigo-900 mb-1">¿Querés participar?</h3>
            <p class="text-sm text-indigo-700 mb-3">Inscribite con tu equipo en este torneo.</p>
            <a href="{{ route('torneos.inscripciones.crear', $torneo->id) }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Inscribirse al torneo
            </a>
        </div>
        @endif
    @else
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 sm:p-6 mb-6">
            <p class="text-sm text-indigo-700 mb-3">
                <a href="{{ route('login') }}" class="font-semibold underline">Iniciá sesión</a>
                o
                <a href="{{ route('register') }}" class="font-semibold underline">registrate</a>
                para inscribirte en este torneo.
            </p>
        </div>
    @endauth
@endif
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/torneos/public.blade.php
git commit -m "feat: boton de inscribirse en la vista publica del torneo"
```

---

## Task 12: Toggle auto-aceptar en perfil del jugador

**Files:**
- Modify: `resources/views/jugador/perfil.blade.php`
- Modify: `app/Http/Controllers/Jugador/PerfilController.php`

- [ ] **Step 1: Actualizar PerfilController**

En `app/Http/Controllers/Jugador/PerfilController.php`, en el método `update()`, agregar en el array de validación:

```php
'auto_aceptar_invitaciones' => ['boolean'],
```

Y en el bloque `if ($jugador)`, agregar al array del `update()`:

```php
'auto_aceptar_invitaciones' => $request->boolean('auto_aceptar_invitaciones'),
```

- [ ] **Step 2: Agregar toggle en la vista de perfil**

En `resources/views/jugador/perfil.blade.php`, buscar la sección del formulario principal de datos del jugador y agregar antes del botón de guardar:

```blade
{{-- Toggle auto-aceptar invitaciones --}}
<div class="flex items-center justify-between py-3 border-t border-gray-100">
    <div>
        <p class="text-sm font-medium text-gray-900">Auto-aceptar invitaciones</p>
        <p class="text-xs text-gray-500 mt-0.5">Aceptar automáticamente invitaciones de jugadores con quienes ya jugaste.</p>
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="auto_aceptar_invitaciones" value="1"
               {{ $jugador?->auto_aceptar_invitaciones ? 'checked' : '' }}
               class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
    </label>
</div>
```

- [ ] **Step 3: Formatear y commit**

```bash
vendor/bin/pint --dirty
git add resources/views/jugador/perfil.blade.php app/Http/Controllers/Jugador/PerfilController.php
git commit -m "feat: toggle auto-aceptar invitaciones en perfil del jugador"
```

---

## Task 13: Notificar jugadores cuando el organizador elimina un equipo

**Files:**
- Modify: `app/Http/Controllers/TorneoEquipoController.php`

- [ ] **Step 1: Actualizar el método destroy en TorneoEquipoController**

En `app/Http/Controllers/TorneoEquipoController.php`, en el método `destroy()`, buscar la línea:

```php
$equipo->delete();
```

Reemplazar con:

```php
// Notificar jugadores si había inscripción asociada
$inscripcion = \App\Models\InscripcionEquipo::where('equipo_id', $equipo->id)->first();

if ($inscripcion) {
    app(\App\Services\InscripcionService::class)->cancelarInscripcion($inscripcion, 'organizador');
} else {
    // Equipo creado manualmente: notificar igualmente si los jugadores tienen usuario
    $jugadores = $equipo->jugadores()->whereNotNull('user_id')->with('user')->get();
    foreach ($jugadores as $jugador) {
        if ($jugador->user) {
            $jugador->user->notify(new \App\Notifications\InscripcionCanceladaNotification(
                new \App\Models\InscripcionEquipo([
                    'torneo_id' => $equipo->torneo_id,
                    'cancelado_por' => 'organizador',
                ])
            ));
        }
    }
}

$equipo->delete();
```

- [ ] **Step 2: Ejecutar suite completa de tests**

```bash
php artisan test --no-interaction
```

Esperado: todos los tests pasan.

- [ ] **Step 3: Formatear y commit final**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/TorneoEquipoController.php
git commit -m "feat: notificar jugadores cuando el organizador elimina un equipo del torneo"
```

---

## Verificación final

- [ ] Ejecutar `php artisan route:list --path=inscripciones` y verificar que aparecen todas las rutas
- [ ] Verificar que los assets del frontend compilan: `npm run build`
- [ ] Probar manualmente el flujo completo en el navegador como jugador con perfil
- [ ] Ejecutar suite completa: `php artisan test --no-interaction`
