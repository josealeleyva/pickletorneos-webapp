# Sección de Jugadores — Roadmap de Desarrollo

## Índice
- [Visión General](#visión-general)
- [Principios de Diseño](#principios-de-diseño)
- [Estado Actual de la Base Técnica](#estado-actual-de-la-base-técnica)
- [Etapa 1 — Portal Base del Jugador](#etapa-1--portal-base-del-jugador)
- [Etapa 2 — Visibilidad de Torneos](#etapa-2--visibilidad-de-torneos)
- [Etapa 3 — Perfil del Jugador](#etapa-3--perfil-del-jugador)
- [Etapa 4 — Carga de Resultados por Jugadores](#etapa-4--carga-de-resultados-por-jugadores)
- [Etapa 5 — Notificaciones In-App](#etapa-5--notificaciones-in-app)
- [Etapa 6 — Ranking Global](#etapa-6--ranking-global)
- [Etapa 7 — Social y Comunidad](#etapa-7--social-y-comunidad)
- [Etapa 8 — Inscripción Directa al Torneo](#etapa-8--inscripción-directa-al-torneo)

---

## Visión General

La sección de jugadores extiende la plataforma para que los participantes de los torneos tengan su propio espacio dentro del sistema. El objetivo es reducir el trabajo manual del organizador progresivamente, dándole al jugador visibilidad, control y protagonismo en su experiencia deportiva.

El rol `Jugador` ya existe en el sistema (Spatie Permissions). Lo que se construye aquí es el flujo completo desde el registro hasta la comunidad.

---

## Principios de Diseño

> Estos principios aplican a **todas** las etapas del desarrollo de la sección de jugadores.

- **Mobile-first obligatorio**: La experiencia principal es en dispositivo móvil. Cada pantalla se diseña primero para móvil y luego se adapta a desktop.
- **Consistencia visual**: Todos los componentes deben seguir los estilos actuales del sistema (Tailwind CSS, paleta de colores, tipografías, cards, badges y demás elementos existentes).
- **UX compatible desktop/mobile**: El diseño debe funcionar correctamente en ambos contextos, priorizando siempre la usabilidad móvil.
- **Responsive obligatorio**: Sin excepciones. Ver guías de responsive en `CLAUDE.md`.

---

## Estado Actual de la Base Técnica

Lo que ya existe y puede aprovecharse para esta sección:

| Elemento | Estado | Notas |
|---|---|---|
| Rol `Jugador` (Spatie) | ✅ Existe | Necesita activarse en el flujo de registro |
| Modelo `Jugador` | ✅ Existe | Tiene `user_id` para vincular con usuario registrado |
| Modelo `User` | ✅ Existe | Tiene `deporte_principal_id` |
| Modelo `Inscripcion` | ✅ Existe | Estructura base: `torneo_id`, `jugador_id`, `estado`, `pagado` |
| Modelo `Partido` | ✅ Existe | Se llega al jugador vía Equipo |
| Notificaciones email | ✅ Existe | `EnviarNotificacionPartido`, jobs en cola |
| Modelo `Notificacion` | ✅ Existe | Vinculada a users via pivot |
| Integración MercadoPago | ✅ Existe | Base para etapa 8 |
| Vistas públicas de torneos | ✅ Existe | Bracket, fixture, tabla de posiciones |

---

## Etapa 1 — Portal Base del Jugador

**Objetivo:** Que un jugador pueda registrarse en la plataforma, tener su propio panel y ver sus próximos partidos.

### Registro y Autenticación

- [x] Modificar `/register` para incluir un switch principal: **"Soy Jugador" / "Soy Organizador"**
  - El formulario cambia dinámicamente según la selección
  - Registro de Organizador: mantiene el flujo actual
  - Registro de Jugador: nombre, apellido, email, contraseña, deporte principal, DNI (opcional), teléfono (opcional)
- [x] Al registrarse como Jugador:
  - Se crea un `User` con rol `Jugador`
  - Se crea automáticamente un `Jugador` vinculado (`user_id`) sin `organizador_id`
  - Este jugador estará disponible para **cualquier organizador** al asignar jugadores en un torneo
- [x] El login (`/login`) es único para ambos roles
- [x] Redirección post-login según rol: Organizador → panel de organizador actual / Jugador → nuevo panel de jugador

> **Nota sobre jugadores duplicados:** Antes de este sistema, los organizadores podían tener el mismo jugador cargado manualmente varias veces. Los jugadores registrados vía app son un perfil único y global. No hay fusión automática con registros manuales existentes.

### Dashboard del Jugador

- [x] Layout del panel de jugador con sidebar responsive (mismo patrón que el panel de organizador)
  - Desktop: sidebar fijo
  - Mobile: menú hamburguesa con overlay
- [x] Sección principal del dashboard: **Próximos Partidos**
  - Cards de partidos con: fecha/hora, cancha, torneo, rival
  - Estado del partido (programado, jugado, resultado pendiente)
  - Ordenados cronológicamente, los más próximos primero
  - Empty state si no hay partidos próximos
- [x] Sidebar de torneos con tabs:
  - **Torneos Activos**: torneos que está disputando actualmente
  - **Historial**: torneos finalizados en los que participó, con su posición final
  - **Explorar**: todos los torneos activos de la plataforma, con filtros por deporte y nombre

---

## Etapa 2 — Visibilidad de Torneos

**Objetivo:** El jugador puede seguir en detalle cada torneo en el que participa.

### Datos de Prueba (Seeder)

Para testear el panel de jugador con datos reales, el seeder crea los siguientes usuarios con rol `Jugador` ya inscriptos en torneos:

| Email | Nombre | Deporte | Torneos en los que aparece |
|---|---|---|---|
| `jugador1@pickletorneos.com` | Carlos Rodríguez | Padel | Torneo 1 (Grupos+Elim.), Torneo 2 (Liga), Torneo 3 (Elim. Directa) |
| `jugador2@pickletorneos.com` | Ana Martínez | Padel | Torneo 1 (Grupos+Elim.), Torneo 2 (Liga), Torneo 3 (Elim. Directa) |
| `jugador3@pickletorneos.com` | Diego Fernández | Fútbol | Sin torneos (para probar empty state) |

**Password:** `1234` para todos.

> `TorneoConEquiposSeeder` reemplaza los primeros dos slots de jugadores con los registros vinculados a `jugador1` y `jugador2`. Ambos aparecen en el mismo equipo en los tres torneos de padel.
> Para regenerar: `php artisan migrate:fresh --seed`

### Sección Torneos (tabs)

- [x] **Tab: Torneos Activos**
  - Lista de torneos en curso donde el jugador tiene equipo inscripto
  - Card por torneo: nombre, deporte, complejo, estado, fechas
  - Acceso al detalle del torneo
- [x] **Tab: Historial de Torneos**
  - Lista de torneos finalizados donde participó
  - Posición final obtenida (campeón, 2do, 3ro, 4to, participante)
  - Acceso al detalle del torneo (solo lectura)
- [x] **Tab: Explorar Torneos**
  - Todos los torneos activos/públicos de la plataforma
  - Filtros: por deporte y nombre (client-side)
  - Base para la futura inscripción directa (Etapa 8)

### Detalle del Torneo (vista del jugador)

- [x] Fixture del torneo: ver todos los partidos de su equipo (con resaltado "Tu partido")
- [x] Tabla de posiciones del grupo (si aplica) — reutiliza vista pública existente
- [x] Bracket de eliminación con posición actual del equipo — reutiliza vista pública existente
- [x] Información general del torneo: complejo, fechas, organizador — reutiliza vista pública existente
- [x] Barra superior "Volver al panel" para jugadores autenticados en la vista pública

---

## Etapa 3 — Perfil del Jugador

**Objetivo:** El jugador tiene una identidad dentro de la plataforma con datos personales y estadísticas básicas.

### Datos del Perfil

- [ ] Vista de perfil editable: nombre, apellido, DNI, teléfono, deporte principal
- [ ] Foto de perfil: upload con preview, recorte opcional
- [ ] Avatar con IA: imagen o GIF corto generado con IA a partir de la foto de perfil *(a definir — etapa futura)*

### Estadísticas del Perfil

- [ ] Resumen de torneos jugados / ganados
- [ ] Puntos acumulados en el ranking global (campo visible pero calculado en Etapa 6)
- [ ] Deportes en los que ha participado

---

## Etapa 4 — Carga de Resultados por Jugadores

**Objetivo:** Descargar al organizador de la carga de resultados apelando a la buena fe de los equipos.

### Flujo de Carga

- [ ] Cualquier jugador de un equipo puede cargar el resultado de un partido que disputó
- [ ] Al cargar un resultado:
  - Se guarda como "pendiente de aprobación"
  - Se notifica (in-app y/o email) a todos los jugadores del equipo rival para que lo aprueben o desaprueben
- [ ] Si el equipo rival **aprueba**: el resultado queda confirmado y se procesa igual que si lo cargara el organizador
- [ ] Si cualquier jugador del equipo rival **desaprueba**:
  - El resultado se borra completamente
  - Se cancela/elimina la notificación pendiente al rival
  - Cualquiera de los dos equipos puede volver a cargar un resultado nuevo
- [ ] Un partido puede tener como máximo **un intento de resultado pendiente** a la vez

### Rol del Organizador

- [ ] El organizador puede cargar o modificar el resultado de cualquier partido **en todo momento**, excepto si ya existe un resultado **aprobado por ambas partes**
- [ ] Si el organizador carga un resultado sobre un partido que ya tenía un intento pendiente (sin aprobar):
  - El intento pendiente se borra
  - La notificación al equipo rival se cancela/elimina
  - El resultado del organizador queda confirmado directamente (sin necesitar aprobación)

### Consideraciones Técnicas

- [ ] Nuevo campo en `partidos`: `resultado_origen` (enum: `organizador`, `jugador_pendiente`, `jugador_aprobado`)
- [ ] Nueva tabla o campos para rastrear el intento pendiente: `equipo_cargador_id`, `fecha_carga`, `estado_aprobacion`
- [ ] Las notificaciones de aprobación deben ser cancelables

---

## Etapa 5 — Notificaciones In-App

**Objetivo:** El jugador recibe alertas relevantes dentro de la app sin depender solo del email.

### Tipos de Notificaciones

- [ ] **Partido programado**: cuando el organizador programa un partido del jugador (fecha, hora, cancha, rival)
- [ ] **Resultado pendiente de aprobación**: cuando el equipo rival cargó un resultado y necesita confirmación
- [ ] **Resultado aprobado**: cuando el resultado de un partido fue confirmado por ambas partes
- [ ] **Resultado cargado por organizador**: cuando el organizador carga directamente el resultado
- [ ] **Inscripción confirmada** *(preparar para Etapa 8)*: cuando el organizador confirma la inscripción al torneo

### Infraestructura

- [ ] Centro de notificaciones in-app en el panel del jugador (ícono con badge de no leídas)
- [ ] Marcar notificaciones como leídas (individual y "marcar todas")
- [ ] Aprovechar el modelo `Notificacion` y la tabla pivot `notificaciones_usuarios` ya existentes
- [ ] Las notificaciones críticas también se envían por email (reutilizar sistema de jobs existente)

---

## Etapa 6 — Ranking Global

**Objetivo:** Incentivar la competencia y la fidelidad a la plataforma con un ranking unificado por deporte.

### Reglas del Ranking

- [ ] El ranking es **por deporte** (Padel, Fútbol, Tenis — etc.)
- [ ] No se segmenta por categoría (las categorías las gestiona el organizador, no son globales)
- [ ] Los puntos se asignan al finalizar cada torneo a **todos los jugadores del equipo** según su posición final:
  - 🥇 Campeón (1er puesto): **5 puntos**
  - 🥈 Finalista (2do puesto): **3 puntos**
  - 🥉 Semifinalista (3er puesto): **2 puntos**
  - 4to puesto: **1 punto**
  - Resto de participantes: **0 puntos**

> **Nota:** Este esquema de puntuación puede ajustarse en el futuro (por ejemplo, agregar puntos por victorias individuales, bonificaciones por participación, etc.). El diseño de la tabla debe contemplar que el sistema de puntaje es configurable.

### Implementación

- [ ] Nueva tabla `ranking_jugadores`: `jugador_id`, `deporte_id`, `puntos_totales`, `torneos_jugados`, `torneos_ganados`, `updated_at`
- [ ] Job que se ejecuta al finalizar un torneo: calcula y actualiza los puntos de todos los jugadores participantes
- [ ] Vista de ranking público por deporte: lista de jugadores ordenada por puntos, con foto y nombre
- [ ] Visualización del ranking personal en el perfil del jugador (posición actual + puntos)

---

## Etapa 7 — Social y Comunidad

**Objetivo:** Que los jugadores puedan mostrar su perfil y logros, generando viralidad para la plataforma.

### Perfil Público

- [ ] Página pública del jugador (`/jugadores/{id}`) accesible sin login
- [ ] Muestra: foto de perfil, nombre, deporte principal, puntos en el ranking, torneos destacados
- [ ] El jugador puede configurar si su perfil es público o privado

### Compartir Perfil

- [ ] Generar imagen compartible con estética de PickleTorneos: foto + nombre + ranking + deporte + logros destacados
- [ ] Generar video/GIF animado compartible *(a definir — posiblemente generado con IA)*
- [ ] Botones de compartir: WhatsApp, Instagram, descarga directa

### Avatar con IA *(a definir)*

- [ ] Generación de avatar o GIF animado a partir de la foto de perfil del jugador
- [ ] Estilo visual: jugador cruzando los brazos u otras poses características
- [ ] Definir proveedor de IA a usar, flujo de generación y costos asociados

---

## Etapa 8 — Inscripción Directa al Torneo

**Objetivo:** El jugador se inscribe a torneos desde la app y paga directamente al organizador, con un fee del 5% para la plataforma.

> Esta es la etapa de mayor complejidad técnica y de negocio. Se desarrolla última porque requiere que el jugador ya tenga un perfil consolidado y que el organizador cuente con experiencia gestionando torneos en la plataforma.

### Configuración por parte del Organizador

- [ ] Al crear/editar un torneo, el organizador puede **habilitar inscripciones via app**
- [ ] Configuración de inscripciones:
  - Precio de inscripción por equipo/jugador
  - Cupo máximo de inscriptos
  - Fecha límite de inscripción
- [ ] El organizador puede seguir inscribiendo jugadores manualmente (flujo actual) aunque tenga habilitadas las inscripciones online

### Flujo del Jugador

- [ ] Desde "Explorar Torneos", el jugador puede iniciar una inscripción a un torneo con inscripciones abiertas
- [ ] Para deportes de duplas (Padel): selección/invitación de compañero
- [ ] Pago de inscripción vía MercadoPago directamente en la app

### Modelo de Pagos (MP Connect)

- [ ] Integración con **MercadoPago Connect**: el organizador conecta su propia cuenta de MP a la plataforma
- [ ] El pago de inscripción se acredita **directamente en la cuenta MP del organizador**
- [ ] La plataforma retiene automáticamente un **fee del 5%** sobre cada transacción
- [ ] El organizador puede ver el historial de pagos de inscripciones desde su panel

### Gestión de Inscripciones

- [ ] Panel del organizador: listado de inscripciones por torneo con estado (pendiente, confirmada, rechazada)
- [ ] El organizador puede aprobar/rechazar inscripciones manualmente si lo desea
- [ ] Notificación al jugador cuando su inscripción es confirmada
- [ ] Lista de espera automática cuando se alcanza el cupo máximo
- [ ] Formación automática de equipos a partir de inscripciones confirmadas

---

## Resumen de Etapas

| Etapa | Descripción | Prioridad | Estado |
|-------|-------------|-----------|--------|
| 1 | Portal Base del Jugador | 🔴 Alta | ✅ Completado |
| 2 | Visibilidad de Torneos | 🔴 Alta | ✅ Completado |
| 3 | Perfil del Jugador | 🟡 Media | Pendiente |
| 4 | Carga de Resultados por Jugadores | 🟡 Media | Pendiente |
| 5 | Notificaciones In-App | 🟡 Media | Pendiente |
| 6 | Ranking Global | 🟢 Baja | Pendiente |
| 7 | Social y Comunidad | 🟢 Baja | Pendiente |
| 8 | Inscripción Directa al Torneo | 🟢 Baja | Pendiente |

---

**Última actualización:** 2026-03-11
**Versión:** 1.1
**Estado:** 🚧 En desarrollo — Etapa 3 próxima a iniciar
