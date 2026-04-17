# Inscripciones del Jugador — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Crear la página `/jugador/inscripciones` con dos tabs (invitaciones recibidas + inscripciones lideradas), agregar el item en el sidebar con badge de pendientes, y hacer que la campana navegue a esa página al clickear una notificación de invitación.

**Architecture:** Se agrega un método `inscripciones()` al `DashboardController` existente que carga los cuatro conjuntos de datos necesarios (invitaciones pendientes, historial, inscripciones lideradas pendientes, inscripciones confirmadas). La vista usa Alpine.js para el estado de tabs. El sidebar calcula el badge inline con Eloquent. La campana agrega una función `handleClick()` que navega condicionalmente.

**Tech Stack:** Laravel 10, Blade, Alpine.js 3, Tailwind CSS, PHPUnit

---

## Mapa de archivos

| Archivo | Acción | Responsabilidad |
|---|---|---|
| `routes/web.php` | Modificar | Agregar `GET /jugador/inscripciones` |
| `app/Http/Controllers/Jugador/DashboardController.php` | Modificar | Agregar método `inscripciones()` |
| `resources/views/jugador/inscripciones.blade.php` | Crear | Vista con dos tabs y todos los estados |
| `resources/views/layouts/jugador.blade.php` | Modificar | Item "Inscripciones" en sidebar con badge |
| `resources/views/partials/_campana-notificaciones.blade.php` | Modificar | Función `handleClick()` con navegación condicional |
| `tests/Feature/Jugador/InscripcionesPageTest.php` | Crear | Tests de acceso, datos y autorización |

---

## Task 1: Ruta y método controller

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/Jugador/DashboardController.php`
- Create: `tests/Feature/Jugador/InscripcionesPageTest.php`

- [ ] **Step 1.1: Crear el test de feature**

```bash
php artisan make:test Jugador/InscripcionesPageTest
```

- [ ] **Step 1.2: Escribir los tests**

Reemplazar el contenido de `tests/Feature/Jugador/InscripcionesPageTest.php`:

```php
<?php

namespace Tests\Feature\Jugador;

use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscripcionesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/jugador/inscripciones');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_without_jugador_profile_sees_empty_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $response->assertStatus(200);
        $response->assertViewIs('jugador.inscripciones');
        $response->assertViewHas('invitacionesPendientes');
        $response->assertViewHas('historialInvitaciones');
        $response->assertViewHas('inscripcionesPendientes');
        $response->assertViewHas('inscripcionesConfirmadas');
        $this->assertCount(0, $response->viewData('invitacionesPendientes'));
        $this->assertCount(0, $response->viewData('inscripcionesPendientes'));
    }

    public function test_page_shows_pending_invitations_for_jugador(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $invitacion = InvitacionJugador::factory()->create([
            'jugador_id' => $jugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $response->assertStatus(200);
        $pendientes = $response->viewData('invitacionesPendientes');
        $this->assertCount(1, $pendientes);
        $this->assertEquals($invitacion->id, $pendientes->first()->id);
    }

    public function test_page_shows_last_10_historical_invitations(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        // Crear 12 invitaciones respondidas
        InvitacionJugador::factory()->count(12)->create([
            'jugador_id' => $jugador->id,
            'estado' => 'aceptada',
            'respondido_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $historial = $response->viewData('historialInvitaciones');
        $this->assertCount(10, $historial);
    }

    public function test_page_shows_inscriptions_led_by_jugador(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $inscripcion = InscripcionEquipo::factory()->create([
            'lider_jugador_id' => $jugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $pendientes = $response->viewData('inscripcionesPendientes');
        $this->assertCount(1, $pendientes);
        $this->assertEquals($inscripcion->id, $pendientes->first()->id);
    }

    public function test_page_does_not_show_other_jugadores_invitations(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $otroJugador = Jugador::factory()->create();
        InvitacionJugador::factory()->create([
            'jugador_id' => $otroJugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $this->assertCount(0, $response->viewData('invitacionesPendientes'));
    }
}
```

- [ ] **Step 1.3: Correr los tests para verificar que fallan**

```bash
php artisan test tests/Feature/Jugador/InscripcionesPageTest.php
```

Resultado esperado: todos los tests fallan con `404` o `Route not found`.

- [ ] **Step 1.4: Agregar la ruta en `routes/web.php`**

Buscar el grupo de rutas del jugador (línea ~98) y agregar la nueva ruta:

```php
// RUTAS DEL PANEL DE JUGADOR
Route::middleware('auth')->prefix('jugador')->name('jugador.')->group(function () {
    Route::get('/dashboard', [JugadorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/torneos', [JugadorDashboardController::class, 'torneos'])->name('torneos');
    Route::get('/inscripciones', [JugadorDashboardController::class, 'inscripciones'])->name('inscripciones'); // <- agregar esta línea
    Route::get('/perfil', [JugadorPerfilController::class, 'show'])->name('perfil');
    Route::put('/perfil', [JugadorPerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [JugadorPerfilController::class, 'updatePassword'])->name('perfil.password');
    Route::post('/perfil/foto', [JugadorPerfilController::class, 'updateFoto'])->name('perfil.foto');
});
```

- [ ] **Step 1.5: Agregar el método `inscripciones()` en `DashboardController`**

Agregar los imports necesarios al inicio del archivo (después de los imports existentes):

```php
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
```

Agregar el método al final de la clase, antes del cierre `}`:

```php
public function inscripciones()
{
    $user = auth()->user();
    $jugador = $user->jugador;

    $invitacionesPendientes = collect();
    $historialInvitaciones = collect();
    $inscripcionesPendientes = collect();
    $inscripcionesConfirmadas = collect();

    if ($jugador) {
        $invitacionesPendientes = InvitacionJugador::with([
            'inscripcionEquipo.torneo',
            'inscripcionEquipo.categoria',
            'inscripcionEquipo.lider',
        ])
            ->where('jugador_id', $jugador->id)
            ->where('estado', 'pendiente')
            ->get();

        $historialInvitaciones = InvitacionJugador::with([
            'inscripcionEquipo.torneo',
            'inscripcionEquipo.categoria',
            'inscripcionEquipo.lider',
        ])
            ->where('jugador_id', $jugador->id)
            ->where('estado', '!=', 'pendiente')
            ->orderByDesc('respondido_at')
            ->limit(10)
            ->get();

        $inscripcionesPendientes = InscripcionEquipo::with([
            'torneo',
            'categoria',
            'invitaciones.jugador',
        ])
            ->where('lider_jugador_id', $jugador->id)
            ->where('estado', 'pendiente')
            ->get();

        $inscripcionesConfirmadas = InscripcionEquipo::with([
            'torneo',
            'categoria',
            'equipo',
        ])
            ->where('lider_jugador_id', $jugador->id)
            ->where('estado', 'confirmada')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
    }

    return view('jugador.inscripciones', compact(
        'jugador',
        'invitacionesPendientes',
        'historialInvitaciones',
        'inscripcionesPendientes',
        'inscripcionesConfirmadas'
    ));
}
```

- [ ] **Step 1.6: Crear la vista vacía temporalmente para que los tests pasen**

Crear el archivo `resources/views/jugador/inscripciones.blade.php` con contenido mínimo:

```blade
@extends('layouts.jugador')
@section('title', 'Mis Inscripciones')
@section('page-title', 'Mis Inscripciones')
@section('content')
<div>placeholder</div>
@endsection
```

- [ ] **Step 1.7: Correr los tests para verificar que pasan**

```bash
php artisan test tests/Feature/Jugador/InscripcionesPageTest.php
```

Resultado esperado: todos los tests pasan.

- [ ] **Step 1.8: Commit**

```bash
git add routes/web.php \
        app/Http/Controllers/Jugador/DashboardController.php \
        resources/views/jugador/inscripciones.blade.php \
        tests/Feature/Jugador/InscripcionesPageTest.php
git commit -m "feat: ruta y controller para página inscripciones del jugador"
```

---

## Task 2: Vista con dos tabs

**Files:**
- Modify: `resources/views/jugador/inscripciones.blade.php`

- [ ] **Step 2.1: Reemplazar la vista con la implementación completa**

Reemplazar el contenido completo de `resources/views/jugador/inscripciones.blade.php`:

```blade
@extends('layouts.jugador')

@section('title', 'Mis Inscripciones')
@section('page-title', 'Mis Inscripciones')

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-0 py-4 md:py-6" x-data="{ tab: 'recibidas' }">

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button @click="tab = 'recibidas'"
                :class="tab === 'recibidas' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm transition flex items-center gap-2">
            Invitaciones recibidas
            @if($invitacionesPendientes->count() > 0)
                <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $invitacionesPendientes->count() }}
                </span>
            @endif
        </button>
        <button @click="tab = 'lideradas'"
                :class="tab === 'lideradas' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm transition flex items-center gap-2">
            Inscripciones que lidero
            @if($inscripcionesPendientes->count() > 0)
                <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $inscripcionesPendientes->count() }}
                </span>
            @endif
        </button>
    </div>

    {{-- ===================== TAB 1: INVITACIONES RECIBIDAS ===================== --}}
    <div x-show="tab === 'recibidas'" x-cloak>

        {{-- Pendientes --}}
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Pendientes</h3>

        @forelse($invitacionesPendientes as $inv)
            @php $insc = $inv->inscripcionEquipo; @endphp
            <div class="bg-white rounded-lg shadow-sm border border-indigo-200 p-4 mb-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $insc->torneo->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $insc->categoria->nombre }}
                            · Invitado por <span class="font-medium">{{ $insc->lider->nombre_completo }}</span>
                        </p>
                        @if($insc->expires_at)
                            <p class="text-xs text-orange-500 mt-1">
                                Expira {{ $insc->expires_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form action="{{ route('inscripciones.invitacion.aceptar', $inv->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                                Aceptar
                            </button>
                        </form>
                        <form action="{{ route('inscripciones.invitacion.rechazar', $inv->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('¿Rechazar la invitación? Se cancelará la inscripción de todo el equipo.')"
                                    class="bg-white hover:bg-red-50 text-red-600 border border-red-300 text-sm font-medium px-4 py-2 rounded-lg transition">
                                Rechazar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 rounded-lg p-6 text-center text-sm text-gray-400 mb-6">
                No tenés invitaciones pendientes
            </div>
        @endforelse

        {{-- Historial --}}
        @if($historialInvitaciones->count() > 0)
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-6">Historial</h3>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @foreach($historialInvitaciones as $inv)
                    @php $insc = $inv->inscripcionEquipo; @endphp
                    <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $insc->torneo->nombre }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $insc->categoria->nombre }}
                                @if($inv->respondido_at)
                                    · {{ $inv->respondido_at->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0
                            {{ $inv->estado === 'aceptada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $inv->estado === 'aceptada' ? 'Aceptada' : 'Rechazada' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ===================== TAB 2: INSCRIPCIONES QUE LIDERO ===================== --}}
    <div x-show="tab === 'lideradas'" x-cloak>

        {{-- Pendientes --}}
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Pendientes de confirmar</h3>

        @forelse($inscripcionesPendientes as $insc)
            @php
                $totalInvitados = $insc->invitaciones->count();
                $confirmados = $insc->invitaciones->where('estado', 'aceptada')->count();
                $minutosRestantes = $insc->expires_at ? max(0, now()->diffInMinutes($insc->expires_at, false)) : 0;
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-4 mb-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $insc->torneo->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $insc->categoria->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium text-indigo-700">{{ $confirmados }}/{{ $totalInvitados }}</span>
                            jugadores confirmados
                        </p>
                        @if($minutosRestantes > 0)
                            <p class="text-xs text-orange-500 mt-0.5">
                                {{ $minutosRestantes }} min restantes
                            </p>
                        @else
                            <p class="text-xs text-red-500 mt-0.5">Expirada</p>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <a href="{{ route('inscripciones.invitar', $insc) }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition text-center">
                            Gestionar equipo
                        </a>
                        <form action="{{ route('inscripciones.cancelar', $insc) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('¿Cancelar la inscripción? Se notificará a todos los jugadores invitados.')"
                                    class="bg-white hover:bg-red-50 text-red-600 border border-red-300 text-sm font-medium px-4 py-2 rounded-lg transition">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 rounded-lg p-6 text-center text-sm text-gray-400 mb-6">
                No liderás ninguna inscripción activa
            </div>
        @endforelse

        {{-- Confirmadas --}}
        @if($inscripcionesConfirmadas->count() > 0)
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-6">Equipos confirmados</h3>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @foreach($inscripcionesConfirmadas as $insc)
                    <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $insc->equipo?->nombre ?? $insc->torneo->nombre }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $insc->categoria->nombre }}</p>
                        </div>
                        <a href="{{ route('torneos.public', $insc->torneo_id) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex-shrink-0">
                            Ver torneo →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
```

- [ ] **Step 2.2: Verificar que los tests siguen pasando**

```bash
php artisan test tests/Feature/Jugador/InscripcionesPageTest.php
```

Resultado esperado: todos pasan.

- [ ] **Step 2.3: Commit**

```bash
git add resources/views/jugador/inscripciones.blade.php
git commit -m "feat: vista de inscripciones del jugador con tabs recibidas/lideradas"
```

---

## Task 3: Sidebar con item y badge

**Files:**
- Modify: `resources/views/layouts/jugador.blade.php`

- [ ] **Step 3.1: Agregar el item "Inscripciones" en el sidebar**

En `resources/views/layouts/jugador.blade.php`, localizar el bloque de navegación (alrededor de la línea 84) que tiene "Mis Torneos". Agregar el nuevo item **después** del link de "Mis Torneos":

```blade
{{-- Mis Torneos --}}
<a href="{{ route('jugador.torneos') }}"
    class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.torneos*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    Mis Torneos
</a>

{{-- Inscripciones (nuevo) --}}
@php
    $inscripcionesBadge = 0;
    if (auth()->user()->jugador) {
        $jugadorId = auth()->user()->jugador->id;
        $inscripcionesBadge = \App\Models\InvitacionJugador::where('jugador_id', $jugadorId)
            ->where('estado', 'pendiente')
            ->count()
            + \App\Models\InscripcionEquipo::where('lider_jugador_id', $jugadorId)
            ->where('estado', 'pendiente')
            ->count();
    }
@endphp
<a href="{{ route('jugador.inscripciones') }}"
    class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.inscripciones*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
    </svg>
    <span class="flex-1">Inscripciones</span>
    @if($inscripcionesBadge > 0)
        <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0">
            {{ $inscripcionesBadge > 9 ? '9+' : $inscripcionesBadge }}
        </span>
    @endif
</a>
```

- [ ] **Step 3.2: Verificar que la app no tira errores de compilación**

```bash
php artisan view:clear
php artisan route:list --path=jugador
```

Resultado esperado: la ruta `jugador.inscripciones` aparece listada, no hay errores.

- [ ] **Step 3.3: Commit**

```bash
git add resources/views/layouts/jugador.blade.php
git commit -m "feat: item Inscripciones en sidebar del jugador con badge de pendientes"
```

---

## Task 4: Campana — navegación al hacer click en invitación

**Files:**
- Modify: `resources/views/partials/_campana-notificaciones.blade.php`

- [ ] **Step 4.1: Agregar la función `handleClick()` y actualizar el template**

En `_campana-notificaciones.blade.php`, agregar la función `handleClick` dentro del objeto `x-data`, después de la función `marcarTodas()` (alrededor de la línea 51):

```javascript
async handleClick(n) {
    if (!n.leida) {
        await this.marcarLeida(n.id);
    }
    if (n.tipo === 'invitacion_torneo') {
        window.location.href = '{{ route('jugador.inscripciones') }}';
    }
},
```

Luego, en el template de cada notificación (alrededor de la línea 118-119), cambiar el `@click`:

```html
{{-- Antes: --}}
<div
    @click="!n.leida && marcarLeida(n.id)"
    :class="n.leida ? 'bg-white' : 'bg-indigo-50 cursor-pointer hover:bg-indigo-100'"
    class="px-4 py-3 transition"
>

{{-- Después: --}}
<div
    @click="handleClick(n)"
    :class="n.leida && n.tipo !== 'invitacion_torneo' ? 'bg-white' : 'bg-indigo-50 cursor-pointer hover:bg-indigo-100'"
    class="px-4 py-3 transition"
>
```

- [ ] **Step 4.2: Verificar que los tests generales siguen pasando**

```bash
php artisan test tests/Feature/Jugador/InscripcionesPageTest.php
```

Resultado esperado: todos pasan.

- [ ] **Step 4.3: Commit final**

```bash
git add resources/views/partials/_campana-notificaciones.blade.php
git commit -m "feat: campana navega a inscripciones al clickear notificación de invitación"
```

---

## Task 5: Verificación integral

- [ ] **Step 5.1: Correr el test suite completo**

```bash
php artisan test
```

Resultado esperado: todos los tests pasan. Si alguno falla, investigar antes de continuar.

- [ ] **Step 5.2: Limpiar caches**

```bash
php artisan view:clear && php artisan route:clear
```

- [ ] **Step 5.3: Verificar rutas**

```bash
php artisan route:list --path=jugador
```

Resultado esperado: aparecen `jugador.dashboard`, `jugador.torneos`, `jugador.inscripciones`, `jugador.perfil`.
