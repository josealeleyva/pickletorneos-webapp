# Diseño: Integración DUPR en PickleTorneos

**Fecha:** 2026-04-27  
**Enfoque elegido:** A — SSO OAuth2 + resultados en cola asíncrona  
**Entorno inicial:** UAT (`uat.mydupr.com`)  
**Credenciales:**
- Client ID: `7725216610`
- Client Key: `test-ck-ab5bfcaa-8a25-4bff-ff71-63604d7dd806`
- Client Secret: `test-cs-446890c8cf3f41e2f9031a44f383348a`

---

## Principios de la integración

- DUPR es **completamente opcional** para jugadores — no bloquea el registro ni el uso de la plataforma.
- Un jugador puede vincular su cuenta DUPR desde su perfil en cualquier momento.
- El organizador decide por torneo si DUPR es requerido o no (`dupr_requerido`).
- Por cada categoría de un torneo con DUPR habilitado, se pueden definir opcionalmente un rating mínimo y máximo.
- El envío de resultados a DUPR es automático y asíncrono (no bloquea al organizador aunque la API de DUPR falle).
- El `dupr_id` **nunca** se ingresa manualmente — solo se obtiene via SSO OAuth2 de DUPR.

---

## 1. Cambios en el modelo de datos

### Tabla `jugadores`
| Campo | Tipo | Notas |
|---|---|---|
| `dupr_id` | string nullable, unique | Guardado via SSO, nunca editable manualmente |
| `rating_singles` | decimal(4,2) nullable | Cache local del rating de singles |
| `rating_doubles` | decimal(4,2) nullable | Cache local del rating de dobles |
| `dupr_sincronizado_at` | timestamp nullable | Última sincronización de ratings |

### Tabla `users`
| Campo | Tipo | Notas |
|---|---|---|
| `dupr_access_token` | text nullable | Token OAuth para llamadas en nombre del jugador |
| `dupr_token_expires_at` | timestamp nullable | Expiración del token |

### Tabla `torneos`
| Campo | Tipo | Notas |
|---|---|---|
| `dupr_requerido` | boolean, default false | Si true, solo jugadores con dupr_id pueden inscribirse |

### Tabla `categoria_torneo` (pivot)
| Campo | Tipo | Notas |
|---|---|---|
| `dupr_rating_min` | decimal(4,2) nullable | Solo aplica si torneo->dupr_requerido |
| `dupr_rating_max` | decimal(4,2) nullable | Solo aplica si torneo->dupr_requerido |

### Tabla `partidos`
| Campo | Tipo | Notas |
|---|---|---|
| `dupr_partido_id` | string nullable | ID del partido en el sistema DUPR |
| `dupr_sincronizado` | boolean, default false | Si el resultado fue enviado exitosamente |
| `dupr_sincronizado_at` | timestamp nullable | Momento de sincronización exitosa |
| `dupr_error` | text nullable | Último error del job para diagnóstico |

---

## 2. Componentes nuevos

### `app/Services/DuprService.php`
Wrapper de toda la API DUPR. Encapsula autenticación server-to-server y todas las llamadas a la API.

**Métodos:**
- `obtenerToken(): string` — Llama a `POST /api/auth/v1.0/token` con `x-authorization: base64(ClientKey:ClientSecret)`. Cachea el token resultante.
- `obtenerRatingJugador(string $duprId): array` — Devuelve `['singles' => float, 'doubles' => float]`.
- `crearPartido(Partido $partido): string` — Crea el partido en DUPR con los `dupr_id` de los jugadores de ambos equipos. Devuelve el `dupr_partido_id`.
- `enviarResultado(string $duprPartidoId, array $scores): bool` — Envía los sets/scores al partido creado.
- `eliminarPartido(string $duprPartidoId): bool` — Borra el partido de DUPR (por si un resultado se anula).

**Autenticación de servidor:**
```
POST https://uat.mydupr.com/api/auth/v1.0/token
Header: x-authorization: base64("test-ck-ab5bfcaa-8a25-4bff-ff71-63604d7dd806:test-cs-446890c8cf3f41e2f9031a44f383348a")
```
Formato: `base64(ClientKey:ClientSecret)` — el Client ID (7725216610) se usa como identificador de app, no en el header.  
El token obtenido se cachea en Laravel Cache (clave `dupr_server_token`) con TTL de 55 minutos.

### `app/Http/Controllers/DuprController.php`
Maneja el flujo OAuth2 de vinculación y desvinculación de cuenta DUPR.

**Nota:** Los endpoints exactos del OAuth2 usuario (authorize URL y token URL) deben confirmarse en `https://uat.mydupr.com/api/v3/api-docs` antes de implementar. Se asume flujo OAuth2 Authorization Code estándar.

**Métodos:**
- `redirect()` — Genera la URL de autorización DUPR y redirige al jugador.
- `callback(Request $request)` — Recibe el código de autorización, lo intercambia por token, obtiene el `dupr_id` del jugador, actualiza `jugadores.dupr_id` + `users.dupr_access_token`, luego llama a `DuprService::obtenerRatingJugador()` para actualizar los ratings. Redirige al perfil con mensaje de éxito.
- `desconectar()` — Pone a null `dupr_id`, `rating_singles`, `rating_doubles`, `dupr_access_token`, `dupr_token_expires_at` en el jugador/usuario.

**Rutas:**
```php
GET  /dupr/conectar          → DuprController@redirect      (auth, rol Jugador)
GET  /dupr/callback          → DuprController@callback      (auth, rol Jugador)
POST /dupr/desconectar       → DuprController@desconectar   (auth, rol Jugador)
```

### `app/Jobs/SincronizarResultadoDuprJob.php`
Job en cola que envía el resultado de un partido a DUPR después de que el organizador lo carga.

**Comportamiento:**
1. Recibe el `Partido` por ID.
2. Verifica que `torneo->dupr_requerido` siga siendo true y que el partido aún no esté sincronizado.
3. Reúne los `dupr_id` de todos los jugadores de `equipo1` y `equipo2`.
4. Si algún jugador no tiene `dupr_id` → marca `dupr_error` y **no falla el job** (no tiene sentido reintentar).
5. Llama a `DuprService::crearPartido()` para registrar el partido en DUPR.
6. Llama a `DuprService::enviarResultado()` con los scores.
7. Actualiza `partidos.dupr_partido_id`, `dupr_sincronizado = true`, `dupr_sincronizado_at`.
8. Si la API falla → el job lanza excepción, Laravel reintenta (3 intentos con backoff).
9. Al agotar reintentos → guarda el error en `partidos.dupr_error`.

**Configuración:** `$tries = 3`, `$backoff = [60, 300, 900]` (1min, 5min, 15min).

---

## 3. Componentes modificados

### `InscripcionService::validarCondicionesJugador()`
Se agregan dos validaciones al final del método existente:

```
Si torneo->dupr_requerido Y jugador->dupr_id es null:
    throw RuntimeException("Este torneo requiere vincular tu cuenta DUPR. Podés hacerlo desde tu perfil.")

Si categoría->pivot->dupr_rating_min o dupr_rating_max están configurados:
    rating = jugador->rating_doubles (por defecto; pickleball es formato dobles)
    Si rating es null:
        throw RuntimeException("Tu cuenta DUPR no tiene rating registrado aún.")
    Si rating < dupr_rating_min:
        throw RuntimeException("Tu rating DUPR ({rating}) es menor al mínimo requerido ({min}).")
    Si rating > dupr_rating_max:
        throw RuntimeException("Tu rating DUPR ({rating}) supera el máximo permitido ({max}).")
```

### `InscripcionService::jugadorCumpleCondiciones()`
Misma lógica pero retorna `false` en lugar de lanzar excepciones — para que el buscador de jugadores elegibles filtre correctamente antes de mostrarlos.

### `InscripcionService::buscarJugadoresElegibles()`
Sin cambio estructural — la lógica de filtro en `jugadorCumpleCondiciones()` ya cubrirá DUPR automáticamente.

### `TorneoFixtureController::cargarResultado()` y `TorneoLlaveController::cargarResultado()`
Después de guardar el resultado y el ganador:
```php
if ($torneo->dupr_requerido) {
    SincronizarResultadoDuprJob::dispatch($partido);
}
```

### Wizard de creación de torneo — Paso 2
Se agrega un toggle `dupr_requerido` en la sección de configuración del torneo. No requiere cambios estructurales al wizard, solo un campo nuevo en el formulario y el request de validación.

### Formulario de categorías en torneo
Se agregan dos campos numéricos opcionales `dupr_rating_min` y `dupr_rating_max`, visibles solo cuando `torneo->dupr_requerido = true` (controlado con JS/Alpine). Se guardan en la pivot `categoria_torneo`.

### Perfil del jugador (vista)
- Si `jugador->dupr_id` es null: botón "Conectar con DUPR".
- Si `jugador->dupr_id` existe: badge "DUPR conectado" con rating singles y dobles + botón "Desconectar".

---

## 4. Flujos completos

### Flujo A — Conectar DUPR desde perfil

```
1. Jugador pulsa "Conectar con DUPR" en perfil
2. GET /dupr/conectar → DuprController@redirect
3. Redirect a uat.mydupr.com (OAuth2 authorize endpoint)
4. Jugador se autentica en DUPR
5. DUPR redirige a GET /dupr/callback?code=...
6. DuprController@callback:
   a. Intercambia code por access_token (POST a DUPR token endpoint)
   b. GET a DUPR /me o equivalente → obtiene dupr_id
   c. Actualiza jugador->dupr_id, users->dupr_access_token/expires_at
   d. Llama DuprService::obtenerRatingJugador() → actualiza ratings
7. Redirect a perfil con mensaje "Cuenta DUPR vinculada correctamente"
```

### Flujo B — Inscripción en torneo con DUPR requerido

```
1. Jugador inicia inscripción en torneo con dupr_requerido = true
2. InscripcionService::iniciarInscripcion() → validarCondicionesJugador()
   → Si no tiene dupr_id: error "Necesitás vincular tu cuenta DUPR"
   → Si categoría tiene rating min/max y no cumple: error con detalle (se valida `rating_doubles` por defecto, ya que pickleball es formato dobles)
3. Si pasa validaciones → flujo de inscripción normal (sin cambios)
4. Al buscar compañeros de equipo → buscarJugadoresElegibles() también filtra
   jugadores sin dupr_id o fuera del rango de rating
```

### Flujo C — Sincronización de resultado con DUPR

```
1. Organizador carga resultado del partido (sets_equipo1, sets_equipo2, ganador)
2. Se guarda en DB normalmente
3. Si torneo->dupr_requerido:
   → dispatch(SincronizarResultadoDuprJob::class, $partido)
4. El organizador recibe respuesta inmediata (sin esperar a DUPR)
5. Job en background:
   a. DuprService::obtenerToken() (del cache o nuevo)
   b. Reúne dupr_id de todos los jugadores del partido
   c. DuprService::crearPartido() → obtiene dupr_partido_id
   d. DuprService::enviarResultado() con scores
   e. partido->dupr_sincronizado = true, dupr_partido_id = X
6. Si DUPR falla: job reintenta a 1min, 5min, 15min
7. Si agota reintentos: partido->dupr_error = mensaje del error
```

---

## 5. Consideraciones de error y edge cases

- **Jugador sin dupr_id en partido DUPR**: El job registra el error pero no falla — evita bloquear reintentos indefinidos por un dato que no va a cambiar.
- **Token DUPR expirado**: `DuprService` verifica `dupr_token_expires_at` antes de usar el token cacheado; si está por vencer lo renueva.
- **Partido de torneo que cambia de dupr_requerido**: El toggle solo afecta torneos en estado `borrador`. Una vez activo, `dupr_requerido` no se puede modificar para mantener consistencia.
- **Desconectar DUPR**: Solo permitido si el jugador no está inscripto en ningún torneo activo con `dupr_requerido = true`.

---

## 6. Resumen de archivos a crear/modificar

### Crear
- `app/Services/DuprService.php`
- `app/Http/Controllers/DuprController.php`
- `app/Jobs/SincronizarResultadoDuprJob.php`
- `database/migrations/XXXX_add_dupr_fields_to_jugadores_table.php`
- `database/migrations/XXXX_add_dupr_fields_to_users_table.php`
- `database/migrations/XXXX_add_dupr_requerido_to_torneos_table.php`
- `database/migrations/XXXX_add_dupr_rating_to_categoria_torneo_table.php`
- `database/migrations/XXXX_add_dupr_fields_to_partidos_table.php`

### Modificar
- `app/Services/InscripcionService.php` — validaciones DUPR
- `app/Http/Controllers/TorneoFixtureController.php` — dispatch job
- `app/Http/Controllers/TorneoLlaveController.php` — dispatch job
- `routes/web.php` — rutas DUPR
- Vista perfil jugador — botón conectar/desconectar + badge rating
- Vista paso 2 wizard torneo — toggle dupr_requerido
- Vista formulario categorías — campos rating min/max
