# Sistema de Gestión de Torneos - PickleTorneos

## Índice
- [Resumen General](#resumen-general)
- [Estado Actual](#estado-actual)
- [Arquitectura Implementada](#arquitectura-implementada)
- [Funcionalidades Implementadas](#funcionalidades-implementadas)
- [Funcionalidades Pendientes](#funcionalidades-pendientes)
- [Roadmap de Implementación](#roadmap-de-implementación)

---

## Resumen General

El sistema de gestión de torneos permite a los organizadores crear y administrar torneos deportivos (principalmente Padel, Fútbol y Tenis) con diferentes formatos de competencia.

### Flujo de Estados del Torneo
```
borrador → activo → en_curso → finalizado
                             ↓
                         cancelado
```

---

## Estado Actual

### ✅ FLUJO COMPLETO DE TORNEO IMPLEMENTADO

El sistema permite gestionar un torneo desde su creación hasta su finalización, con **todos los formatos soportados** (Eliminación Directa, Fase de Grupos + Eliminación, y Liga).

#### Fase 1: Creación y Configuración Básica ✅
- [x] Estructura de base de datos para torneos
- [x] Seeders de datos iniciales (deportes, categorías, formatos, etc.)
- [x] CRUD completo de torneos
- [x] Wizard de creación en 2 pasos
- [x] Configuración de formatos (eliminación directa, grupos + eliminación, liga)
- [x] Cálculo de equipos que avanzan a eliminación
- [x] Gestión de imágenes de banner
- [x] Autorización y políticas de acceso
- [x] Diseño responsive (mobile-first)
- [x] **Integración completa con MercadoPago**
- [x] **Middleware de verificación de pago de torneos**

#### Fase 2: Gestión de Equipos y Jugadores ✅
- [x] **Gestión completa de jugadores** (registro manual por organizador)
- [x] **Creación y gestión de equipos**
- [x] **Asignación de jugadores a equipos**
- [x] **Manejo de apellidos compuestos**
- [x] **Vista pública de torneos** (accesible sin autenticación)
- [x] **Soporte para múltiples categorías** en un mismo torneo

#### Fase 3: Generación de Fixture y Llaves ✅
- [x] **Publicar torneo** (borrador → activo → en_curso)
- [x] **Sorteo y asignación de grupos** (aleatorio y manual con drag & drop)
- [x] **Intercambio de equipos entre grupos**
- [x] **Reseteo de sorteo**
- [x] **Generación de fixture de fase de grupos** (algoritmo Round Robin)
- [x] **Generación de fixture para liga** (todos contra todos)
- [x] **Generación de llaves de eliminación** (brackets de 4, 8, 16, 32 equipos)
- [x] **Programación de partidos** con asignación de canchas y horarios
- [x] **Prevención de conflictos de horarios** en canchas
- [x] **Vista de fixture por grupo y por fecha**
- [x] **Vista visual de bracket** (estilo torneo de tenis) - desktop y mobile
- [x] **Reseteo de fixture y llaves**

#### Fase 4: Ejecución del Torneo ✅
- [x] **Carga de resultados para partidos de grupos**
- [x] **Carga de resultados para partidos de llaves**
- [x] **Validaciones de marcador según deporte** (sets para Padel/Tenis, goles para Fútbol)
- [x] **Tabla de posiciones automática por grupo**
- [x] **Cálculo automático de puntos, diferencia de goles/sets y partidos ganados**
- [x] **Avance automático de equipos a fase de eliminación**
- [x] **Aplicación de criterios de avance configurados**
- [x] **Desempate de mejores segundos**
- [x] **Avance automático de ganadores en brackets**
- [x] **Generación automática de semifinales y final**
- [x] **Marcar campeón del torneo**
- [x] **Sistema de notificaciones por email a jugadores**
- [x] **Notificaciones de partidos programados** (individual y masiva)
- [x] **Sistema de jobs en cola** para procesamiento asíncrono

#### Fase 5: Finalización ✅
- [x] **Finalización de torneo** (en_curso → finalizado)
- [x] **Validación de que todos los partidos tengan resultado**
- [x] **Determinación de campeón y podio**

### ❌ Pendientes (Funcionalidades Futuras)

Solo queda pendiente el **sistema de inscripción automática de jugadores** (actualmente se crean equipos manualmente). Ver sección [Funcionalidades Pendientes](#funcionalidades-pendientes) para funcionalidades adicionales.

---

## Arquitectura Implementada

### Modelos y Relaciones

```php
Torneo
├── belongsTo: Deporte
├── belongsTo: ComplejoDeportivo
├── belongsTo: User (organizador)
├── belongsTo: FormatoTorneo
├── belongsTo: TamanioGrupo (nullable)
├── belongsTo: AvanceGrupo (nullable)
├── hasMany: Grupo
├── hasMany: Equipo
├── hasMany: Llave
├── hasMany: Inscripcion
└── hasMany: ActividadTorneo
```

### Estructura de Datos

#### Tabla: `torneos`
```sql
- id
- nombre
- deporte_id (FK → deportes)
- descripcion (text, nullable)
- fecha_inicio
- fecha_fin
- fecha_limite_inscripcion (nullable)
- imagen_banner (nullable)
- premios (text, nullable)
- complejo_id (FK → complejos_deportivos)
- organizador_id (FK → users)
- precio_inscripcion (decimal, nullable)
- formato_id (FK → formatos_torneos)
- numero_grupos (integer, nullable) ← NUEVO
- tamanio_grupo_id (FK → tamanios_grupos, nullable)
- avance_grupos_id (FK → avances_grupos, nullable)
- estado (enum: borrador, activo, en_curso, finalizado, cancelado)
- timestamps
- soft_deletes
```

### Datos Semilla (Seeders)

#### 1. Deportes
- Padel
- Fútbol 5
- Tenis

#### 2. Categorías (por deporte)
- **Padel**: Masculino, Femenino, Mixto
- **Fútbol**: Libre, Masculino, Femenino
- **Tenis**: Singles Masculino, Singles Femenino, Dobles Masculino, Dobles Femenino, Dobles Mixto

#### 3. Formatos de Torneo
- **Eliminación Directa** (sin grupos)
- **Fase de Grupos + Eliminación** (con grupos)
- **Round Robin / Todos contra Todos** (con grupos)

#### 4. Tamaños de Grupo
- 3 equipos por grupo
- 4 equipos por grupo
- 5 equipos por grupo
- 6 equipos por grupo

#### 5. Criterios de Avance
- Primero de cada grupo
- Primero + 2 mejores segundos
- Primeros 2 de cada grupo

---

## Funcionalidades Implementadas

### 1. Creación de Torneos (Wizard 2 Pasos)

#### Paso 1: Información General
- Nombre del torneo
- Deporte
- Complejo deportivo (solo los que pertenecen al organizador)
- Fechas (inicio, fin)
- Descripción
- Precio de inscripción
- Premios
- Imagen de banner (upload local)

**Validaciones:**
- Nombre requerido
- Complejo debe pertenecer al organizador
- Fecha de fin debe ser posterior a fecha de inicio
- Imagen máximo 2MB

#### Paso 2: Configuración de Formato

**Para formatos sin grupos (Eliminación Directa):**
- Solo selección de formato

**Para formatos con grupos:**
- Número de grupos (2-8)
- Equipos por grupo (3, 4, 5 o 6)
- Criterio de avance a eliminación

**Preview Dinámico:**
```
Con 4 grupos:
Avanzan 1 equipo de cada grupo (4 equipos) + los 2 mejores segundos = 6 equipos a fase de eliminación.
```

**Cálculos automáticos:**
- Total de equipos = `numero_grupos × tamanio_grupo`
- Equipos que avanzan = `(directos × numero_grupos) + mejores_segundos`

### 2. Listado de Torneos

**Características:**
- Vista en cards responsiva
- Badges de estado (borrador, activo, en_curso, finalizado, cancelado)
- Imagen de banner o gradient de fallback
- Información clave: deporte, complejo, fechas, organizador
- Botones de acción según estado:
  - **Borrador**: Ver, Editar, Eliminar
  - **Otros estados**: Ver

**Empty State:**
- Mensaje cuando no hay torneos
- Botón CTA para crear primer torneo

### 3. Vista Detallada del Torneo

**Sección Principal:**
- Banner con imagen o gradient
- Badge de estado
- Información general (descripción, complejo, fechas, organizador, premios)
- Configuración de formato con cálculos:
  - Número de grupos
  - Tamaño de grupos
  - Criterio de avance
  - Total de equipos
  - Equipos que avanzan a eliminación

**Sidebar:**
- Estadísticas (participantes, partidos, grupos) - actualmente en 0
- Próximos pasos para torneos en borrador
- Acciones rápidas (marcadas como "Próximamente")

### 4. Edición de Torneos

**Restricciones:**
- Solo torneos en estado "borrador" pueden editarse
- Solo el organizador puede editar sus propios torneos

**Características:**
- Formulario combinado (ambos pasos del wizard en una sola vista)
- Muestra banner actual con opción de reemplazar
- Preview dinámico de configuración de grupos
- Validación de fechas en tiempo real

### 5. Eliminación de Torneos

**Características:**
- Soft delete implementado
- Solo organizador puede eliminar
- Solo torneos en borrador pueden eliminarse (restricción en UI)

### 6. Autorización y Seguridad

**TorneoPolicy:**
```php
- viewAny: Todos pueden ver listado
- view: Todos pueden ver detalle
- create: Solo organizadores
- update: Solo organizador del torneo + estado borrador
- delete: Solo organizador del torneo + estado borrador
```

**Validaciones de Negocio:**
- Complejo debe pertenecer al organizador
- Fechas coherentes (fin >= inicio)
- Campos de grupos requeridos solo si formato tiene grupos
- Número de grupos entre 2 y 8

### 7. Interfaz de Usuario

**Características:**
- Diseño mobile-first con Tailwind CSS
- Responsive en todas las vistas
- Navegación en sidebar con icono activo
- Feedback visual con mensajes de éxito/error
- Gradientes y colores según estado del torneo

---

## Funcionalidades Pendientes

### Sistema de Inscripción Automática de Jugadores (Feature Futura Principal)

**Estado actual:** Los equipos se crean manualmente por el organizador. El flujo completo del torneo funciona perfectamente con este enfoque.

**Funcionalidad propuesta para el futuro:**

#### Sistema de Inscripción de Jugadores
- [ ] Formulario de inscripción público para jugadores
- [ ] Selección de pareja (para deportes de duplas)
- [ ] Gestión de pagos de inscripción de jugadores
- [ ] Confirmación de inscripción vía email
- [ ] Lista de espera cuando se alcanza el límite

#### Administración de Inscripciones (Organizador)
- [ ] Listado de inscripciones por torneo
- [ ] Aprobar/rechazar inscripciones
- [ ] Marcar pagos como confirmados
- [ ] Editar/eliminar inscripciones
- [ ] Exportar lista de inscriptos

#### Formación Automática de Equipos desde Inscripciones
- [ ] Generar equipos automáticamente desde inscripciones
- [ ] Para deportes individuales: 1 jugador por equipo
- [ ] Para deportes de duplas: 2 jugadores por equipo
- [ ] Asignar nombres automáticos a equipos

---

### Funcionalidades Adicionales (Futuras)

#### Reportes y Exportaciones
- [ ] Reporte completo del torneo (PDF)
- [ ] Exportar fixture a PDF
- [ ] Exportar tabla de posiciones a PDF/Excel
- [ ] Exportar lista de participantes

#### Estadísticas Avanzadas del Torneo
- [ ] Goleadores / Máximos anotadores del torneo
- [ ] Jugador más valioso (MVP)
- [ ] Mejor equipo ofensivo/defensivo
- [ ] Partido con más goles/puntos
- [ ] Asistencia por fecha
- [ ] Estadísticas individuales de jugadores

#### Podio y Premios
- [ ] Vista mejorada de podio con ganadores
- [ ] Asignación formal de premios
- [ ] Generación de diplomas/certificados (PDF)
- [ ] Galería de fotos del torneo

#### Calendario y Recordatorios
- [ ] Vista de calendario visual con partidos programados
- [ ] Integración con Google Calendar (export .ics)
- [ ] Recordatorios automáticos por email

#### Galería y Multimedia
- [ ] Upload de fotos del torneo
- [ ] Galería pública de imágenes
- [ ] Videos de partidos (links YouTube)
- [ ] Streaming en vivo (futuro)

#### Sponsors
- [ ] Gestión de sponsors del torneo
- [ ] Logos en banner y reportes
- [ ] Niveles de sponsorship (oro, plata, bronce)

#### Torneos Recurrentes
- [ ] Plantilla de torneo
- [ ] Clonar torneo anterior
- [ ] Torneos periódicos (semanal, mensual)

---

## Roadmap de Implementación

### Estado Actual: ✅ Flujo Principal Completo

**El sistema está 100% funcional para gestionar torneos de principio a fin.** Los organizadores pueden:
1. Crear torneos con cualquier formato
2. Agregar jugadores y equipos manualmente
3. Sortear grupos (si aplica)
4. Generar fixture y llaves
5. Cargar resultados
6. Finalizar el torneo con campeón

### Próximas Features Recomendadas (Opcionales)

#### Feature 1: Sistema de Inscripción Automática (Prioridad Alta)
**Objetivo:** Permitir que los jugadores se inscriban solos a los torneos, reduciendo trabajo manual del organizador.

**Tareas:**
1. Formulario público de inscripción
2. Gestión de pagos de inscripción
3. Panel de administración de inscripciones
4. Formación automática de equipos desde inscripciones
5. Notificaciones de inscripción confirmada

**Tiempo estimado:** 2-3 semanas

#### Feature 2: Reportes y Exportaciones (Prioridad Media)
**Objetivo:** Generar documentos profesionales del torneo.

**Tareas:**
1. Exportar fixture a PDF
2. Exportar tabla de posiciones
3. Generar diplomas/certificados
4. Reporte completo del torneo

**Tiempo estimado:** 1-2 semanas

#### Feature 3: Estadísticas Avanzadas (Prioridad Media)
**Objetivo:** Proveer estadísticas detalladas del torneo.

**Tareas:**
1. Tabla de goleadores/anotadores
2. Estadísticas por jugador
3. Mejor equipo ofensivo/defensivo
4. Historial de rendimiento

**Tiempo estimado:** 1-2 semanas

#### Feature 4: Mejoras de UX (Prioridad Baja)
**Objetivo:** Mejorar experiencia visual y funcional.

**Tareas:**
1. Calendario visual de partidos
2. Galería de fotos
3. Integración con Google Calendar
4. Vista mejorada de podio

**Tiempo estimado:** 2-3 semanas

---

## Consideraciones Técnicas

### Performance
- [ ] Implementar caché para listado de torneos públicos
- [ ] Eager loading de relaciones en queries
- [ ] Paginación en listados grandes
- [ ] Optimización de imágenes (resize automático)

### Testing
- [ ] Tests unitarios para cálculos de avance
- [ ] Tests de integración para wizard de creación
- [ ] Tests de políticas de autorización
- [ ] Tests de generación de fixture

### Deployment
- [ ] Configurar storage público para imágenes
- [ ] Configurar colas para procesamiento de notificaciones
- [ ] Backup automático de base de datos
- [ ] Logs de actividad de torneos

---

## Notas Finales

### Decisiones de Diseño Tomadas

1. **Wizard en 2 pasos**: Para simplificar la creación inicial, dejando participantes y fixture para después de activar
2. **Soft deletes**: Permite recuperar torneos eliminados accidentalmente
3. **Storage local**: Por ahora imágenes en local, pero preparado para migrar a S3/MinIO
4. **Estados estrictos**: Un torneo solo puede editarse en borrador, evitando inconsistencias
5. **Cálculo automático**: Los equipos que avanzan se calculan automáticamente para evitar errores

### Posibles Mejoras Futuras

- [x] ~~Integración con pasarelas de pago (Mercado Pago)~~ ✅ **IMPLEMENTADO**
- [ ] App móvil nativa (React Native / Flutter)
- [ ] Sistema de rankings de jugadores
- [ ] Torneos multi-deporte (actualmente soporta Padel, Fútbol y Tenis)
- [ ] Integración con redes sociales
- [ ] Chatbot para consultas frecuentes
- [ ] Estadísticas avanzadas con IA

---

**Última actualización:** 2025-10-21
**Versión:** 2.0
**Estado:** 🎉 Flujo Principal Completo - Sistema 100% Funcional
