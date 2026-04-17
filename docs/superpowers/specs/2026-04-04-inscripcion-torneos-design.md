# Diseño: Sistema de Inscripción de Jugadores/Equipos a Torneos

**Fecha:** 2026-04-04
**Estado:** Aprobado

---

## Resumen

Sistema que permite a jugadores registrados inscribirse de forma autónoma a torneos, formando su equipo/pareja e invitando a otros jugadores. El organizador recibe notificación y puede eliminar equipos inscritos si lo desea.

---

## Base de Datos

### Nueva tabla: `inscripciones_equipo`

| Campo | Tipo | Descripción |
|---|---|---|
| id | PK | |
| torneo_id | FK torneos | |
| categoria_id | FK categorias | |
| lider_jugador_id | FK jugadores | Jugador que inició la inscripción |
| estado | enum(`pendiente`, `confirmada`, `cancelada`) | Estado de la inscripción |
| expires_at | datetime\|null | Fin de la reserva de cupo (now + 10 min) |
| equipo_id | FK equipos\|null | Asignado cuando todos los jugadores aceptan |
| cancelado_por | enum(`organizador`, `jugador`, `expiracion`)\|null | Razón de cancelación |
| timestamps | | |
| softDeletes | | |

### Nueva tabla: `invitaciones_jugador`

| Campo | Tipo | Descripción |
|---|---|---|
| id | PK | |
| inscripcion_equipo_id | FK inscripciones_equipo | |
| jugador_id | FK jugadores | Jugador invitado |
| estado | enum(`pendiente`, `aceptada`, `rechazada`) | |
| auto_aceptada | boolean | true si se aceptó automáticamente |
| token | string único | Para link de invitación por email |
| respondido_at | datetime\|null | |
| timestamps | | |

**Nota:** El líder también tiene fila en `invitaciones_jugador` con estado `aceptada` desde el inicio, para que la lógica de "todos aceptaron" sea uniforme.

### Modificación en tabla `jugadores`

Agregar campo:
- `auto_aceptar_invitaciones` boolean, default `false`

---

## Máquina de Estados

```
pendiente ──── todos aceptan ────→ confirmada
    │                                   │
    ├── expira (sin cupo) ──→ cancelada  │
    ├── jugador rechaza ────→ cancelada  ├── organizador elimina → cancelada
    └── lider cancela ─────→ cancelada  └── (notifica a todos los jugadores)
```

---

## Flujo Completo

### 1. El jugador inicia inscripción

- Solo puede inscribirse en torneos con estado `activo` y cupos disponibles en su categoría.
- El sistema filtra torneos por `edad_minima`, `edad_maxima` y `genero_permitido` del pivot `categoria_torneo`.
- Se crea `InscripcionEquipo` con estado `pendiente` y `expires_at = now() + 10 minutos`.
- Se crea la invitación del líder con estado `aceptada`.

### 2. El líder busca compañeros

- Búsqueda AJAX por nombre/apellido o email/teléfono.
- Solo se muestran jugadores (usuarios registrados) que cumplen las condiciones del torneo.
- No se pueden invitar jugadores ya en otro equipo del mismo torneo/categoría.
- No se puede invitar al mismo jugador dos veces.

### 3. Envío de invitaciones

- Se crea fila en `invitaciones_jugador` (estado `pendiente`, token único).
- Se envía notificación in-app + email con link `/inscripciones/invitacion/{token}`.
- Si el invitado no está logueado, se redirige a login/registro y luego al link.

### 4. El invitado responde

- **Auto-aceptar:** si el invitado tiene `auto_aceptar_invitaciones = true` Y tiene historial de partidos compartidos con el líder (registrado en `equipo_jugador`), la invitación se acepta automáticamente sin intervención.
- **Manual:** el invitado ve la notificación en la plataforma y acepta o rechaza.
- Si rechaza → `InscripcionEquipo` pasa a `cancelada` (`cancelado_por = jugador`), se notifica a todos los involucrados.

### 5. Confirmación automática del equipo

Cuando todas las `invitaciones_jugador` de una `InscripcionEquipo` tienen estado `aceptada`:
- Se crea el `Equipo` en la tabla `equipos` del torneo.
- Se asigna `InscripcionEquipo.equipo_id` y estado → `confirmada`.
- Notificación in-app + email a todos los jugadores del equipo.
- Notificación in-app + email al organizador del torneo.

### 6. Expiración (Job cada 5 minutos)

`ProcesarInscripcionesExpiradas` evalúa todas las `InscripcionEquipo` con `expires_at` vencido y estado `pendiente`:
- **Si hay cupo disponible:** confirmar igual (crear equipo, notificar).
- **Si no hay cupo:** cancelar (`cancelado_por = expiracion`), notificar a todos los jugadores.

### 7. El organizador elimina un equipo

- El organizador puede eliminar cualquier equipo inscripto desde su panel.
- `InscripcionEquipo` pasa a `cancelada` (`cancelado_por = organizador`).
- Notificación in-app + email a todos los jugadores del equipo.

---

## Componentes Técnicos

### Controladores

- **`InscripcionController`** — inicia inscripción, busca jugadores, cancela (líder)
- **`InvitacionController`** — muestra vista de respuesta por token, procesa aceptar/rechazar

### Servicio

- **`InscripcionService`**
  - `iniciarInscripcion(Jugador, Torneo, Categoria): InscripcionEquipo`
  - `buscarJugadoresElegibles(Torneo, Categoria, string $query): Collection`
  - `enviarInvitacion(InscripcionEquipo, Jugador): InvitacionJugador`
  - `responderInvitacion(InvitacionJugador, bool $aceptar): void`
  - `verificarYConfirmar(InscripcionEquipo): void`
  - `cancelarInscripcion(InscripcionEquipo, string $canceladoPor): void`
  - `debeAutoAceptar(InvitacionJugador): bool`

### Job

- **`ProcesarInscripcionesExpiradas`** — scheduled cada 5 minutos, evalúa cupos y confirma o cancela inscripciones vencidas

### Notificaciones (in-app + email para todas)

| Notificación | Destinatario | Cuándo |
|---|---|---|
| `InvitacionTorneoNotification` | Jugador invitado | Al enviar invitación |
| `InscripcionConfirmadaNotification` | Todos los jugadores del equipo | Al confirmar equipo |
| `InscripcionCanceladaNotification` | Todos los jugadores del equipo | Al cancelar por cualquier razón |
| `NuevoEquipoInscriptoNotification` | Organizador | Al confirmar equipo |

### Rutas

```php
// Inscripción
GET  /torneos/{torneo}/inscribirse                → InscripcionController@crear
POST /torneos/{torneo}/inscribirse                → InscripcionController@store
GET  /torneos/{torneo}/inscribirse/buscar         → InscripcionController@buscarJugadores (AJAX)
POST /inscripciones/{inscripcion}/invitar         → InscripcionController@invitar
DELETE /inscripciones/{inscripcion}               → InscripcionController@cancelar

// Invitaciones (acceso por token, público con redirect a login si no autenticado)
GET  /inscripciones/invitacion/{token}            → InvitacionController@mostrar
POST /inscripciones/invitacion/{token}/aceptar    → InvitacionController@aceptar
POST /inscripciones/invitacion/{token}/rechazar   → InvitacionController@rechazar
```

### Vistas

- **`torneos/public.blade.php`** — agregar sección "Inscribirse" con botón que verifica condiciones del jugador logueado
- **`inscripciones/crear.blade.php`** — formulario de inicio: selección de categoría, búsqueda y selección de compañeros
- **`inscripciones/invitacion.blade.php`** — vista para aceptar/rechazar invitación (accesible por token)
- **`jugadores/perfil.blade.php`** — agregar toggle `auto_aceptar_invitaciones`

---

## Reglas de Negocio

1. Solo usuarios con perfil de jugador y cuenta activa pueden inscribirse.
2. Solo el organizador puede agregar jugadores sin cuenta al torneo (flujo existente).
3. El sistema filtra automáticamente jugadores que no cumplen condiciones de edad/género al buscar compañeros.
4. Un jugador no puede estar en dos equipos del mismo torneo/categoría simultáneamente.
5. La reserva de cupo dura 10 minutos; al vencer se evalúa cupo y se confirma o cancela.
6. Auto-aceptar aplica solo si: el invitado tiene el flag activado Y tiene historial de partidos jugados con el líder.
7. Si un jugador rechaza, toda la inscripción se cancela (no se puede reemplazar).
8. El organizador puede eliminar equipos confirmados; se notifica a todos los jugadores.
9. Los cobros de inscripción son gestionados fuera de la plataforma por ahora (preparado para integración MercadoPago futura en `InscripcionEquipo`).

---

## Consideraciones Futuras

- **Fee por MercadoPago:** el modelo `InscripcionEquipo` está preparado para agregar `monto_inscripcion`, `estado_pago` y referencia a pago sin cambios estructurales mayores.
- **Reemplazo de jugador:** actualmente si uno rechaza, la inscripción cancela. En el futuro se podría permitir al líder invitar a otro.
