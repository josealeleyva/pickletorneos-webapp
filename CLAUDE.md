# CLAUDE.md

Este archivo proporciona orientación a Claude Code (claude.ai/code) cuando trabaja con código en este repositorio.

## Descripción del Proyecto

**Punto de Oro** es una plataforma integral de gestión de torneos deportivos construida con Laravel 10. El sistema permite a los organizadores crear y administrar torneos deportivos (principalmente Padel, Fútbol y Tenis) con diferentes formatos de competencia, incluyendo eliminación directa, fase de grupos + eliminación, y formato de liga.

## Stack Tecnológico

- **Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Blade templates con Vite
- **Base de Datos:** MySQL
- **Autenticación:** Laravel Sanctum (API tokens) + autenticación web estándar
- **Permisos:** Spatie Laravel Permission
- **Integración de Pagos:** MercadoPago SDK
- **Colas:** Database-backed queues para notificaciones
- **Almacenamiento:** Sistema de archivos local (preparado para migración a S3)
- **CSS Framework:** Tailwind CSS (para diseño responsive)

## 📱 Política de Diseño Responsive

**IMPORTANTE:** Todas las vistas, pantallas y componentes del sistema DEBEN ser completamente responsive y funcionar correctamente en dispositivos móviles, tablets y desktop.

### Breakpoints de Tailwind CSS (usados en el proyecto)

- **Mobile**: `< 640px` (sin prefijo)
- **Tablet**: `sm: >= 640px`
- **Desktop small**: `md: >= 768px`
- **Desktop**: `lg: >= 1024px`
- **Desktop large**: `xl: >= 1280px`

### Reglas Obligatorias de Responsive Design

1. **Grids**: Usar siempre grid responsive con breakpoints
   ```html
   <!-- ✅ CORRECTO -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

   <!-- ❌ INCORRECTO -->
   <div class="grid grid-cols-4 gap-4">
   ```

2. **Tablas**: Envolver en contenedor con scroll horizontal para mobile
   ```html
   <!-- ✅ CORRECTO -->
   <div class="overflow-x-auto -mx-4 md:mx-0">
       <table class="min-w-full">...</table>
   </div>
   ```

3. **Padding/Margin**: Reducir en mobile
   ```html
   <!-- ✅ CORRECTO -->
   <div class="p-4 md:p-6">
   <div class="px-4 md:px-6 py-4">
   ```

4. **Texto**: Ajustar tamaños de fuente
   ```html
   <!-- ✅ CORRECTO -->
   <h1 class="text-xl md:text-2xl lg:text-3xl">
   <p class="text-sm md:text-base">
   ```

5. **Sidebar/Menús**: Usar menú hamburguesa en mobile
   - Desktop: Sidebar fijo visible
   - Mobile: Sidebar oculto, toggle con botón hamburguesa
   - Overlay oscuro al abrir en mobile

6. **Forms**: Stack en mobile, horizontal en desktop
   ```html
   <!-- ✅ CORRECTO -->
   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
       <input ...>
       <input ...>
   </div>
   ```

7. **Cards**: Apilables en mobile
   ```html
   <!-- ✅ CORRECTO -->
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
   ```

### Testing Responsive

Al desarrollar nuevas vistas, SIEMPRE probar en:
- ✅ Chrome DevTools (F12) → Toggle device toolbar
- ✅ Tamaños: 375px (iPhone), 768px (iPad), 1024px (Desktop)
- ✅ Verificar que no haya overflow horizontal
- ✅ Verificar que todos los botones sean clickeables
- ✅ Verificar que las tablas tengan scroll si son anchas

### Componentes Responsive del Proyecto

**Panel de Organizador**: ✅ Completamente responsive
**Panel de Administrador**: ✅ Completamente responsive (implementado Oct 2025)
**Vistas públicas**: ⚠️ Verificar según se desarrollen

### Ejemplo de Vista Responsive Completa

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 md:px-6 py-4 md:py-6">
    <!-- Header -->
    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold mb-4 md:mb-6">
        Título de la Vista
    </h1>

    <!-- Grid de Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
        <!-- Cards aquí -->
    </div>

    <!-- Tabla Responsive -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full">
                <!-- Tabla aquí -->
            </table>
        </div>
    </div>
</div>
@endsection
```

**Nota:** Cualquier vista nueva que no sea responsive será rechazada en code review.

## Comandos de Desarrollo

### Instalación Inicial
```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias del frontend
npm install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Poblar base de datos con datos iniciales (deportes, categorías, formatos, roles, permisos y usuarios demo)
php artisan db:seed
```

### Ejecutar la Aplicación
```bash
# Iniciar servidor de desarrollo de Laravel
php artisan serve

# Compilar assets del frontend en modo desarrollo (watch mode)
npm run dev

# Compilar assets del frontend para producción
npm run build

# Procesar trabajos en cola (para notificaciones por email)
php artisan queue:work
```

### Operaciones de Base de Datos
```bash
# Crear una nueva migración
php artisan make:migration create_example_table

# Crear un nuevo seeder
php artisan make:seeder ExampleSeeder

# Migración fresca con seed (ADVERTENCIA: destruye todos los datos)
php artisan migrate:fresh --seed

# Revertir última migración
php artisan migrate:rollback
```

### Testing
```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar suite específica de tests
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Ejecutar un archivo de test específico
php artisan test tests/Feature/ExampleTest.php
```

### Calidad de Código
```bash
# Generar documentación de API con Scribe
php artisan scribe:generate
```

### Helpers de Artisan
```bash
# Listar todas las rutas
php artisan route:list

# Listar rutas de un path específico
php artisan route:list --path=torneos

# Limpiar todos los cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Crear un nuevo controlador
php artisan make:controller ExampleController

# Crear un nuevo modelo con migración
php artisan make:model Example -m
```

## Arquitectura General

### Modelos del Dominio Principal

La aplicación gira en torno a **Torneos** con las siguientes relaciones clave:

```
Torneo
├── belongsTo: Deporte (Padel, Futbol, Tenis)
├── belongsTo: ComplejoDeportivo
├── belongsTo: User (organizador)
├── belongsTo: FormatoTorneo (eliminación directa, grupos + eliminación, liga)
├── belongsTo: TamanioGrupo (tamaño de grupo: 3-6 equipos por grupo)
├── belongsTo: AvanceGrupo (criterio de avance desde grupos)
├── belongsTo: PagoTorneo (estado de pago)
├── hasMany: Grupo
├── hasMany: Equipo
├── hasMany: Llave (llaves de eliminación)
├── hasMany: Partido
└── hasMany: Inscripcion

Equipo
├── belongsTo: Torneo
├── belongsTo: Grupo (nullable)
├── belongsToMany: Jugador (a través de pivot equipo_jugador)
└── hasMany: Partido (como equipo1 o equipo2)

Jugador
├── belongsTo: User (nullable - pueden ser usuarios registrados o entradas manuales)
├── belongsTo: User (organizador - quien creó el registro del jugador)
└── belongsToMany: Equipo (a través de pivot equipo_jugador)

Partido
├── belongsTo: Torneo
├── belongsTo: Equipo (equipo1)
├── belongsTo: Equipo (equipo2)
├── belongsTo: Cancha
├── belongsTo: Llave (nullable)
└── belongsTo: Grupo (nullable)

Llave
├── belongsTo: Torneo
├── belongsTo: Equipo (equipo1, nullable)
├── belongsTo: Equipo (equipo2, nullable)
├── belongsTo: Equipo (ganador, nullable)
└── hasOne: Partido
```

### Máquina de Estados del Torneo

Los torneos siguen una progresión de estados estricta:

```
borrador → activo → en_curso → finalizado
                              ↓
                          cancelado
```

**Importante:** Solo los torneos en estado `borrador` pueden ser editados o eliminados (aplicado por `TorneoPolicy`).

### Formatos de Torneo (Enums)

Definidos en `app/Enums/TipoFormatoTorneo.php`:

1. **ELIMINACION_DIRECTA**: Bracket de eliminación directa (sin grupos)
2. **FASE_GRUPOS_ELIMINACION**: Fase de grupos seguida de bracket de eliminación
3. **LIGA**: Liga todos contra todos (round-robin)

Métodos helper del enum:
- `tieneGrupos()`: Retorna true si el formato requiere grupos
- `esEliminacionDirecta()`: Verifica si es eliminación directa
- `esLiga()`: Verifica si es formato de liga
- `esFaseGrupos()`: Verifica si es grupos + eliminación

### Roles de Usuario (Spatie Permissions)

Definidos en `app/Enums/Roles.php`:

- **Superadmin**: Acceso completo al sistema
- **Organizador**: Puede crear/gestionar torneos y sus complejos
- **Jugador**: Jugadores que se registran y participan en torneos

### Sistema de Configuración (ConfiguracionSistema)

El sistema utiliza el modelo `ConfiguracionSistema` para almacenar valores configurables que afectan el comportamiento de la aplicación. Este patrón permite cambiar configuraciones sin modificar código.

**Modelo:** `app/Models/ConfiguracionSistema.php`

**Uso:**
```php
// Obtener un valor de configuración
$valor = ConfiguracionSistema::get('clave', $valorPorDefecto);

// Ejemplo: obtener precio del torneo
$precio = ConfiguracionSistema::get('precio_torneo', 25000);
```

**Configuraciones del Sistema de Referidos:**

- `precio_torneo` (decimal): Precio base por torneo en pesos argentinos (ej: 25000)
- `porcentaje_descuento_referido` (integer): Porcentaje de descuento para referidos (ej: 20 = 20%)
- `porcentaje_credito_referidor` (integer): Porcentaje del precio del torneo que recibe el referidor como crédito (ej: 100 = 100% = 1 torneo gratis)

**Tipos de datos soportados:**
- `integer`: Números enteros
- `decimal`: Números decimales
- `boolean`: Valores booleanos (true/false)
- `string`: Texto
- `json`: Estructuras de datos JSON

**Importante:** Al agregar nuevas configuraciones, usar el patrón de seeder:
1. Crear/actualizar seeder en `database/seeders/ConfiguracionPreciosSeeder.php`
2. Usar `updateOrCreate` para evitar duplicados
3. Agregar el seeder al `DatabaseSeeder`
4. Ejecutar `php artisan db:seed --class=NombreDelSeeder`

**Ventajas:**
- Cambios sin modificar código
- Valores centralizados
- Fácil de mantener y auditar
- Los cálculos se ajustan automáticamente cuando cambia la configuración base

### Wizard de Creación de Torneo

La creación de torneos sigue un patrón de wizard de 2 pasos:

1. **Paso 1** (`/torneos/crear/paso-1`): Información básica (nombre, deporte, complejo, fechas, precio, banner)
2. **Paso 2** (`/torneos/{torneo}/paso-2`): Configuración de formato (tipo de formato, grupos, criterio de avance)

La integración de pago vía MercadoPago ocurre después del Paso 2 antes de que el torneo sea completamente accesible.

### Flujo de Pagos (MercadoPago)

El middleware `VerificarPagoTorneo` protege el acceso al torneo basándose en el estado del pago:

- **pendiente**: El usuario solo puede acceder a páginas de checkout o eliminar el torneo
- **pagado/gratuito**: Acceso completo a la gestión del torneo
- **cancelado/vencido**: Solo puede eliminar el torneo

Los estados de pago se rastrean en la tabla `pagos_torneos`.

### Generación de Fixture y Llaves

El sistema genera partidos basándose en el formato del torneo:

- **Fase de Grupos**: Usa algoritmo Round-Robin (ver `TorneoFixtureController@generar`)
- **Bracket de Eliminación**: Crea estructura de bracket basado en equipos que avanzan (ver `TorneoLlaveController@generate`)
- **Programación de Partidos**: Asigna canchas (`Cancha`) y horarios para evitar conflictos (ver `TorneoFixtureController@programar`)

### Sistema de Notificaciones

La aplicación usa trabajos en cola para enviar notificaciones:

- **Job**: `app/Jobs/EnviarNotificacionPartido.php`
- **Mail**: `app/Mail/PartidoNotificacionMail.php`
- **Queue**: Database-backed (asegurar que `queue:work` esté corriendo)

Las notificaciones se envían a los jugadores antes de los partidos vía email con detalles del partido (hora, cancha, oponentes).

## Controladores Principales y sus Responsabilidades

- **TorneoController**: CRUD de torneos, flujo del wizard, publicación y finalización
- **TorneoEquipoController**: Gestión de equipos dentro de los torneos
- **TorneoGrupoController**: Asignación de grupos y lógica de sorteo
- **TorneoFixtureController**: Generación de partidos, programación y carga de resultados para fases de grupos
- **TorneoLlaveController**: Generación y gestión de brackets de eliminación
- **PagoController**: Integración con MercadoPago para pagos de torneos, detección de descuentos y créditos de referidos
- **JugadorController**: Gestión de jugadores (tanto usuarios registrados como entradas manuales)
- **ReferidoController**: Dashboard de referidos, página de invitación, validación de códigos

## Sistema de Referidos

**Punto de Oro** cuenta con un sistema completo de referidos que incentiva a los organizadores a invitar a otros organizadores a la plataforma.

### Modelos del Sistema de Referidos

```
User
├── codigo_referido (string, único): Código generado automáticamente al registrarse
├── referido_por_id (FK users): Quién lo refirió
├── total_referidos_activos (int): Contador de referidos que se activaron
├── hasMany: referidos (usuarios que refirió)
├── belongsTo: referidor (quien lo refirió)
└── hasMany: creditosReferidos

Referido
├── referidor_id (FK users): Quien refiere
├── referido_id (FK users): Quien fue referido
├── fecha_registro (datetime)
├── estado (enum): pendiente, activo, expirado
├── fecha_activacion (datetime, nullable)
└── método activar(): Acredita crédito al referidor

CreditoReferido
├── user_id (FK users): Referidor que recibe el crédito
├── referido_id (FK users): Quien generó el crédito
├── monto (decimal): Monto del crédito (configurable vía ConfiguracionSistema)
├── estado (enum): disponible, usado, expirado
├── fecha_acreditacion (datetime)
├── fecha_vencimiento (datetime): 12 meses desde acreditación
├── torneo_usado_id (FK torneos, nullable)
├── fecha_uso (datetime, nullable)
└── método usar(Torneo): Marca crédito como usado
```

### Configuraciones del Sistema de Referidos

Todas configurables vía `ConfiguracionSistema`:

- **precio_torneo** (decimal): Precio base por torneo en pesos (ej: 25000)
- **porcentaje_descuento_referido** (integer): Descuento para el referido en su primer torneo pago (ej: 20 = 20%)
- **porcentaje_credito_referidor** (integer): Porcentaje del precio que recibe el referidor como crédito (ej: 100 = 1 torneo gratis)

### Flujo del Sistema de Referidos

**1. Registro con Código:**
- Usuario se registra → código generado automáticamente (formato: `PO` + 6 caracteres aleatorios)
- Si usa código de otro → se crea registro en tabla `referidos` con estado "pendiente"
- Referidor recibe email de notificación (`NuevoReferidoNotification`)

**2. Beneficio para el Referido:**
- **Primer torneo**: GRATIS automáticamente (configuración estándar de la plataforma)
- **Segundo torneo (primer pago)**: Descuento del 20% aplicado automáticamente en checkout
  - Se muestra precio original tachado, descuento y precio final
  - MercadoPago recibe el monto con descuento aplicado
  - Se guarda info del descuento en tabla `pagos_torneos`

**3. Activación del Referido:**
- Cuando el referido paga su segundo torneo:
  - `ReferidoService::verificarActivacionReferido()` se ejecuta en el callback de pago
  - Estado del referido cambia de "pendiente" a "activo"
  - Se crea un `CreditoReferido` para el referidor (valor: 100% del precio del torneo)
  - Referidor recibe email celebratorio (`ReferidoActivadoNotification`)
  - Contador `total_referidos_activos` del referidor se incrementa

**4. Uso del Crédito por el Referidor:**
- Al crear un torneo, en el checkout se muestra:
  - Box verde destacado si tiene crédito disponible
  - Monto del crédito y fecha de vencimiento
  - Botón "Usar Crédito Gratis"
- Al usar el crédito:
  - Crédito cambia a estado "usado"
  - Se crea `PagoTorneo` con estado "gratuito" y referencia al crédito
  - Usuario accede al torneo inmediatamente sin pagar

**5. Expiración:**
- **Créditos**: Expiran 12 meses después de acreditación (Job: `ExpirarCreditosVencidos`, diario 2 AM)
- **Referidos pendientes**: Expiran 6 meses después de registro si no activaron (Job: `ExpirarReferidosPendientes`, diario 3 AM)

### Componentes Clave del Sistema de Referidos

**Servicio: `app/Services/ReferidoService.php`**
- `aplicarDescuentoReferido(User, Torneo)`: Calcula descuento del 20% para primer torneo pago
- `verificarActivacionReferido(PagoTorneo)`: Verifica si debe activar referido y acreditar crédito
- `obtenerCreditoDisponible(User)`: Obtiene el crédito más antiguo disponible (FIFO)
- `usarCreditoReferido(User, Torneo)`: Marca crédito como usado y crea pago gratuito

**Controlador: `app/Http/Controllers/ReferidoController.php`**
- `dashboard()`: Muestra código, estadísticas, lista de referidos, créditos disponibles
- `invitacion($codigo)`: Landing page personalizada para compartir código
- `validarCodigo(Request)`: Validación AJAX de código en formulario de registro

**Modificaciones en PagoController:**
- `checkout()`: Detecta descuentos y créditos disponibles, ajusta precio en MercadoPago
- `success()`: Verifica activación de referido después de pago exitoso
- `usarCredito()`: Endpoint POST para usar crédito de referido
- `webhook()`: También verifica activación en webhook de MercadoPago

**Notificaciones:**
- `NuevoReferidoNotification`: Email cuando alguien se registra con tu código
- `ReferidoActivadoNotification`: Email cuando ganas un torneo gratis (referido se activó)

**Vistas:**
- `resources/views/referidos/dashboard.blade.php`: Dashboard completo con stats y opciones de compartir
- `resources/views/referidos/invitacion.blade.php`: Landing page pública para invitaciones
- `resources/views/pagos/checkout.blade.php`: Muestra descuentos y opción de usar crédito
- `resources/views/emails/nuevo_referido.blade.php`: Email al referidor
- `resources/views/emails/referido_activado.blade.php`: Email celebratorio con instrucciones

**Jobs Programados:**
- `ExpirarCreditosVencidos`: Ejecuta diariamente a las 2 AM
- `ExpirarReferidosPendientes`: Ejecuta diariamente a las 3 AM

### Rutas del Sistema de Referidos

```php
// Públicas
GET  /invitacion/{codigo}           -> Página de invitación
POST /validar-codigo-referido       -> Validación AJAX de código

// Autenticadas
GET  /referidos/dashboard            -> Dashboard de referidos
POST /torneos/{torneo}/pago/usar-credito -> Usar crédito en pago
```

### Reglas de Negocio del Sistema de Referidos

1. **Códigos Únicos**: Cada usuario tiene un código único, generado automáticamente al registrarse
2. **Descuento Único**: El descuento del 20% solo aplica en el PRIMER torneo pago del referido
3. **Límite de Tiempo**: El descuento expira 6 meses después del registro si no se usa
4. **Activación**: El referido se activa solo cuando paga su segundo torneo (primer pago real)
5. **Créditos FIFO**: Los créditos se usan en orden de antigüedad (primero el más viejo)
6. **Vencimiento de Créditos**: 12 meses desde que se acreditan
7. **No Acumulables**: El descuento de referido y el crédito gratis no se pueden combinar en el mismo torneo
8. **Tracking Completo**: Todos los descuentos y créditos usados quedan registrados en `pagos_torneos`

### Estado del Sistema de Referidos

✅ **100% Funcional y Listo para Producción**

- Base de datos y modelos completos
- Generación automática de códigos
- Formulario de registro con validación
- Dashboard visual con estadísticas
- Página de invitación personalizada
- Detección automática de descuentos
- Integración completa con flujo de pagos
- Sistema de créditos gratis
- Notificaciones por email
- Jobs programados para expiración
- Todas las configuraciones dinámicas

**Documentación adicional:** Ver `docs/Referidos.md` para especificación completa del sistema.

## Reglas de Negocio Importantes

1. **Propiedad de Complejos**: Los organizadores solo pueden crear torneos en complejos que les pertenecen (validado en `TorneoPolicy` y controladores)

2. **Alcance de Jugadores**: Los jugadores están limitados al organizador que los creó (`organizador_id`). Los jugadores vinculados a usuarios registrados (`user_id`) son globales pero aparecen en listas específicas del organizador.

3. **Apellidos Compuestos**: Al crear jugadores, el sistema maneja apellidos compuestos (ej. "De La Cruz") usando todo lo que está después de la primera palabra como apellido.

4. **Restricciones de Fixture**:
   - Los partidos solo pueden programarse en canchas que pertenecen al complejo del torneo
   - El sistema previene reservas dobles de canchas al mismo tiempo
   - Los resultados solo pueden cargarse después de que el torneo sea publicado (`estado = 'en_curso'`)

5. **Avance de Grupos**: Cuando se usa el formato "Fase de Grupos + Eliminación", el sistema calcula:
   - Total de equipos = `numero_grupos × tamanio_grupo`
   - Equipos que avanzan = `(directos × numero_grupos) + mejores_segundos`
   - Esto se muestra en tiempo real en la UI del wizard

6. **Soft Deletes**: Los torneos, equipos y otros modelos clave usan soft deletes para prevenir pérdida accidental de datos.

## Estrategia de Testing

- **Unit Tests**: Se enfocan en lógica de negocio en modelos y enums (ej. cálculos de avance, verificaciones de formato)
- **Feature Tests**: Prueban flujos de controladores, especialmente el wizard de creación de torneos y políticas de autorización
- **Base de Datos**: Los tests usan la configuración de base de datos de prueba por defecto (ver `phpunit.xml`)

Al escribir tests, usar los seeders existentes para configurar datos de prueba consistentes.

## Datos de Prueba (Seeding)

El `DatabaseSeeder` crea datos demo para desarrollo:

- **Deportes**: Padel, Futbol, Tenis
- **Usuarios**:
  - Superadmin: `superadmin@puntodeoro.com` / `1234`
  - Organizador 1: `organizador1@puntodeoro.com` / `1234` (Padel Center Rosario)
  - Organizador 2: `organizador2@puntodeoro.com` / `1234` (Complejo La Cancha)
- **Formatos**: Eliminación Directa, Fase de Grupos + Eliminación, Liga
- **Categorías**: Categorías específicas por deporte (Masculino, Femenino, Mixto, etc.)
- **Tamaños de Grupo**: 3, 4, 5, 6 equipos por grupo
- **Criterios de Avance**: Primero de cada grupo, primero + mejores segundos, primeros 2 de cada grupo

El `TorneoConEquiposSeeder` opcional puede descomentarse en `DatabaseSeeder` para crear un torneo completo con equipos y partidos para testing.

## Convenciones de Estructura de Archivos

- **Controllers**: Siguen nomenclatura de recursos (`TorneoController`, `JugadorController`)
- **Models**: Nombres singulares (`Torneo`, `Equipo`, `Jugador`)
- **Views**: Ubicadas en `resources/views/` con estructura de carpetas que coincide con controladores
- **Migrations**: Usan nombres descriptivos con timestamps
- **Seeders**: Nombrados `{Entity}Seeder` o `{Entity}sSeeder`

## Rutas de API

La aplicación tiene dos sistemas de rutas:

1. **Rutas Web** (`routes/web.php`): UI basada en Blade para organizadores
2. **Rutas API** (`routes/api.php`): API RESTful con autenticación Sanctum

Las rutas API tienen prefijo `/api` y están protegidas por middleware `auth:sanctum`. Los endpoints actuales se enfocan en gestión de usuarios, roles y permisos.

## Patrones Comunes de Desarrollo

### Crear una Nueva Funcionalidad de Torneo

1. Actualizar el modelo `Torneo` si se necesitan nuevos campos
2. Crear/modificar la migración
3. Actualizar el controlador apropiado (`TorneoController` para general, controladores específicos para sub-funcionalidades)
4. Actualizar la policy (`TorneoPolicy`) para autorización
5. Crear/actualizar vistas Blade
6. Agregar rutas a `routes/web.php`
7. Probar con datos demo usando seeders

### Agregar un Nuevo Deporte

1. Agregar entrada en `database/seeders/DatabaseSeeder.php`
2. Crear categorías en `database/seeders/CategoriasSeeder.php`
3. Actualizar cualquier lógica específica del deporte en controladores (ej. validación de tamaño de equipo)

### Trabajar con Resultados de Partidos

Los resultados se cargan a través de `TorneoFixtureController@cargarResultado` (fase de grupos) y `TorneoLlaveController@cargarResultado` (eliminación). Ambos actualizan el modelo `Partido` y avanzan equipos automáticamente según el formato del torneo.

## Consideraciones de Despliegue

- **Storage**: Actualmente usa disco local. La migración a S3 está preparada (`league/flysystem-aws-s3-v3` instalado)
- **Queue Worker**: Producción requiere un proceso de queue worker (`php artisan queue:work`) para notificaciones
- **MercadoPago**: Asegurar que la URL del webhook esté configurada y accesible desde los servidores de MercadoPago
- **Environment**: Verificar `.env` para configuraciones de producción (base de datos, mail, credenciales de MercadoPago)

## Estado del Proyecto

### ✅ Flujo Completo de Torneo Implementado

El sistema está **100% funcional** para gestionar torneos de principio a fin:

- ✅ Creación de torneos con wizard de 2 pasos
- ✅ Integración completa con MercadoPago para pagos
- ✅ Gestión manual de jugadores y equipos
- ✅ Sorteo y asignación de grupos (aleatorio y manual)
- ✅ Generación de fixture (Round Robin para grupos y liga)
- ✅ Generación de llaves de eliminación (brackets visuales)
- ✅ Programación de partidos con canchas y horarios
- ✅ Carga de resultados y avance automático de equipos
- ✅ Tabla de posiciones automática
- ✅ Sistema de notificaciones por email
- ✅ Finalización de torneo con determinación de campeón
- ✅ Vista pública de torneos

### Funcionalidades Futuras Opcionales

La única funcionalidad principal pendiente es:

- **Sistema de Inscripción Automática**: Actualmente los equipos se crean manualmente. En el futuro se puede agregar un sistema donde los jugadores se inscriban solos y los equipos se formen automáticamente.

Otras mejoras futuras incluyen reportes PDF, estadísticas avanzadas, y calendario visual. Ver [docs/TORNEOS.md](docs/TORNEOS.md) para el roadmap completo.

## Documentación

Documentación adicional disponible en el directorio `docs/`:

- **[docs/TORNEOS.md](docs/TORNEOS.md)**: Documentación completa del sistema de torneos con estado de implementación
- **[docs/modeloDeNegocio.md](docs/modeloDeNegocio.md)**: Modelo de negocio y estrategia de precios
- **[docs/requisitos.md](docs/requisitos.md)**: Especificación completa de requerimientos
- **[docs/Referidos.md](docs/Referidos.md)**: Especificación de sistemas de referidos

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.30
- laravel/framework (LARAVEL) - v10
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v10 rules ===

## Laravel 10

- Use the `search-docs` tool to get version specific documentation.
- Middleware typically live in `app/Http/Middleware/` and service providers in `app/Providers/`.
- There is no `bootstrap/app.php` application configuration in Laravel 10:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`
- When using Eloquent model casts, you must use `protected $casts = [];` and not the `casts()` method. The `casts()` method isn't available on models in Laravel 10.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
</laravel-boost-guidelines>