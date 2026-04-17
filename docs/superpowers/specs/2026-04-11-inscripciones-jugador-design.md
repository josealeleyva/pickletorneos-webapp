# Diseño: Página de Inscripciones del Jugador + Mejoras de Navegación

**Fecha:** 2026-04-11
**Estado:** Aprobado

---

## Contexto

El flujo de inscripción a torneos tiene dos roles:
- **Invitado**: recibe una invitación de otro jugador para unirse a un equipo.
- **Líder**: crea la inscripción e invita a otros jugadores.

Actualmente:
- La campana de notificaciones no navega a ningún lado al hacer click en `invitacion_torneo`.
- El líder no tiene forma de volver a `/inscripciones/{id}/invitar` si navega fuera.
- No existe una sección centralizada para gestionar inscripciones ni invitaciones.

---

## Objetivo

1. Crear una página central `/jugador/inscripciones` con dos tabs para gestionar todo lo relacionado con inscripciones.
2. Agregar un item en el sidebar con badge de pendientes.
3. Hacer que la campana navegue a esta página cuando la notificación es de tipo `invitacion_torneo`.

---

## Diseño

### 1. Nueva página `/jugador/inscripciones`

**Ruta:** `GET /jugador/inscripciones` → nombre `jugador.inscripciones`
**Controller:** método `inscripciones()` en `Jugador/DashboardController`
**Vista:** `resources/views/jugador/inscripciones.blade.php`

#### Tab 1: "Invitaciones recibidas"

Muestra invitaciones donde el jugador autenticado es el invitado (`invitaciones_jugador.jugador_id`).

**Sección "Pendientes":**
- Lista de `InvitacionJugador` con `estado = 'pendiente'`
- Por cada invitación muestra: nombre del torneo, categoría, nombre del líder, fecha de expiración de la inscripción
- Dos botones: **Aceptar** (POST `/inscripciones/invitacion/{token}/aceptar`) y **Rechazar** (POST `/inscripciones/invitacion/{token}/rechazar`)
- Estado vacío si no hay pendientes: "No tenés invitaciones pendientes"

**Sección "Historial":**
- Últimas 10 invitaciones con `estado != 'pendiente'`, ordenadas por `respondido_at DESC`
- Muestra estado (badge verde/rojo), torneo, categoría, fecha de respuesta
- No tiene botones de acción

#### Tab 2: "Inscripciones que lidero"

Muestra inscripciones donde el jugador autenticado es líder (`inscripciones_equipo.lider_jugador_id`).

**Inscripciones pendientes:**
- Lista de `InscripcionEquipo` con `estado = 'pendiente'`
- Muestra: torneo, categoría, jugadores confirmados vs total requerido (ej: "2/4 confirmados"), tiempo restante hasta `expires_at`
- Botón **"Gestionar equipo"** → `/inscripciones/{id}/invitar`
- Botón **"Cancelar"** → DELETE `/inscripciones/{id}` (con confirm)

**Inscripciones confirmadas (últimas 5):**
- `InscripcionEquipo` con `estado = 'confirmada'`
- Muestra: nombre del equipo, torneo, categoría
- Link al torneo público

**Estado vacío:** "No liderás ninguna inscripción activa"

---

### 2. Sidebar — nuevo item

En `resources/views/layouts/jugador.blade.php`, bajo la sección "Mi Actividad", agregar:

```
Mis Torneos     (existente)
Inscripciones   (nuevo) ← badge rojo si hay pendientes
Mi Perfil       (existente)
```

**Badge:** muestra el total de:
- Invitaciones recibidas pendientes (`invitaciones_jugador` donde `jugador_id = user->jugador->id` y `estado = 'pendiente'`)
- Inscripciones lideradas pendientes (`inscripciones_equipo` donde `lider_jugador_id = user->jugador->id` y `estado = 'pendiente'`)

Calculado inline en el layout con Eloquent. Si el usuario no tiene perfil de jugador, el badge no se muestra.

---

### 3. Campana de notificaciones

En `resources/views/partials/_campana-notificaciones.blade.php`, actualizar el handler de click:

```javascript
// Antes (solo marca como leída):
@click="!n.leida && marcarLeida(n.id)"

// Después:
@click="handleClick(n)"
```

Nueva función `handleClick(n)`:
- Si `n.tipo === 'invitacion_torneo'`: marca como leída Y navega a `/jugador/inscripciones`
- Para el resto: solo marca como leída (comportamiento actual)

---

## Archivos a modificar/crear

| Archivo | Tipo | Descripción |
|---|---|---|
| `routes/web.php` | Modificar | Agregar ruta `GET /jugador/inscripciones` |
| `app/Http/Controllers/Jugador/DashboardController.php` | Modificar | Agregar método `inscripciones()` |
| `resources/views/jugador/inscripciones.blade.php` | Crear | Vista con dos tabs |
| `resources/views/layouts/jugador.blade.php` | Modificar | Item sidebar + badge |
| `resources/views/partials/_campana-notificaciones.blade.php` | Modificar | `handleClick()` con navegación |

---

## Restricciones

- No requiere migración de base de datos.
- No requiere nuevos modelos.
- Reutiliza los endpoints existentes de `InvitacionController` para aceptar/rechazar.
- Reutiliza el endpoint existente de `InscripcionController@cancelar` para cancelar.
- La vista debe ser completamente responsive (mobile-first, Tailwind CSS).
- Todos los formularios deben incluir `@csrf`.
