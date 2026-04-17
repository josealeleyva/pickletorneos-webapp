# Diseño: Carga de Resultado por Jugadores (Sistema Tentativo)

**Fecha:** 2026-04-12
**Estado:** Aprobado

---

## Contexto

Actualmente solo el organizador puede cargar resultados de partidos. Los jugadores no tienen ninguna forma de registrar el resultado una vez que juegan. El dashboard del jugador además oculta el partido tan pronto como la `fecha_hora` pasa, aunque el resultado todavía no exista.

---

## Objetivo

1. Mantener el partido visible en el dashboard hasta que tenga resultado oficial.
2. Permitir que cualquier jugador de ambos equipos proponga un resultado tentativo una vez que el partido comenzó.
3. Implementar un flujo de confirmación ping-pong entre equipos hasta que ambos acuerden, o el organizador lo resuelva.
4. Nueva página `/jugador/partidos` accesible desde el sidebar con badge de confirmaciones pendientes.

---

## Base de Datos

### Nueva tabla `resultados_tentativo`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | |
| `partido_id` | FK unique → partidos | Un solo tentativo activo por partido |
| `propuesto_por_equipo_id` | FK → equipos | Último equipo en proponer/modificar |
| `propuesto_por_jugador_id` | FK → jugadores | Jugador específico que lo cargó |
| `juegos` | JSON | Array de sets: `[{"juego_equipo1": int, "juego_equipo2": int}]` |
| `sets_equipo1` | integer | Total puntos/sets equipo 1 (calculado) |
| `sets_equipo2` | integer | Total puntos/sets equipo 2 (calculado) |
| `equipo_ganador_id` | FK nullable → equipos | Calculado de sets; null si empate (fútbol) |
| `created_at` / `updated_at` | timestamps | |

### Migration ENUM notificaciones

Agregar dos valores al ENUM `notificaciones.tipo`:
- `resultado_tentativo`
- `resultado_confirmado`

### Sin cambios en `partidos`

No se agregan columnas. El único cambio es en la query del dashboard (eliminar filtro `fecha_hora >= now()`).

---

## Flujo de Estados

```
[sin resultado, fecha pasada]
    ──(jugador carga)──► [ResultadoTentativo existe, propuesto_por = equipo A]
                              │
                    ┌─────────┴──────────┐
             (rival confirma)      (rival modifica)
                    │                    │
                    ▼                    ▼
           resultado oficial     [ResultadoTentativo actualizado,
           en partidos ✓          propuesto_por = equipo B]
           tentativo eliminado         │
                                (equipo A debe confirmar)
                                       ...ping-pong...

    ──(organizador carga en cualquier momento)──► resultado oficial ✓
                                                  tentativo eliminado si existía
```

**Reglas:**
- Botón "Cargar resultado" disponible para jugadores de **ambos** equipos cuando: `fecha_hora < now()` AND `equipo_ganador_id IS NULL` AND no existe `ResultadoTentativo`.
- Si el tentativo fue propuesto por **mi equipo** → veo estado "Esperando confirmación del rival" (sin acciones).
- Si el tentativo fue propuesto por el **equipo rival** → veo botones "Confirmar" y "Modificar".
- Al **confirmar**: se aplica el resultado oficial al `Partido` (mismo mecanismo que el organizador), se crean registros en `juegos`, se elimina el `ResultadoTentativo`, se notifica al equipo proponente.
- Al **modificar**: se reemplaza el `ResultadoTentativo` con nuevos datos, `propuesto_por_equipo_id` cambia al equipo modificador, se notifica al equipo original.
- El organizador puede cargar el resultado en cualquier momento mediante el panel existente (`TorneoFixtureController@cargarResultado`). Si existe un tentativo, se elimina silenciosamente.
- No hay timeout: el estado queda pendiente indefinidamente hasta acuerdo o intervención del organizador.

---

## Archivos a Crear/Modificar

| Archivo | Acción | Descripción |
|---|---|---|
| `database/migrations/XXXX_create_resultados_tentativo_table.php` | Crear | Tabla `resultados_tentativo` |
| `database/migrations/XXXX_add_resultado_tipos_to_notificaciones.php` | Crear | Agregar valores al ENUM |
| `app/Models/ResultadoTentativo.php` | Crear | Modelo con casts y relaciones |
| `app/Http/Controllers/Jugador/ResultadoTentativoController.php` | Crear | store, confirmar, modificar |
| `app/Http/Controllers/Jugador/DashboardController.php` | Modificar | Agregar `partidos()`, fix query en `index()` |
| `resources/views/jugador/partidos.blade.php` | Crear | Vista con 3 secciones + formulario inline |
| `resources/views/jugador/dashboard.blade.php` | Modificar | Agregar botón "Cargar resultado" en cards de partidos |
| `resources/views/layouts/jugador.blade.php` | Modificar | Item "Mis Partidos" en sidebar con badge |
| `resources/views/partials/_campana-notificaciones.blade.php` | Modificar | Navegación para `resultado_tentativo` y `resultado_confirmado` |
| `routes/web.php` | Modificar | Nuevas rutas jugador |
| `app/Http/Controllers/TorneoFixtureController.php` | Modificar | Eliminar tentativo al cargar resultado oficial |
| `app/Http/Controllers/TorneoLlaveController.php` | Modificar | Ídem para brackets |
| `tests/Feature/Jugador/ResultadoTentativoTest.php` | Crear | Tests de flujo completo |

---

## Controller y Rutas

### Rutas nuevas (dentro del grupo `auth`, prefix `jugador`)

```php
Route::get('/partidos', [JugadorDashboardController::class, 'partidos'])->name('partidos');
Route::post('/partidos/{partido}/resultado', [ResultadoTentativoController::class, 'store'])->name('partidos.resultado.store');
Route::post('/resultados/{resultado}/confirmar', [ResultadoTentativoController::class, 'confirmar'])->name('resultados.confirmar');
Route::post('/resultados/{resultado}/modificar', [ResultadoTentativoController::class, 'modificar'])->name('resultados.modificar');
```

### `ResultadoTentativoController`

**`store(Request $request, Partido $partido)`**
- Autorización: el `auth()->user()->jugador` debe pertenecer a `equipo1` o `equipo2` del partido.
- Validación: partido con `fecha_hora < now()`, sin `equipo_ganador_id`, sin tentativo existente.
- Formato de input: igual que `TorneoFixtureController@cargarResultado` — array `juegos[]` con `juego_equipo1` y `juego_equipo2`.
- Calcula `sets_equipo1`, `sets_equipo2`, `equipo_ganador_id`.
- Crea `ResultadoTentativo`.
- Notifica jugadores del equipo rival (con `user_id`) con tipo `resultado_tentativo`.
- Redirige a `/jugador/partidos` con mensaje flash.

**`confirmar(Request $request, ResultadoTentativo $resultado)`**
- Autorización: el jugador debe pertenecer al equipo rival de `propuesto_por_equipo_id`.
- Aplica resultado al `Partido`: actualiza `sets_equipo1`, `sets_equipo2`, `equipo_ganador_id`, `estado = 'finalizado'`.
- Crea registros en tabla `juegos` a partir de `$resultado->juegos`.
- Elimina el `ResultadoTentativo`.
- Notifica jugadores del equipo proponente con tipo `resultado_confirmado`.
- Llama a `TorneoController::intentarFinalizarAutomatico($torneo)`.
- Redirige a `/jugador/partidos` con mensaje flash.

**`modificar(Request $request, ResultadoTentativo $resultado)`**
- Autorización: el jugador debe pertenecer al equipo rival de `propuesto_por_equipo_id`.
- Valida nuevo array `juegos[]`.
- Recalcula totales.
- Actualiza el `ResultadoTentativo` (mismo registro, reemplaza datos + cambia `propuesto_por_equipo_id` y `propuesto_por_jugador_id`).
- Notifica jugadores del equipo original con tipo `resultado_tentativo`.
- Redirige a `/jugador/partidos` con mensaje flash.

### `DashboardController` — cambios

**`index()`:** eliminar `->where('fecha_hora', '>=', now())`. Agregar `.with('resultadoTentativo')` al eager load de `$proximosPartidos`.

**`partidos()` (nuevo método):** carga tres colecciones para la vista:
- `$pendientesConfirmacion`: partidos con `ResultadoTentativo` donde `propuesto_por_equipo_id` es el equipo **rival** del jugador.
- `$esperandoRival`: partidos con `ResultadoTentativo` donde `propuesto_por_equipo_id` es **mi equipo**.
- `$sinResultado`: partidos con `fecha_hora < now()`, sin `equipo_ganador_id`, sin `ResultadoTentativo`.

---

## Vista `/jugador/partidos`

Tres secciones apiladas (sin tabs, son estados excluyentes por partido):

### Sección 1: "Pendiente de tu confirmación" (badge rojo)
Por cada partido en `$pendientesConfirmacion`:
- Muestra: torneo, equipos, resultado propuesto por el rival (sets/juegos según deporte).
- Botón **"Confirmar"** → POST a `jugador.resultados.confirmar`.
- Botón **"Modificar"** → muestra formulario inline idéntico al de carga.
- Estado vacío: no se muestra la sección.

### Sección 2: "Esperando confirmación del rival"
Por cada partido en `$esperandoRival`:
- Muestra: torneo, equipos, resultado que propuse.
- Badge gris "Esperando al rival".
- Sin botones de acción.

### Sección 3: "Sin resultado"
Por cada partido en `$sinResultado`:
- Muestra: torneo, equipos, fecha/hora jugada.
- Botón **"Cargar resultado"** → muestra formulario inline.

**Formulario inline** (mismo en sección 1 modificar y sección 3):
- Fútbol: campo único "Goles equipo A" / "Goles equipo B" (un juego).
- Pádel/Tenis/Pickleball: interfaz de sets dinámica — botón "Agregar set", cada set con dos campos numéricos. Mismo UX que el modal del organizador en `fixture/index.blade.php`.
- El deporte se detecta desde `$partido->equipo1->torneo->deporte`.

---

## Dashboard — cambio en cards de partidos

En `jugador/dashboard.blade.php`, en cada card de `$proximosPartidos`, agregar condicionalmente al final de la card:

```
@if($partido->fecha_hora < now() && !$partido->equipo_ganador_id)
    @if(!$partido->resultadoTentativo)
        [botón "Cargar resultado" → /jugador/partidos]
    @elseif($partido->resultadoTentativo->propuesto_por_equipo_id == $miEquipo->id)
        [badge "Esperando rival"]
    @else
        [botón "Confirmar resultado" → /jugador/partidos]
    @endif
@endif
```

El botón/badge del dashboard es solo un acceso rápido — toda la acción real sucede en `/jugador/partidos`.

---

## Sidebar

Nuevo item **"Mis Partidos"** bajo "Mi Actividad" (entre "Mis Torneos" e "Inscripciones"):

```php
@php
    $partidosBadge = 0;
    if (auth()->user()->jugador) {
        $equipoIds = auth()->user()->jugador->equipos()->pluck('equipos.id');
        $partidosBadge = \App\Models\ResultadoTentativo::whereHas('partido', function ($q) use ($equipoIds) {
            $q->whereIn('equipo1_id', $equipoIds)->orWhereIn('equipo2_id', $equipoIds);
        })->whereNotIn('propuesto_por_equipo_id', $equipoIds)->count();
    }
@endphp
```

Badge rojo solo cuando hay partidos pendientes de **mi** confirmación.

---

## Notificaciones

| Evento | Tipo | Destinatarios | Mensaje |
|---|---|---|---|
| Jugador carga tentativo | `resultado_tentativo` | Jugadores del equipo rival con `user_id` | "[Equipo X] propuso el resultado del partido vs [Equipo Y]. Confirmá o modificá." |
| Jugador modifica tentativo | `resultado_tentativo` | Jugadores del equipo original con `user_id` | "[Equipo X] modificó el resultado propuesto. Revisá y confirmá." |
| Jugador confirma | `resultado_confirmado` | Jugadores del equipo proponente con `user_id` | "[Equipo X] confirmó el resultado del partido vs [Equipo Y]." |

**Campana:** agregar `resultado_tentativo` y `resultado_confirmado` al `handleClick()` para navegar a `/jugador/partidos`.

---

## Integración con el Organizador

En `TorneoFixtureController@cargarResultado` y `TorneoLlaveController@cargarResultado`, después de guardar el resultado oficial, agregar:

```php
ResultadoTentativo::where('partido_id', $partido->id)->delete();
```

Esto garantiza que si el organizador interviene, el tentativo desaparece sin conflicto.

---

## Restricciones

- No se modifican los modelos `Partido` ni `Juego` existentes.
- El resultado confirmado por jugadores usa exactamente la misma lógica de cálculo que el organizador (sumatoria, ganador, `intentarFinalizarAutomatico`).
- Solo jugadores con cuenta registrada (`user_id` no null) reciben notificaciones, pero cualquier jugador del equipo puede cargar/confirmar/modificar (si tiene sesión).
- Responsive, mobile-first, Tailwind CSS.
- Todos los formularios con `@csrf`.
