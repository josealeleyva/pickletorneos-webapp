# PRD — PickleTorneos
## Product Requirements Document

**Versión:** 1.0
**Fecha:** Marzo 2026
**Estado:** En producción (MVP completo)

---

## 1. Resumen Ejecutivo

**PickleTorneos** es una plataforma integral de gestión de torneos deportivos que conecta organizadores con jugadores, facilitando la administración completa de eventos deportivos desde la creación hasta la finalización.

### Propuesta de valor

| Para | Problema que resuelve | Solución |
|------|----------------------|----------|
| **Organizadores** | Gestión manual de torneos consume 5-10 hs por evento (Excel + WhatsApp) | Panel completo de administración con automatización |
| **Jugadores** | Falta de información centralizada y en tiempo real sobre sus torneos | App web con notificaciones, llaves, resultados y ranking |

### Deportes soportados
- Pádel (foco principal)
- Fútbol
- Tenis
- Otros (arquitectura preparada para expansión)

---

## 2. Objetivos del Producto

### Objetivos primarios
1. Ser la plataforma de referencia para gestión de torneos de pádel en Argentina.
2. Lograr que el primer torneo de cada organizador sea publicado con cero fricción.
3. Reducir el tiempo de administración de torneos en al menos un 80%.

### KPIs clave
- **Tasa de conversión gratis → pago:** >80%
- **Tasa de retención de organizadores:** >90%
- **Torneos gestionados al año 1:** 100+
- **NPS (Net Promoter Score):** >50

---

## 3. Usuarios Objetivo

### 3.1 Organizador de torneos
- Dueño o encargado de un complejo deportivo (pádel, fútbol, tenis)
- Organiza torneos amateurs o semiprofesionales de forma recurrente (1-4 por mes)
- Actualmente gestiona inscripciones y resultados de forma manual
- Nivel técnico: medio-bajo (necesita interfaz simple e intuitiva)

### 3.2 Jugador participante
- Deportista amateur que participa en torneos organizados por terceros
- Quiere información de sus partidos sin depender del organizador
- Accede principalmente desde el celular

### 3.3 Superadministrador
- Equipo interno de PickleTorneos
- Gestiona usuarios, configuraciones del sistema y métricas

---

## 4. Modelo de Negocio

### Pricing

| Torneo | Precio |
|--------|--------|
| Primer torneo | **GRATIS** (estrategia de adquisición) |
| Torneos siguientes | **$25.000 ARS** por torneo |

**Justificación:** Ahorra 5-10 hs de trabajo administrativo. ROI para el organizador: 60%-200%.

### Sistema de Referidos
- Organizador comparte su código único con otros organizadores.
- El referido recibe 20% de descuento en su primer torneo pago.
- El referidor recibe 1 torneo gratis cuando su referido paga.
- Créditos vencen a los 12 meses.

### Fuentes de ingreso futuras (Roadmap)
- Fee sobre inscripciones de jugadores (5-8%)
- Plan Premium con subdominio personalizado ($40.000/mes)
- Estadísticas avanzadas y reportes PDF
- Publicidad de marcas deportivas

---

## 5. Funcionalidades del Producto

### 5.1 Módulo de Autenticación y Perfiles

#### Organizadores
- Registro con email/contraseña
- Inicio y cierre de sesión seguro
- Gestión de perfil (nombre, email, teléfono, logo de organización)
- Gestión de múltiples complejos deportivos
- Cada complejo define sus canchas disponibles

#### Jugadores
- Registro con email/contraseña
- Perfil con foto, deporte principal, categoría
- Vinculación automática cuando un organizador los carga con su email

---

### 5.2 Módulo de Torneos (Core)

#### Wizard de creación (2 pasos)

**Paso 1 — Información básica:**
- Nombre del torneo
- Deporte
- Complejo deportivo asociado
- Fechas de inicio y fin
- Precio de inscripción (opcional)
- Banner/imagen del torneo

**Paso 2 — Configuración de formato:**
- Tipo de formato (ver sección 5.3)
- Número de grupos / tamaño de grupos
- Criterio de avance entre fases

#### Estados del torneo
```
borrador → activo → en_curso → finalizado
                              ↓
                          cancelado
```

Solo los torneos en `borrador` pueden editarse o eliminarse.

#### Acciones del organizador
- Publicar torneo (borrador → en_curso)
- Finalizar torneo (en_curso → finalizado)
- Cancelar torneo
- Ver historial de torneos organizados

---

### 5.3 Formatos de Competencia

| Formato | Descripción |
|---------|-------------|
| **Eliminación Directa** | Bracket clásico, perdedor queda eliminado |
| **Fase de Grupos + Eliminación** | Round Robin en grupos, clasificados a bracket |
| **Liga** | Todos contra todos, gana el de más puntos |

#### Configuración de Fase de Grupos
- Tamaño de grupo: 3, 4, 5 o 6 equipos
- Equipos que avanzan por grupo: solo 1°, 1° y 2°, 1° y 2° y 3°
- Mejores segundos/terceros para completar potencia de 2 (8, 16, 32, 64)
- Criterios de desempate: partidos ganados → diferencia de sets → diferencia de games → enfrentamiento directo

#### BYEs en brackets
- Se asignan automáticamente a los mejor clasificados cuando los clasificados no son potencia de 2
- Sembrado inteligente: 1° grupo A vs 2° grupo B en cruces

---

### 5.4 Módulo de Jugadores y Equipos

- Alta manual de jugadores por el organizador (nombre, apellido, email, teléfono, foto)
- Soporte de apellidos compuestos (ej. "De La Cruz")
- Búsqueda de jugadores ya registrados en el sistema
- Jugadores con `user_id` vinculado reciben notificaciones automáticas
- Formación de parejas (pádel) o equipos (fútbol)
- Asignación de categorías por deporte (8va, 7ma, 6ta para pádel / libre, +30, +40 para fútbol)
- Importación/exportación de jugadores desde Excel

---

### 5.5 Módulo de Sorteo y Grupos

- Sorteo automático aleatorio de equipos en grupos
- Sorteo manual con drag & drop
- Asignación de cabezas de serie para distribución equitativa
- Intercambio de equipos entre grupos
- Reseteo de sorteo

---

### 5.6 Módulo de Fixture y Programación

- **Fase de Grupos:** Generación Round Robin (todos contra todos dentro del grupo)
- **Liga:** Generación Round Robin global
- **Eliminación:** Bracket automático basado en equipos clasificados
- Programación de partidos: asignación de cancha, fecha y hora
- Prevención de conflictos de horarios en canchas (doble booking)
- Vista de fixture por grupo y por fecha
- Vista visual de bracket (estilo Wimbledon) en desktop y mobile
- Reseteo de fixture y llaves

---

### 5.7 Módulo de Resultados

- Carga de resultados por el organizador
- Validaciones de marcador según deporte (sets para pádel/tenis, goles para fútbol)
- Tabla de posiciones automática por grupo
- Cálculo automático: puntos, diferencia de sets/goles, partidos ganados
- Avance automático de equipos a fase de eliminación
- Desempate de mejores segundos según criterios configurados
- Avance automático de ganadores en brackets
- Generación automática de semifinales y final
- Determinación de campeón y podio

---

### 5.8 Módulo de Notificaciones

- Notificaciones por email a jugadores con detalles del partido (hora, cancha, oponentes)
- Envío individual o masivo
- Jobs en cola (procesamiento asíncrono) para no bloquear la UI
- Recordatorio automático configurable antes del partido
- Notificaciones del sistema de referidos (nuevo referido, crédito ganado)

---

### 5.9 Vista Pública de Torneos

- Accesible sin autenticación
- Muestra: información general, bracket/llaves, tabla de posiciones, fixture, resultados
- URL pública compartible

---

### 5.10 Panel de Administración (Superadmin)

- Gestión de usuarios (organizadores, jugadores)
- Gestión de roles y permisos (Spatie Permission)
- Configuración del sistema (precios, porcentajes de descuento, etc.)
- Métricas y estadísticas generales

---

### 5.11 Sistema de Pagos (MercadoPago)

- Integración completa con MercadoPago SDK
- Flujo de checkout con redirección
- Detección automática de descuentos (referido, crédito)
- Webhook para confirmar pagos y activar torneos
- Estados de pago: pendiente, pagado, gratuito, cancelado, vencido
- Middleware `VerificarPagoTorneo` protege el acceso al torneo

---

## 6. Arquitectura Técnica

### Stack
| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 10 (PHP 8.1+) |
| Frontend | Blade + Vite + Tailwind CSS |
| Base de datos | MySQL |
| Autenticación | Laravel Sanctum + autenticación web |
| Permisos | Spatie Laravel Permission |
| Pagos | MercadoPago SDK |
| Colas | Database-backed queues |
| Almacenamiento | Sistema de archivos local (preparado para S3) |

### Modelos principales
- `Torneo` — entidad central, relaciona todo
- `Equipo` — grupos de jugadores dentro de un torneo
- `Jugador` — puede ser usuario registrado o entrada manual
- `Partido` — unidad de competencia (puede pertenecer a grupo o llave)
- `Llave` — bracket de eliminación
- `Grupo` — agrupación de equipos en fase de grupos
- `ConfiguracionSistema` — parámetros configurables sin tocar código

### Roles
- `Superadmin` — acceso total
- `Organizador` — crea y gestiona torneos de sus complejos
- `Jugador` — consulta torneos en los que participa

---

## 7. Diseño y UX

### Política responsive (obligatoria)
Todas las vistas deben funcionar en mobile, tablet y desktop.

| Dispositivo | Ancho mínimo |
|-------------|-------------|
| Mobile | < 640px |
| Tablet | >= 640px (sm) |
| Desktop | >= 768px (md) / >= 1024px (lg) |

### Reglas clave
- Grids responsive con breakpoints (nunca grid fijo)
- Tablas con scroll horizontal en mobile (`overflow-x-auto`)
- Formularios apilados en mobile, horizontales en desktop
- Sidebar con menú hamburguesa en mobile
- Tamaños de fuente adaptables (`text-xl md:text-2xl lg:text-3xl`)

---

## 8. Estado de Implementación

### Completado ✅
- Wizard de creación de torneos (2 pasos)
- Integración MercadoPago
- Middleware de verificación de pago
- Gestión de jugadores y equipos
- Importación/exportación de jugadores por Excel
- Sorteo de grupos (aleatorio y manual)
- Generación de fixture Round Robin
- Generación de brackets de eliminación
- Programación de partidos con canchas y horarios
- Carga de resultados con validaciones
- Tabla de posiciones automática
- Avance automático entre fases
- Desempate de mejores segundos
- Vista visual de bracket (desktop y mobile)
- Sistema de notificaciones por email
- Vista pública de torneos
- Sistema de referidos completo
- Panel de administración responsive
- Jobs programados para expiración de créditos y referidos

### Pendiente / Futuro ⏳
- Sistema de inscripción automática de jugadores (actualmente manual)
- Reportes y estadísticas avanzadas
- Exportación de resultados a PDF
- Diplomas digitales personalizados
- Notificaciones por WhatsApp
- Plan Premium con subdominio personalizado
- Fee sobre inscripciones de jugadores (Fase 2)
- App nativa mobile (iOS/Android)

---

## 9. Rutas Principales

### Web (organizador)
| Ruta | Descripción |
|------|-------------|
| `GET /torneos` | Lista de torneos del organizador |
| `GET /torneos/crear/paso-1` | Wizard paso 1 |
| `GET /torneos/{id}/paso-2` | Wizard paso 2 |
| `GET /torneos/{id}` | Panel de gestión del torneo |
| `GET /torneos/{id}/equipos` | Gestión de equipos |
| `GET /torneos/{id}/grupos` | Sorteo y grupos |
| `GET /torneos/{id}/fixture` | Fixture y programación |
| `GET /torneos/{id}/llaves` | Bracket de eliminación |
| `GET /torneos/{id}/pago/checkout` | Checkout MercadoPago |

### Públicas
| Ruta | Descripción |
|------|-------------|
| `GET /t/{slug}` | Vista pública del torneo |
| `GET /invitacion/{codigo}` | Landing de referido |

### API (Sanctum)
| Ruta | Descripción |
|------|-------------|
| `POST /api/login` | Autenticación |
| `GET /api/user` | Perfil del usuario autenticado |

---

## 10. Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|-------------|---------|------------|
| Baja adopción inicial | Media | Alto | Primer torneo gratis + demo en vivo |
| Problemas técnicos durante torneos | Baja | Alto | Testing exhaustivo + soporte durante el evento |
| Competencia de soluciones gratuitas | Media | Medio | UX superior, soporte local y personalizado |
| Estacionalidad (menos torneos en invierno) | Alta | Medio | Diversificar deportes y geografía |
| Costos de infraestructura al escalar | Media | Bajo | Arquitectura escalable + S3 preparado |

---

## 11. Proyección Financiera (Resumen)

| Período | Organizadores | Torneos/mes | Ingreso mensual |
|---------|--------------|-------------|-----------------|
| Año 1 (inicio) | 2-10 | 8-40 | $150k–$875k ARS |
| Año 1 (cierre) | 10+ | 40+ | $875k+ ARS |
| Año 2 | 25-30 | 100-120 | $2.5M ARS |
| Año 3 | 50-80 | 200-320 | $5M–$8M ARS |

**Punto de equilibrio:** menos de 1 torneo pago por mes.
**Margen bruto estimado:** ~92% por torneo.

---

## 12. Glosario

| Término | Definición |
|---------|-----------|
| **Torneo** | Evento deportivo gestionado en la plataforma |
| **Organizador** | Usuario que crea y administra torneos |
| **Complejo** | Instalación deportiva con múltiples canchas |
| **Fixture** | Programación de partidos dentro de un torneo |
| **Llave** | Bracket de eliminación directa |
| **BYE** | Clasificación directa a la siguiente ronda sin jugar |
| **Mejor segundo** | Segundo clasificado de un grupo con mejor rendimiento para completar bracket |
| **Crédito referido** | Torneo gratuito ganado por el referidor al activarse un referido |
| **Cabeza de serie** | Equipo preferente para distribución equitativa en sorteos |
| **Round Robin** | Formato donde todos los equipos del grupo se enfrentan entre sí |

---

*Última actualización: Marzo 2026 — v1.0*
