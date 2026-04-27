# Integración DUPR Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Integrar la API DUPR RaaS en PickleTorneos para que los jugadores puedan vincular su cuenta DUPR, los organizadores puedan crear torneos con DUPR requerido (con restricciones de rating por categoría), y los resultados de partidos se sincronicen automáticamente con DUPR.

**Architecture:** Un `DuprService` centraliza todas las llamadas a la API DUPR usando server-to-server token (cacheado 55 min). Los jugadores vinculan su cuenta via búsqueda por nombre + confirmación (no entrada manual de DUPR ID). Al cargar un resultado en un torneo con `dupr_requerido=true`, se despacha `SincronizarResultadoDuprJob` que envía el resultado de forma asíncrona y resiliente.

**Tech Stack:** Laravel 10, Laravel HTTP Client (`Http::` facade), Laravel Cache, Laravel Queue (database), Tailwind CSS, Alpine.js

---

## API DUPR — Referencia rápida

- **Base URL UAT:** `https://uat.mydupr.com`
- **Auth:** `POST /api/auth/v1.0/token` con header `x-authorization: base64(ClientKey:ClientSecret)`
- **Token response:** `result.token` (válido ~1 hora)
- **Requests autenticados:** header `Authorization: Bearer {token}`
- **Buscar jugador:** `POST /api/user/v1.0/search` → `result.hits[].duprId`, `result.hits[].fullName`, `result.hits[].singlesRating`, `result.hits[].doublesRating`
- **Ver jugador:** `GET /api/user/v1.0/{duprId}` → `result.duprId`, `result.singlesRating`, `result.doublesRating`
- **Crear partido:** `POST /api/match/v1.0/create` → `result.matchCode`

---

## Mapa de archivos

### Crear
| Archivo | Responsabilidad |
|---|---|
| `app/Services/DuprService.php` | Toda la comunicación con la API DUPR |
| `app/Http/Controllers/DuprController.php` | Buscar/vincular/desconectar DUPR desde perfil |
| `app/Jobs/SincronizarResultadoDuprJob.php` | Envío async de resultados a DUPR |
| `database/migrations/..._add_dupr_fields_to_jugadores_table.php` | `dupr_id`, `rating_singles`, `rating_doubles`, `dupr_sincronizado_at` |
| `database/migrations/..._add_dupr_token_to_users_table.php` | `dupr_access_token`, `dupr_token_expires_at` |
| `database/migrations/..._add_dupr_requerido_to_torneos_table.php` | `dupr_requerido` |
| `database/migrations/..._add_dupr_rating_to_categoria_torneo_table.php` | `dupr_rating_min`, `dupr_rating_max` |
| `database/migrations/..._add_dupr_fields_to_partidos_table.php` | `dupr_partido_id`, `dupr_sincronizado`, `dupr_sincronizado_at`, `dupr_error` |
| `tests/Feature/DuprServiceTest.php` | Tests del servicio DUPR |
| `tests/Feature/DuprInscripcionValidationTest.php` | Tests de validaciones DUPR en inscripción |
| `tests/Feature/DuprJobTest.php` | Tests del job de sincronización |

### Modificar
| Archivo | Qué cambia |
|---|---|
| `config/services.php` | Agregar bloque `dupr` |
| `.env` / `.env.example` | Agregar variables DUPR |
| `app/Models/Jugador.php` | Agregar `dupr_id`, `rating_singles`, `rating_doubles` al `$fillable` |
| `app/Models/User.php` | Agregar `dupr_access_token`, `dupr_token_expires_at` al `$fillable` y `$casts` |
| `app/Models/Torneo.php` | Agregar `dupr_requerido` al `$fillable` y `$casts` |
| `app/Models/Partido.php` | Agregar campos DUPR al `$fillable` y `$casts` |
| `app/Services/InscripcionService.php` | Agregar validaciones DUPR en `validarCondicionesJugador` y `jugadorCumpleCondiciones` |
| `app/Http/Controllers/TorneoController.php` | Agregar `dupr_requerido` y `dupr_rating_min/max` en `storeStep2` |
| `app/Http/Controllers/TorneoFixtureController.php` | Dispatch job en `cargarResultado` |
| `app/Http/Controllers/TorneoLlaveController.php` | Dispatch job en `cargarResultado` |
| `routes/web.php` | Rutas DUPR |
| `resources/views/jugador/perfil.blade.php` | Sección "Cuenta DUPR" |
| `resources/views/torneos/create-step2.blade.php` | Toggle DUPR + campos rating por categoría |

---

## Task 1: Configuración y Migraciones

**Files:**
- Modify: `config/services.php`
- Modify: `.env`
- Modify: `.env.example`
- Create: 5 migration files

- [ ] **Step 1: Agregar bloque DUPR a `config/services.php`**

Abrir `config/services.php` y agregar al final del array `return [...]`, antes del cierre `]`:

```php
    'dupr' => [
        'base_url' => env('DUPR_BASE_URL', 'https://uat.mydupr.com'),
        'client_key' => env('DUPR_CLIENT_KEY'),
        'client_secret' => env('DUPR_CLIENT_SECRET'),
    ],
```

- [ ] **Step 2: Agregar variables al `.env`**

Agregar al final de `.env`:
```
DUPR_BASE_URL=https://uat.mydupr.com
DUPR_CLIENT_KEY=test-ck-ab5bfcaa-8a25-4bff-ff71-63604d7dd806
DUPR_CLIENT_SECRET=test-cs-446890c8cf3f41e2f9031a44f383348a
```

Agregar también al `.env.example`:
```
DUPR_BASE_URL=https://uat.mydupr.com
DUPR_CLIENT_KEY=
DUPR_CLIENT_SECRET=
```

- [ ] **Step 3: Crear migraciones**

```bash
php artisan make:migration add_dupr_fields_to_jugadores_table --no-interaction
php artisan make:migration add_dupr_token_to_users_table --no-interaction
php artisan make:migration add_dupr_requerido_to_torneos_table --no-interaction
php artisan make:migration add_dupr_rating_to_categoria_torneo_table --no-interaction
php artisan make:migration add_dupr_fields_to_partidos_table --no-interaction
```

- [ ] **Step 4: Completar migración `add_dupr_fields_to_jugadores_table`**

```php
public function up(): void
{
    Schema::table('jugadores', function (Blueprint $table) {
        $table->string('dupr_id', 10)->nullable()->unique()->after('ranking');
        $table->decimal('rating_singles', 4, 2)->nullable()->after('dupr_id');
        $table->decimal('rating_doubles', 4, 2)->nullable()->after('rating_singles');
        $table->timestamp('dupr_sincronizado_at')->nullable()->after('rating_doubles');
    });
}

public function down(): void
{
    Schema::table('jugadores', function (Blueprint $table) {
        $table->dropColumn(['dupr_id', 'rating_singles', 'rating_doubles', 'dupr_sincronizado_at']);
    });
}
```

- [ ] **Step 5: Completar migración `add_dupr_token_to_users_table`**

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->text('dupr_access_token')->nullable()->after('google_id');
        $table->timestamp('dupr_token_expires_at')->nullable()->after('dupr_access_token');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['dupr_access_token', 'dupr_token_expires_at']);
    });
}
```

- [ ] **Step 6: Completar migración `add_dupr_requerido_to_torneos_table`**

```php
public function up(): void
{
    Schema::table('torneos', function (Blueprint $table) {
        $table->boolean('dupr_requerido')->default(false)->after('reglamento');
    });
}

public function down(): void
{
    Schema::table('torneos', function (Blueprint $table) {
        $table->dropColumn('dupr_requerido');
    });
}
```

- [ ] **Step 7: Completar migración `add_dupr_rating_to_categoria_torneo_table`**

```php
public function up(): void
{
    Schema::table('categoria_torneo', function (Blueprint $table) {
        $table->decimal('dupr_rating_min', 4, 2)->nullable()->after('genero_permitido');
        $table->decimal('dupr_rating_max', 4, 2)->nullable()->after('dupr_rating_min');
    });
}

public function down(): void
{
    Schema::table('categoria_torneo', function (Blueprint $table) {
        $table->dropColumn(['dupr_rating_min', 'dupr_rating_max']);
    });
}
```

- [ ] **Step 8: Completar migración `add_dupr_fields_to_partidos_table`**

```php
public function up(): void
{
    Schema::table('partidos', function (Blueprint $table) {
        $table->string('dupr_partido_id')->nullable()->after('ultima_notificacion');
        $table->boolean('dupr_sincronizado')->default(false)->after('dupr_partido_id');
        $table->timestamp('dupr_sincronizado_at')->nullable()->after('dupr_sincronizado');
        $table->text('dupr_error')->nullable()->after('dupr_sincronizado_at');
    });
}

public function down(): void
{
    Schema::table('partidos', function (Blueprint $table) {
        $table->dropColumn(['dupr_partido_id', 'dupr_sincronizado', 'dupr_sincronizado_at', 'dupr_error']);
    });
}
```

- [ ] **Step 9: Ejecutar migraciones**

```bash
php artisan migrate --no-interaction
```

Expected: `5 migrations run successfully`

- [ ] **Step 10: Actualizar modelos con nuevos campos**

En `app/Models/Jugador.php`, agregar al `$fillable`:
```php
'dupr_id',
'rating_singles',
'rating_doubles',
'dupr_sincronizado_at',
```

En `app/Models/User.php`, agregar al `$fillable`:
```php
'dupr_access_token',
'dupr_token_expires_at',
```
Y al `$casts`:
```php
'dupr_token_expires_at' => 'datetime',
```

En `app/Models/Torneo.php`, agregar al `$fillable`:
```php
'dupr_requerido',
```
Y al `$casts`:
```php
'dupr_requerido' => 'boolean',
```

En `app/Models/Partido.php`, agregar al `$fillable`:
```php
'dupr_partido_id',
'dupr_sincronizado',
'dupr_sincronizado_at',
'dupr_error',
```
Y al `$casts`:
```php
'dupr_sincronizado' => 'boolean',
'dupr_sincronizado_at' => 'datetime',
```

- [ ] **Step 11: Commit**

```bash
git add database/migrations/ config/services.php .env.example app/Models/
git commit -m "feat: migraciones y config para integración DUPR"
```

---

## Task 2: DuprService — Token y búsqueda de jugadores

**Files:**
- Create: `app/Services/DuprService.php`
- Create: `tests/Feature/DuprServiceTest.php`

- [ ] **Step 1: Crear el archivo de test**

```bash
php artisan make:test DuprServiceTest --no-interaction
```

- [ ] **Step 2: Escribir tests del DuprService**

Reemplazar el contenido de `tests/Feature/DuprServiceTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Services\DuprService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DuprServiceTest extends TestCase
{
    use RefreshDatabase;

    private DuprService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new DuprService();
    }

    public function test_obtener_token_retorna_token_del_servidor(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-bearer-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
        ]);

        $token = $this->service->obtenerToken();

        $this->assertEquals('test-bearer-token', $token);
    }

    public function test_obtener_token_usa_cache_en_segunda_llamada(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'cached-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
        ]);

        $this->service->obtenerToken();
        $this->service->obtenerToken();

        Http::assertSentCount(1);
    }

    public function test_buscar_jugadores_retorna_array_de_resultados(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
            'uat.mydupr.com/api/user/v1.0/search' => Http::response([
                'status' => 'SUCCESS',
                'result' => [
                    'hits' => [
                        ['duprId' => 'ABC123', 'fullName' => 'Juan Perez', 'singlesRating' => 4.5, 'doublesRating' => 5.0],
                    ],
                    'total' => 1,
                ],
            ], 200),
        ]);

        $results = $this->service->buscarJugadores('Juan Perez');

        $this->assertCount(1, $results);
        $this->assertEquals('ABC123', $results[0]['duprId']);
        $this->assertEquals('Juan Perez', $results[0]['fullName']);
        $this->assertEquals(4.5, $results[0]['singlesRating']);
        $this->assertEquals(5.0, $results[0]['doublesRating']);
    }

    public function test_buscar_jugadores_retorna_array_vacio_si_no_hay_resultados(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
            'uat.mydupr.com/api/user/v1.0/search' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['hits' => [], 'total' => 0],
            ], 200),
        ]);

        $results = $this->service->buscarJugadores('Nombre Inexistente');

        $this->assertEmpty($results);
    }

    public function test_obtener_rating_jugador_retorna_ratings(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
            'uat.mydupr.com/api/user/v1.0/ABC123' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['duprId' => 'ABC123', 'fullName' => 'Juan Perez', 'singlesRating' => 4.5, 'doublesRating' => 5.0],
            ], 200),
        ]);

        $rating = $this->service->obtenerRatingJugador('ABC123');

        $this->assertEquals(4.5, $rating['singles']);
        $this->assertEquals(5.0, $rating['doubles']);
    }

    public function test_obtener_rating_jugador_retorna_null_si_no_existe(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
            'uat.mydupr.com/api/user/v1.0/NOEXIST' => Http::response(['status' => 'NOT_FOUND'], 404),
        ]);

        $rating = $this->service->obtenerRatingJugador('NOEXIST');

        $this->assertNull($rating);
    }
}
```

- [ ] **Step 3: Ejecutar tests para verificar que fallan**

```bash
php artisan test tests/Feature/DuprServiceTest.php --no-interaction
```

Expected: FAIL — `DuprService not found`

- [ ] **Step 4: Crear `app/Services/DuprService.php`**

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuprService
{
    private string $baseUrl;
    private string $clientKey;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.dupr.base_url');
        $this->clientKey = config('services.dupr.client_key');
        $this->clientSecret = config('services.dupr.client_secret');
    }

    public function obtenerToken(): string
    {
        return Cache::remember('dupr_server_token', now()->addMinutes(55), function () {
            $credentials = base64_encode("{$this->clientKey}:{$this->clientSecret}");

            $response = Http::withHeaders([
                'x-authorization' => $credentials,
                'accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/auth/v1.0/token");

            if (! $response->successful()) {
                throw new \RuntimeException('No se pudo obtener token DUPR: ' . $response->body());
            }

            return $response->json('result.token');
        });
    }

    public function buscarJugadores(string $query, int $limit = 10): array
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/api/user/v1.0/search", [
                'query' => $query,
                'offset' => 0,
                'limit' => $limit,
            ]);

        if (! $response->successful()) {
            Log::warning('DUPR búsqueda fallida', ['query' => $query, 'status' => $response->status()]);
            return [];
        }

        return $response->json('result.hits', []);
    }

    public function obtenerRatingJugador(string $duprId): ?array
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/api/user/v1.0/{$duprId}");

        if (! $response->successful()) {
            return null;
        }

        $result = $response->json('result');

        return [
            'singles' => $result['singlesRating'] ?? null,
            'doubles' => $result['doublesRating'] ?? null,
        ];
    }

    public function crearPartido(array $payload): ?string
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/api/match/v1.0/create", $payload);

        if (! $response->successful()) {
            Log::error('DUPR creación de partido fallida', [
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json('result.matchCode');
    }
}
```

- [ ] **Step 5: Ejecutar tests para verificar que pasan**

```bash
php artisan test tests/Feature/DuprServiceTest.php --no-interaction
```

Expected: 5 passed

- [ ] **Step 6: Commit**

```bash
git add app/Services/DuprService.php tests/Feature/DuprServiceTest.php
git commit -m "feat: DuprService con token, búsqueda y rating"
```

---

## Task 3: DuprController — Vincular y desconectar cuenta DUPR

**Files:**
- Create: `app/Http/Controllers/DuprController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Crear el controlador**

```bash
php artisan make:controller DuprController --no-interaction
```

- [ ] **Step 2: Implementar `DuprController`**

Reemplazar el contenido de `app/Http/Controllers/DuprController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\DuprService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DuprController extends Controller
{
    public function __construct(private DuprService $duprService) {}

    public function buscar(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:3|max:100']);

        $resultados = $this->duprService->buscarJugadores($request->q);

        return response()->json(['hits' => $resultados]);
    }

    public function vincular(Request $request): RedirectResponse
    {
        $request->validate(['dupr_id' => 'required|string|max:10']);

        $user = auth()->user();
        $jugador = $user->jugador;

        if (! $jugador) {
            return redirect()->route('jugador.perfil')
                ->with('error', 'No se encontró perfil de jugador.');
        }

        $rating = $this->duprService->obtenerRatingJugador($request->dupr_id);

        if ($rating === null) {
            return redirect()->route('jugador.perfil')
                ->with('error', 'No se encontró ese DUPR ID en el sistema DUPR. Verificá que hayas seleccionado el perfil correcto.');
        }

        $jugador->update([
            'dupr_id' => $request->dupr_id,
            'rating_singles' => $rating['singles'],
            'rating_doubles' => $rating['doubles'],
            'dupr_sincronizado_at' => now(),
        ]);

        return redirect()->route('jugador.perfil')
            ->with('success_dupr', '¡Cuenta DUPR vinculada! Rating dobles: ' . ($rating['doubles'] ?? 'N/A'));
    }

    public function desconectar(): RedirectResponse
    {
        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            return redirect()->route('jugador.perfil');
        }

        $jugador->update([
            'dupr_id' => null,
            'rating_singles' => null,
            'rating_doubles' => null,
            'dupr_sincronizado_at' => null,
        ]);

        auth()->user()->update([
            'dupr_access_token' => null,
            'dupr_token_expires_at' => null,
        ]);

        return redirect()->route('jugador.perfil')
            ->with('success_dupr', 'Cuenta DUPR desvinculada.');
    }
}
```

- [ ] **Step 3: Agregar rutas DUPR en `routes/web.php`**

Dentro del grupo `Route::middleware('auth')->prefix('jugador')->name('jugador.')->group(...)`, agregar luego de las rutas de perfil existentes (después de la línea `Route::post('/perfil/foto', ...)`):

```php
    // Integración DUPR
    Route::get('/dupr/buscar', [\App\Http\Controllers\DuprController::class, 'buscar'])->name('dupr.buscar');
    Route::post('/dupr/vincular', [\App\Http\Controllers\DuprController::class, 'vincular'])->name('dupr.vincular');
    Route::post('/dupr/desconectar', [\App\Http\Controllers\DuprController::class, 'desconectar'])->name('dupr.desconectar');
```

- [ ] **Step 4: Verificar que las rutas existen**

```bash
php artisan route:list --path=dupr --no-interaction
```

Expected: 3 rutas listadas (`jugador.dupr.buscar`, `jugador.dupr.vincular`, `jugador.dupr.desconectar`)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/DuprController.php routes/web.php
git commit -m "feat: DuprController para vincular/desconectar cuenta DUPR"
```

---

## Task 4: SincronizarResultadoDuprJob

**Files:**
- Create: `app/Jobs/SincronizarResultadoDuprJob.php`
- Create: `tests/Feature/DuprJobTest.php`

- [ ] **Step 1: Crear el job**

```bash
php artisan make:job SincronizarResultadoDuprJob --no-interaction
```

- [ ] **Step 2: Crear el test del job**

```bash
php artisan make:test DuprJobTest --no-interaction
```

- [ ] **Step 3: Escribir el test en `tests/Feature/DuprJobTest.php`**

```php
<?php

namespace Tests\Feature;

use App\Jobs\SincronizarResultadoDuprJob;
use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\Equipo;
use App\Models\FormatoTorneo;
use App\Models\Juego;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DuprJobTest extends TestCase
{
    use RefreshDatabase;

    private Partido $partido;
    private Torneo $torneo;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Organizador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $deporte = Deporte::create(['nombre' => 'Pickleball', 'slug' => 'pickleball']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'direccion' => 'Calle 123',
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo = Torneo::create([
            'nombre' => 'Torneo DUPR',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'en_curso',
            'dupr_requerido' => true,
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $categoria = Categoria::create(['nombre' => 'Mixto', 'organizador_id' => $organizador->id, 'deporte_id' => $deporte->id]);

        $jugador1 = Jugador::factory()->create(['dupr_id' => 'AA1111']);
        $jugador2 = Jugador::factory()->create(['dupr_id' => 'BB2222']);
        $jugador3 = Jugador::factory()->create(['dupr_id' => 'CC3333']);
        $jugador4 = Jugador::factory()->create(['dupr_id' => 'DD4444']);

        $equipo1 = Equipo::create(['nombre' => 'Equipo A', 'torneo_id' => $this->torneo->id, 'categoria_id' => $categoria->id]);
        $equipo1->jugadores()->attach([$jugador1->id => ['orden' => 1], $jugador2->id => ['orden' => 2]]);

        $equipo2 = Equipo::create(['nombre' => 'Equipo B', 'torneo_id' => $this->torneo->id, 'categoria_id' => $categoria->id]);
        $equipo2->jugadores()->attach([$jugador3->id => ['orden' => 1], $jugador4->id => ['orden' => 2]]);

        $this->partido = Partido::create([
            'equipo1_id' => $equipo1->id,
            'equipo2_id' => $equipo2->id,
            'estado' => 'finalizado',
            'sets_equipo1' => 2,
            'sets_equipo2' => 1,
            'equipo_ganador_id' => $equipo1->id,
            'fecha_hora' => now()->subDay(),
        ]);

        Juego::create(['partido_id' => $this->partido->id, 'numero_juego' => 1, 'juegos_equipo1' => 11, 'juegos_equipo2' => 8, 'orden' => 1]);
        Juego::create(['partido_id' => $this->partido->id, 'numero_juego' => 2, 'juegos_equipo1' => 7, 'juegos_equipo2' => 11, 'orden' => 2]);
        Juego::create(['partido_id' => $this->partido->id, 'numero_juego' => 3, 'juegos_equipo1' => 11, 'juegos_equipo2' => 9, 'orden' => 3]);
    }

    private function fakeTokenResponse(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
        ]);
    }

    public function test_job_sincroniza_resultado_con_dupr(): void
    {
        Http::fake([
            'uat.mydupr.com/api/auth/v1.0/token' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['token' => 'test-token', 'expiry' => now()->addHour()->toISOString()],
            ], 200),
            'uat.mydupr.com/api/match/v1.0/create' => Http::response([
                'status' => 'SUCCESS',
                'result' => ['identifier' => 'pt-' . $this->partido->id, 'matchCode' => 'MATCH_CODE_123'],
            ], 200),
        ]);

        (new SincronizarResultadoDuprJob($this->partido->id))->handle(new \App\Services\DuprService());

        $this->partido->refresh();
        $this->assertTrue($this->partido->dupr_sincronizado);
        $this->assertEquals('MATCH_CODE_123', $this->partido->dupr_partido_id);
        $this->assertNotNull($this->partido->dupr_sincronizado_at);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === 'https://uat.mydupr.com/api/match/v1.0/create'
                && $body['matchFormat'] === 'DOUBLES'
                && $body['teamA']['player1'] === 'AA1111'
                && $body['teamA']['player2'] === 'BB2222'
                && $body['teamB']['player1'] === 'CC3333'
                && $body['teamB']['player2'] === 'DD4444'
                && $body['teamA']['game1'] === 11
                && $body['teamB']['game1'] === 8;
        });
    }

    public function test_job_guarda_error_si_algun_jugador_no_tiene_dupr_id(): void
    {
        $this->partido->equipo1->jugadores()->first()->update(['dupr_id' => null]);

        (new SincronizarResultadoDuprJob($this->partido->id))->handle(new \App\Services\DuprService());

        $this->partido->refresh();
        $this->assertFalse($this->partido->dupr_sincronizado);
        $this->assertNotNull($this->partido->dupr_error);
        Http::assertNothingSent();
    }

    public function test_job_no_reintenta_si_partido_ya_esta_sincronizado(): void
    {
        $this->partido->update(['dupr_sincronizado' => true, 'dupr_partido_id' => 'EXISTENTE']);

        (new SincronizarResultadoDuprJob($this->partido->id))->handle(new \App\Services\DuprService());

        $this->partido->refresh();
        $this->assertEquals('EXISTENTE', $this->partido->dupr_partido_id);
        Http::assertNothingSent();
    }
}
```

- [ ] **Step 4: Verificar que los tests fallan**

```bash
php artisan test tests/Feature/DuprJobTest.php --no-interaction
```

Expected: FAIL — `SincronizarResultadoDuprJob has no method handle`

- [ ] **Step 5: Implementar `SincronizarResultadoDuprJob`**

Reemplazar el contenido de `app/Jobs/SincronizarResultadoDuprJob.php`:

```php
<?php

namespace App\Jobs;

use App\Models\Partido;
use App\Services\DuprService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SincronizarResultadoDuprJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [60, 300, 900];

    public function __construct(public int $partidoId) {}

    public function handle(DuprService $duprService): void
    {
        $partido = Partido::with([
            'equipo1.jugadores' => fn ($q) => $q->orderBy('equipo_jugador.orden'),
            'equipo2.jugadores' => fn ($q) => $q->orderBy('equipo_jugador.orden'),
            'juegos' => fn ($q) => $q->orderBy('orden'),
        ])->find($this->partidoId);

        if (! $partido || $partido->dupr_sincronizado) {
            return;
        }

        $jugadoresA = $partido->equipo1->jugadores;
        $jugadoresB = $partido->equipo2->jugadores;

        $duprIdA1 = $jugadoresA->get(0)?->dupr_id;
        $duprIdA2 = $jugadoresA->get(1)?->dupr_id;
        $duprIdB1 = $jugadoresB->get(0)?->dupr_id;
        $duprIdB2 = $jugadoresB->get(1)?->dupr_id;

        if (! $duprIdA1 || ! $duprIdA2 || ! $duprIdB1 || ! $duprIdB2) {
            $partido->update(['dupr_error' => 'Uno o más jugadores no tienen DUPR ID asociado.']);
            return;
        }

        $teamA = ['player1' => $duprIdA1, 'player2' => $duprIdA2];
        $teamB = ['player1' => $duprIdB1, 'player2' => $duprIdB2];

        foreach ($partido->juegos as $index => $juego) {
            $key = 'game' . ($index + 1);
            $teamA[$key] = $juego->juegos_equipo1;
            $teamB[$key] = $juego->juegos_equipo2;
        }

        $payload = [
            'identifier' => 'pt-' . $partido->id,
            'matchDate' => $partido->fecha_hora->format('Y-m-d'),
            'matchFormat' => 'DOUBLES',
            'source' => 'PARTNER',
            'teamA' => $teamA,
            'teamB' => $teamB,
        ];

        $matchCode = $duprService->crearPartido($payload);

        if ($matchCode) {
            $partido->update([
                'dupr_partido_id' => $matchCode,
                'dupr_sincronizado' => true,
                'dupr_sincronizado_at' => now(),
                'dupr_error' => null,
            ]);
        } else {
            throw new \RuntimeException("DUPR no devolvió matchCode para partido {$this->partidoId}");
        }
    }
}
```

- [ ] **Step 6: Verificar que el test de Jugador factory existe (o crearlo si falta)**

```bash
php artisan make:factory JugadorFactory --model=Jugador --no-interaction 2>/dev/null || true
```

Si el factory no existe, crear `database/factories/JugadorFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JugadorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'genero' => $this->faker->randomElement(['masculino', 'femenino']),
        ];
    }
}
```

Y agregar `use HasFactory;` al modelo `Jugador` si no lo tiene (ya lo tiene).

- [ ] **Step 7: Ejecutar tests del job**

```bash
php artisan test tests/Feature/DuprJobTest.php --no-interaction
```

Expected: 3 passed

- [ ] **Step 8: Commit**

```bash
git add app/Jobs/SincronizarResultadoDuprJob.php tests/Feature/DuprJobTest.php database/factories/
git commit -m "feat: SincronizarResultadoDuprJob para envío async de resultados"
```

---

## Task 5: Validaciones DUPR en InscripcionService

**Files:**
- Modify: `app/Services/InscripcionService.php`
- Create: `tests/Feature/DuprInscripcionValidationTest.php`

- [ ] **Step 1: Crear el test**

```bash
php artisan make:test DuprInscripcionValidationTest --no-interaction
```

- [ ] **Step 2: Escribir los tests en `tests/Feature/DuprInscripcionValidationTest.php`**

```php
<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use App\Services\InscripcionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DuprInscripcionValidationTest extends TestCase
{
    use RefreshDatabase;

    private InscripcionService $service;
    private Torneo $torneoConDupr;
    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Organizador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $this->service = new InscripcionService();

        $deporte = Deporte::create(['nombre' => 'Pickleball', 'slug' => 'pickleball']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'direccion' => 'Calle 123',
            'organizador_id' => $organizador->id,
        ]);

        $this->torneoConDupr = Torneo::create([
            'nombre' => 'Torneo DUPR',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'dupr_requerido' => true,
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Mixto',
            'organizador_id' => $organizador->id,
            'deporte_id' => $deporte->id,
        ]);

        $this->torneoConDupr->categorias()->attach($this->categoria->id, [
            'cupos_categoria' => 8,
            'dupr_rating_min' => null,
            'dupr_rating_max' => null,
        ]);
    }

    public function test_jugador_sin_dupr_id_no_puede_inscribirse_en_torneo_dupr_requerido(): void
    {
        $jugador = Jugador::factory()->create(['dupr_id' => null]);

        $categoriaConPivot = $this->torneoConDupr->categorias()
            ->where('categorias.id', $this->categoria->id)
            ->first();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Este torneo requiere vincular tu cuenta DUPR');

        $this->service->validarCondicionesJugador($jugador, $categoriaConPivot, $this->torneoConDupr);
    }

    public function test_jugador_con_dupr_id_puede_inscribirse_en_torneo_dupr_requerido(): void
    {
        $jugador = Jugador::factory()->create(['dupr_id' => 'ABC123', 'rating_doubles' => 4.5]);

        $categoriaConPivot = $this->torneoConDupr->categorias()
            ->where('categorias.id', $this->categoria->id)
            ->first();

        $this->service->validarCondicionesJugador($jugador, $categoriaConPivot, $this->torneoConDupr);

        $this->assertTrue(true); // No lanzó excepción
    }

    public function test_jugador_con_rating_bajo_no_cumple_rating_minimo(): void
    {
        $this->torneoConDupr->categorias()->syncWithoutDetaching([
            $this->categoria->id => ['cupos_categoria' => 8, 'dupr_rating_min' => 4.0, 'dupr_rating_max' => null],
        ]);

        $jugador = Jugador::factory()->create(['dupr_id' => 'ABC123', 'rating_doubles' => 3.5]);

        $categoriaConPivot = $this->torneoConDupr->categorias()
            ->where('categorias.id', $this->categoria->id)
            ->first();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('3.50');

        $this->service->validarCondicionesJugador($jugador, $categoriaConPivot, $this->torneoConDupr);
    }

    public function test_jugador_con_rating_alto_no_cumple_rating_maximo(): void
    {
        $this->torneoConDupr->categorias()->syncWithoutDetaching([
            $this->categoria->id => ['cupos_categoria' => 8, 'dupr_rating_min' => null, 'dupr_rating_max' => 4.0],
        ]);

        $jugador = Jugador::factory()->create(['dupr_id' => 'ABC123', 'rating_doubles' => 5.5]);

        $categoriaConPivot = $this->torneoConDupr->categorias()
            ->where('categorias.id', $this->categoria->id)
            ->first();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('5.50');

        $this->service->validarCondicionesJugador($jugador, $categoriaConPivot, $this->torneoConDupr);
    }

    public function test_torneo_sin_dupr_requerido_no_valida_dupr_id(): void
    {
        $torneoSinDupr = Torneo::create([
            'nombre' => 'Torneo Normal',
            'deporte_id' => $this->torneoConDupr->deporte_id,
            'complejo_id' => $this->torneoConDupr->complejo_id,
            'organizador_id' => $this->torneoConDupr->organizador_id,
            'formato_id' => $this->torneoConDupr->formato_id,
            'estado' => 'activo',
            'dupr_requerido' => false,
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $torneoSinDupr->categorias()->attach($this->categoria->id, ['cupos_categoria' => 8]);

        $jugador = Jugador::factory()->create(['dupr_id' => null]);

        $categoriaConPivot = $torneoSinDupr->categorias()
            ->where('categorias.id', $this->categoria->id)
            ->first();

        $this->service->validarCondicionesJugador($jugador, $categoriaConPivot, $torneoSinDupr);

        $this->assertTrue(true); // No lanzó excepción
    }
}
```

- [ ] **Step 3: Ejecutar para verificar que fallan**

```bash
php artisan test tests/Feature/DuprInscripcionValidationTest.php --no-interaction
```

Expected: FAIL — validaciones DUPR no implementadas todavía

- [ ] **Step 4: Refactorizar firma de `validarCondicionesJugador` y `jugadorCumpleCondiciones` para aceptar `Torneo`**

Abrir `app/Services/InscripcionService.php`.

**Cambio 1:** Actualizar la firma del método `validarCondicionesJugador` (línea ~123):

```php
public function validarCondicionesJugador(Jugador $jugador, Categoria $categoriaConPivot, ?Torneo $torneo = null): void
```

Al final del método, agregar ANTES del cierre `}`:

```php
        if ($torneo && $torneo->dupr_requerido) {
            if (! $jugador->dupr_id) {
                throw new \RuntimeException('Este torneo requiere vincular tu cuenta DUPR. Podés hacerlo desde tu perfil.');
            }

            $ratingMin = $categoriaConPivot->pivot->dupr_rating_min ?? null;
            $ratingMax = $categoriaConPivot->pivot->dupr_rating_max ?? null;

            if ($ratingMin !== null || $ratingMax !== null) {
                $rating = $jugador->rating_doubles;

                if ($rating === null) {
                    throw new \RuntimeException('Tu cuenta DUPR no tiene rating de dobles registrado aún.');
                }

                if ($ratingMin !== null && $rating < $ratingMin) {
                    throw new \RuntimeException(sprintf(
                        'Tu rating DUPR (%.2f) es menor al mínimo requerido (%.2f) para esta categoría.',
                        $rating,
                        $ratingMin
                    ));
                }

                if ($ratingMax !== null && $rating > $ratingMax) {
                    throw new \RuntimeException(sprintf(
                        'Tu rating DUPR (%.2f) supera el máximo permitido (%.2f) para esta categoría.',
                        $rating,
                        $ratingMax
                    ));
                }
            }
        }
```

**Cambio 2:** Actualizar la firma de `jugadorCumpleCondiciones` (línea ~365):

```php
private function jugadorCumpleCondiciones(Jugador $jugador, Categoria $categoriaConPivot, ?Torneo $torneo = null): bool
```

Al final del método, agregar ANTES del `return true;` final:

```php
        if ($torneo && $torneo->dupr_requerido) {
            if (! $jugador->dupr_id) {
                return false;
            }

            $ratingMin = $categoriaConPivot->pivot->dupr_rating_min ?? null;
            $ratingMax = $categoriaConPivot->pivot->dupr_rating_max ?? null;

            if ($ratingMin !== null || $ratingMax !== null) {
                $rating = $jugador->rating_doubles;
                if ($rating === null) {
                    return false;
                }
                if ($ratingMin !== null && $rating < $ratingMin) {
                    return false;
                }
                if ($ratingMax !== null && $rating > $ratingMax) {
                    return false;
                }
            }
        }
```

**Cambio 3:** En el método `iniciarInscripcion`, la llamada a `validarCondicionesJugador` debe pasar el torneo. Buscar la línea:

```php
        $this->validarCondicionesJugador($lider, $categoriaConPivot);
```

Reemplazar por:

```php
        $this->validarCondicionesJugador($lider, $categoriaConPivot, $torneo);
```

**Cambio 4:** En `buscarJugadoresElegibles`, la llamada a `jugadorCumpleCondiciones` también debe pasar el torneo. Buscar:

```php
            return $this->jugadorCumpleCondiciones($jugador, $categoriaConPivot);
```

Reemplazar por:

```php
            return $this->jugadorCumpleCondiciones($jugador, $categoriaConPivot, $torneo);
```

Asegurarse de que el `use App\Models\Torneo;` esté en los imports del service (ya debe estar).

- [ ] **Step 5: Verificar la estructura de la tabla `categoria_torneo`**

```bash
php artisan tinker --no-interaction --execute="print_r(DB::getSchemaBuilder()->getColumnListing('categoria_torneo'));"
```

Expected: ver si `torneo_id` existe. Si no existe, hacer el refactor de la firma del método descrito en el paso anterior.

- [ ] **Step 6: Ejecutar tests de inscripción**

```bash
php artisan test tests/Feature/DuprInscripcionValidationTest.php --no-interaction
```

Expected: 5 passed

- [ ] **Step 7: Verificar que los tests existentes de InscripcionService siguen pasando**

```bash
php artisan test tests/Feature/InscripcionServiceTest.php --no-interaction
```

Expected: todos pasan (sin regresiones)

- [ ] **Step 8: Commit**

```bash
git add app/Services/InscripcionService.php tests/Feature/DuprInscripcionValidationTest.php
git commit -m "feat: validaciones DUPR en InscripcionService (dupr_id requerido + rating min/max)"
```

---

## Task 6: Wizard de torneo — dupr_requerido + rating por categoría + dispatch job

**Files:**
- Modify: `app/Http/Controllers/TorneoController.php`
- Modify: `app/Http/Controllers/TorneoFixtureController.php`
- Modify: `app/Http/Controllers/TorneoLlaveController.php`

- [ ] **Step 1: Agregar `dupr_requerido` a la validación de `storeStep2` en `TorneoController`**

En `app/Http/Controllers/TorneoController.php`, en el método `storeStep2`, agregar al array `$request->validate([...])` inicial:

```php
'dupr_requerido' => 'boolean',
'categorias.*.dupr_rating_min' => 'nullable|numeric|min:2|max:8',
'categorias.*.dupr_rating_max' => 'nullable|numeric|min:2|max:8',
```

Después de `$torneo->update(['formato_id' => $validated['formato_id']]);`, agregar:

```php
        $torneo->update(['dupr_requerido' => $request->boolean('dupr_requerido')]);
```

En el loop que construye `$syncData`, dentro de ambos bloques (`tiene_grupos` y `else`), agregar al array:

```php
                    'dupr_rating_min' => $categoriaData['dupr_rating_min'] ?? null,
                    'dupr_rating_max' => $categoriaData['dupr_rating_max'] ?? null,
```

- [ ] **Step 2: Dispatch job en `TorneoFixtureController::cargarResultado()`**

En `app/Http/Controllers/TorneoFixtureController.php`, agregar al inicio del archivo:

```php
use App\Jobs\SincronizarResultadoDuprJob;
```

Luego, en el método `cargarResultado`, después del `DB::commit();` y antes de `\App\Http\Controllers\TorneoController::intentarFinalizarAutomatico($torneo);`, agregar:

```php
            if ($torneo->dupr_requerido) {
                SincronizarResultadoDuprJob::dispatch($partido->id);
            }
```

- [ ] **Step 3: Dispatch job en `TorneoLlaveController::cargarResultado()`**

En `app/Http/Controllers/TorneoLlaveController.php`, agregar al inicio del archivo:

```php
use App\Jobs\SincronizarResultadoDuprJob;
```

En el método `cargarResultado`, después del `DB::commit();` y antes de `\App\Http\Controllers\TorneoController::intentarFinalizarAutomatico($torneo);`, agregar:

```php
            if ($torneo->dupr_requerido) {
                SincronizarResultadoDuprJob::dispatch($partido->id);
            }
```

- [ ] **Step 4: Verificar que los controladores compilan**

```bash
php artisan route:list --no-interaction > /dev/null && echo "OK"
```

Expected: `OK`

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/TorneoController.php app/Http/Controllers/TorneoFixtureController.php app/Http/Controllers/TorneoLlaveController.php
git commit -m "feat: wizard paso 2 con DUPR toggle + dispatch job en cargarResultado"
```

---

## Task 7: Vista — Sección DUPR en perfil del jugador

**Files:**
- Modify: `resources/views/jugador/perfil.blade.php`

- [ ] **Step 1: Agregar sección DUPR al perfil del jugador**

En `resources/views/jugador/perfil.blade.php`, agregar ANTES del cierre `@endsection`, una nueva sección de tarjeta. Buscar la última tarjeta/sección del perfil y agregar después:

```blade
{{-- Sección DUPR --}}
<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mt-6" x-data="{ buscando: false, resultados: [], query: '', cargando: false }">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
        Cuenta DUPR
    </h3>

    @if(session('success_dupr'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success_dupr') }}
        </div>
    @endif

    @if($jugador && $jugador->dupr_id)
        {{-- Cuenta vinculada --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">DUPR ID vinculado</p>
                <p class="font-mono font-bold text-gray-800 text-lg">{{ $jugador->dupr_id }}</p>
                <div class="flex gap-4 mt-2">
                    @if($jugador->rating_doubles)
                        <span class="text-sm text-gray-600">Dobles: <strong class="text-brand-600">{{ number_format($jugador->rating_doubles, 2) }}</strong></span>
                    @endif
                    @if($jugador->rating_singles)
                        <span class="text-sm text-gray-600">Singles: <strong class="text-brand-600">{{ number_format($jugador->rating_singles, 2) }}</strong></span>
                    @endif
                </div>
                @if($jugador->dupr_sincronizado_at)
                    <p class="text-xs text-gray-400 mt-1">Actualizado: {{ $jugador->dupr_sincronizado_at->diffForHumans() }}</p>
                @endif
            </div>
            <form action="{{ route('jugador.dupr.desconectar') }}" method="POST">
                @csrf
                <button type="submit"
                    onclick="return confirm('¿Desvincular cuenta DUPR?')"
                    class="px-4 py-2 text-sm border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                    Desvincular DUPR
                </button>
            </form>
        </div>
    @else
        {{-- Búsqueda para vincular --}}
        <p class="text-sm text-gray-500 mb-4">Vinculá tu cuenta DUPR para participar en torneos que lo requieran y sincronizar tus resultados automáticamente.</p>

        <div class="space-y-3">
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="query"
                    placeholder="Buscá tu nombre en DUPR (mínimo 3 caracteres)"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500"
                    @keyup.enter="buscar()"
                    minlength="3"
                >
                <button
                    type="button"
                    @click="buscar()"
                    :disabled="query.length < 3 || cargando"
                    class="px-4 py-2 bg-brand-600 text-white text-sm rounded-lg hover:bg-brand-700 disabled:opacity-50 transition"
                >
                    <span x-show="!cargando">Buscar</span>
                    <span x-show="cargando">...</span>
                </button>
            </div>

            <div x-show="resultados.length > 0" class="space-y-2">
                <p class="text-xs text-gray-500">Seleccioná tu perfil:</p>
                <template x-for="jugador in resultados" :key="jugador.duprId">
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-brand-400 transition">
                        <div>
                            <p class="font-medium text-sm text-gray-800" x-text="jugador.fullName"></p>
                            <p class="text-xs text-gray-400">
                                DUPR: <span x-text="jugador.duprId" class="font-mono"></span>
                                <span x-show="jugador.doublesRating"> · Dobles: <strong x-text="jugador.doublesRating?.toFixed(2)"></strong></span>
                            </p>
                        </div>
                        <form :action="'{{ route('jugador.dupr.vincular') }}'" method="POST">
                            @csrf
                            <input type="hidden" name="dupr_id" :value="jugador.duprId">
                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition">
                                Soy yo
                            </button>
                        </form>
                    </div>
                </template>
            </div>

            <p x-show="buscando && resultados.length === 0 && !cargando" class="text-sm text-gray-500">No se encontraron resultados.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function duprBuscar() {
        return {
            query: '',
            resultados: [],
            cargando: false,
            buscando: false,
            async buscar() {
                if (this.query.length < 3) return;
                this.cargando = true;
                this.buscando = true;
                try {
                    const res = await fetch(`{{ route('jugador.dupr.buscar') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.resultados = data.hits || [];
                } finally {
                    this.cargando = false;
                }
            }
        }
    }
</script>
@endpush
```

**Nota:** El `x-data` del div principal debe usar la función `duprBuscar()`. Cambiar `x-data="{ buscando: false, ... }"` por `x-data="duprBuscar()"` y el método `buscar()` estará disponible.

- [ ] **Step 2: Commit**

```bash
git add resources/views/jugador/perfil.blade.php
git commit -m "feat: sección DUPR en perfil del jugador (buscar/vincular/desvincular)"
```

---

## Task 8: Vista — Toggle DUPR en wizard paso 2 + rating por categoría

**Files:**
- Modify: `resources/views/torneos/create-step2.blade.php`

- [ ] **Step 1: Agregar toggle `dupr_requerido` en la vista del wizard paso 2**

En `resources/views/torneos/create-step2.blade.php`, buscar el `</form>` final o el botón de submit, y agregar ANTES del botón de submit un nuevo bloque:

```blade
{{-- Toggle DUPR --}}
<div class="mb-6 border border-gray-200 rounded-lg p-4" x-data="{ duprRequerido: {{ old('dupr_requerido', $torneo->dupr_requerido ?? false) ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="text-sm font-semibold text-gray-800">Requiere cuenta DUPR</h4>
            <p class="text-xs text-gray-500 mt-0.5">Solo jugadores con DUPR vinculado podrán inscribirse en este torneo.</p>
        </div>
        <button type="button"
            @click="duprRequerido = !duprRequerido"
            :class="duprRequerido ? 'bg-green-500' : 'bg-gray-200'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
            <span :class="duprRequerido ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
        </button>
    </div>
    <input type="hidden" name="dupr_requerido" :value="duprRequerido ? '1' : '0'">

    {{-- Rating min/max por categoría (solo visible si DUPR requerido) --}}
    <div x-show="duprRequerido" x-transition class="mt-4 space-y-3">
        <p class="text-xs font-medium text-gray-600">Rating DUPR por categoría (opcional):</p>
        @foreach($torneo->categorias as $index => $categoria)
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 p-3 bg-gray-50 rounded-lg">
            <span class="text-sm font-medium text-gray-700 flex-1">{{ $categoria->nombre }}</span>
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500">Mín.</label>
                <input type="number"
                    name="categorias[{{ $index }}][dupr_rating_min]"
                    step="0.01" min="2" max="8"
                    value="{{ old('categorias.'.$index.'.dupr_rating_min', $categoria->pivot->dupr_rating_min ?? '') }}"
                    placeholder="2.00"
                    class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-brand-500">
                <label class="text-xs text-gray-500">Máx.</label>
                <input type="number"
                    name="categorias[{{ $index }}][dupr_rating_max]"
                    step="0.01" min="2" max="8"
                    value="{{ old('categorias.'.$index.'.dupr_rating_max', $categoria->pivot->dupr_rating_max ?? '') }}"
                    placeholder="8.00"
                    class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-brand-500">
            </div>
        </div>
        @endforeach
    </div>
</div>
```

- [ ] **Step 2: Verificar que el blade compila**

```bash
php artisan view:clear --no-interaction && php artisan view:cache --no-interaction 2>&1 | tail -3
```

Expected: sin errores de compilación

- [ ] **Step 3: Commit**

```bash
git add resources/views/torneos/create-step2.blade.php
git commit -m "feat: toggle DUPR y campos rating min/max en wizard paso 2"
```

---

## Task 9: Test de integración final + pint

- [ ] **Step 1: Ejecutar todos los tests nuevos**

```bash
php artisan test tests/Feature/DuprServiceTest.php tests/Feature/DuprJobTest.php tests/Feature/DuprInscripcionValidationTest.php --no-interaction
```

Expected: todos pasan

- [ ] **Step 2: Ejecutar suite completa para verificar sin regresiones**

```bash
php artisan test --no-interaction
```

Expected: todos pasan

- [ ] **Step 3: Correr pint para formateo**

```bash
vendor/bin/pint --dirty
```

- [ ] **Step 4: Commit final**

```bash
git add -A
git commit -m "style: pint formatting en archivos DUPR"
```
