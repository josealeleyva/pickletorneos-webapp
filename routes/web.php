<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ComplejoDeportivoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DuprController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\InvitacionController;
use App\Http\Controllers\Jugador\DashboardController as JugadorDashboardController;
use App\Http\Controllers\Jugador\PerfilController as JugadorPerfilController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferidoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SugerenciaController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\TorneoEquipoController;
use App\Http\Controllers\TorneoFixtureController;
use App\Http\Controllers\TorneoGrupoController;
use App\Http\Controllers\TorneoLlaveController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Términos y Condiciones
Route::get('/terminos-y-condiciones', function () {
    return view('tyc');
})->name('tyc');

// Tutoriales
Route::get('/tutoriales', function () {
    return view('tutoriales');
})->name('tutoriales');

// Rutas públicas (sin autenticación)
Route::get('/torneos/{id}/publico', [TorneoController::class, 'showPublic'])->name('torneos.public');
Route::get('/torneos/{id}/tv', [TorneoController::class, 'showTv'])->name('torneos.tv');

// Página de invitación de referidos (pública)
Route::get('/invitacion/{codigo}', [ReferidoController::class, 'invitacion'])->name('referidos.invitacion');

// Invitación a inscripción (pública - redirige a login si no autenticado)
Route::get('/inscripciones/invitacion/{token}', [InvitacionController::class, 'mostrar'])->name('inscripciones.invitacion.mostrar');

// Webhook de MercadoPago (pública, sin autenticación ni CSRF)
Route::post('/webhooks/mercadopago', [PagoController::class, 'webhook'])->name('pagos.webhook');

// Google OAuth
Route::get('/auth/google/{rol}', [SocialAuthController::class, 'redirectToGoogle'])
    ->name('auth.google.redirect')
    ->where('rol', 'jugador|organizador|desconocido');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');
Route::get('/auth/completar-perfil', [SocialAuthController::class, 'showCompletarPerfil'])
    ->name('auth.completar-perfil');
Route::post('/auth/completar-perfil', [SocialAuthController::class, 'completarPerfil'])
    ->name('auth.completar-perfil.store');

// Rutas de autenticación (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Validar código de referido (AJAX)
    Route::post('/validar-codigo-referido', [RegisterController::class, 'validarCodigo'])->name('validar-codigo-referido');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// RUTAS DEL PANEL DE JUGADOR
Route::middleware('auth')->prefix('jugador')->name('jugador.')->group(function () {
    Route::get('/dashboard', [JugadorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/torneos', [JugadorDashboardController::class, 'torneos'])->name('torneos');
    Route::get('/inscripciones', [JugadorDashboardController::class, 'inscripciones'])->name('inscripciones');
    Route::get('/partidos', [JugadorDashboardController::class, 'partidos'])->name('partidos');
    Route::post('/partidos/{partido}/resultado', [\App\Http\Controllers\Jugador\ResultadoTentativoController::class, 'store'])->name('partidos.resultado.store');
    Route::post('/resultados/{resultado}/confirmar', [\App\Http\Controllers\Jugador\ResultadoTentativoController::class, 'confirmar'])->name('resultados.confirmar');
    Route::post('/resultados/{resultado}/modificar', [\App\Http\Controllers\Jugador\ResultadoTentativoController::class, 'modificar'])->name('resultados.modificar');
    Route::get('/perfil', [JugadorPerfilController::class, 'show'])->name('perfil');
    Route::put('/perfil', [JugadorPerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [JugadorPerfilController::class, 'updatePassword'])->name('perfil.password');
    Route::post('/perfil/foto', [JugadorPerfilController::class, 'updateFoto'])->name('perfil.foto');
});

// RUTAS DUPR
Route::middleware('auth')->prefix('dupr')->name('dupr.')->group(function () {
    Route::get('/buscar', [DuprController::class, 'buscar'])->name('buscar');
    Route::get('/autoconectar', [DuprController::class, 'autoconectar'])->name('autoconectar');
    Route::get('/verificar-id', [DuprController::class, 'verificarId'])->name('verificar-id');
    Route::post('/vincular', [DuprController::class, 'vincular'])->name('vincular');
    Route::post('/crear', [DuprController::class, 'crear'])->name('crear');
    Route::post('/desconectar', [DuprController::class, 'desconectar'])->name('desconectar');
});

// RUTAS AUTENTICADAS (Dashboard)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard de Referidos
    Route::get('/referidos/dashboard', [ReferidoController::class, 'dashboard'])->name('referidos.dashboard');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Complejos Deportivos
    Route::resource('complejos', ComplejoDeportivoController::class);

    // Canchas (anidadas dentro de complejos)
    Route::resource('complejos.canchas', CanchaController::class)->except(['show'])
        ->parameters(['complejos' => 'complejo', 'canchas' => 'cancha']);

    // Categorías
    Route::resource('categorias', CategoriaController::class);

    // Jugadores - Import/Export (deben ir ANTES del resource para evitar conflictos de rutas)
    Route::get('/jugadores/exportar', [JugadorController::class, 'exportar'])->name('jugadores.exportar');
    Route::get('/jugadores/plantilla', [JugadorController::class, 'descargarPlantilla'])->name('jugadores.plantilla');
    Route::post('/jugadores/importar', [JugadorController::class, 'procesarImportacion'])->name('jugadores.importar');

    // Jugadores
    Route::resource('jugadores', JugadorController::class)->parameter('jugadores', 'jugador');

    // API endpoints para autocomplete de equipos (dentro de rutas web autenticadas)
    Route::get('/api/equipos/autocomplete', [\App\Http\Controllers\Api\EquipoPlantillaController::class, 'autocomplete'])->name('api.equipos.autocomplete');
    Route::get('/api/equipos/plantilla/jugadores', [\App\Http\Controllers\Api\EquipoPlantillaController::class, 'jugadores'])->name('api.equipos.plantilla.jugadores');

    // Sugerencias y Soporte
    Route::resource('sugerencias', SugerenciaController::class)->only(['index', 'create', 'store', 'show']);

    // Torneos - Wizard de creación
    Route::get('/torneos/crear/paso-1', [TorneoController::class, 'create'])->name('torneos.create');
    Route::post('/torneos/paso-1', [TorneoController::class, 'storeStep1'])->name('torneos.store-step1');
    Route::get('/torneos/{torneo}/paso-2', [TorneoController::class, 'createStep2'])->name('torneos.create-step2');
    Route::post('/torneos/{torneo}/paso-2', [TorneoController::class, 'storeStep2'])->name('torneos.store-step2');

    // Pagos de Torneos
    Route::get('/torneos/{torneo}/pago', [PagoController::class, 'checkout'])->name('pagos.checkout');
    Route::post('/torneos/{torneo}/pago/usar-credito', [PagoController::class, 'usarCredito'])->name('pagos.usar-credito');
    Route::get('/torneos/{torneo}/pago/exito', [PagoController::class, 'success'])->name('pagos.success');
    Route::get('/torneos/{torneo}/pago/pendiente', [PagoController::class, 'pending'])->name('pagos.pending');
    Route::get('/torneos/{torneo}/pago/error', [PagoController::class, 'failure'])->name('pagos.failure');

    // Torneos - CRUD básico
    Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos.index');
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy'])->name('torneos.destroy');

    // Torneos con verificación de pago
    Route::middleware('pago.verificar')->group(function () {
        Route::get('/torneos/{torneo}', [TorneoController::class, 'show'])->name('torneos.show');
        Route::get('/torneos/{torneo}/editar', [TorneoController::class, 'edit'])->name('torneos.edit');
        Route::put('/torneos/{torneo}', [TorneoController::class, 'update'])->name('torneos.update');
        Route::post('/torneos/{torneo}/comenzar', [TorneoController::class, 'comenzar'])->name('torneos.comenzar');
        Route::post('/torneos/{torneo}/cancelar', [TorneoController::class, 'cancelar'])->name('torneos.cancelar');
        Route::post('/torneos/finalizar', [TorneoController::class, 'finalizar'])->name('torneos.finalizar');
        Route::get('/torneos/{torneo}/exportar-resultados', [TorneoController::class, 'exportarResultados'])->name('torneos.exportar-resultados');

        // Torneos - Gestión de Equipos
        Route::get('/torneos/{torneo}/equipos', [TorneoEquipoController::class, 'index'])->name('torneos.equipos.index');
        Route::get('/torneos/{torneo}/equipos/crear', [TorneoEquipoController::class, 'create'])->name('torneos.equipos.create');
        Route::post('/torneos/{torneo}/equipos', [TorneoEquipoController::class, 'store'])->name('torneos.equipos.store');
        Route::delete('/torneos/{torneo}/equipos/{equipo}', [TorneoEquipoController::class, 'destroy'])->name('torneos.equipos.destroy');
        Route::get('/torneos/{torneo}/equipos/{equipo}/planilla', [TorneoEquipoController::class, 'descargarPlanilla'])->name('torneos.equipos.planilla');

        // Torneos - Gestión de Grupos
        Route::get('/torneos/{torneo}/grupos', [TorneoGrupoController::class, 'index'])->name('torneos.grupos.index');
        Route::post('/torneos/{torneo}/grupos/sortear', [TorneoGrupoController::class, 'sortear'])->name('torneos.grupos.sortear');
        Route::post('/torneos/{torneo}/grupos/resetear', [TorneoGrupoController::class, 'resetear'])->name('torneos.grupos.resetear');
        Route::post('/torneos/{torneo}/grupos/intercambiar', [TorneoGrupoController::class, 'intercambiar'])->name('torneos.grupos.intercambiar');
        Route::post('/torneos/{torneo}/grupos/asignar', [TorneoGrupoController::class, 'asignar'])->name('torneos.grupos.asignar');
        Route::delete('/torneos/{torneo}/grupos/equipos/{equipo}', [TorneoGrupoController::class, 'quitar'])->name('torneos.grupos.quitar');

        // Torneos - Fixture
        Route::get('/torneos/{torneo}/fixture', [TorneoFixtureController::class, 'index'])->name('torneos.fixture.index');
        Route::post('/torneos/{torneo}/fixture/generar', [TorneoFixtureController::class, 'generar'])->name('torneos.fixture.generar');
        Route::post('/torneos/{torneo}/fixture/programar', [TorneoFixtureController::class, 'programar'])->name('torneos.fixture.programar');
        Route::post('/torneos/{torneo}/fixture/resetear', [TorneoFixtureController::class, 'resetear'])->name('torneos.fixture.resetear');
        Route::post('/torneos/{torneo}/fixture/notificar', [TorneoFixtureController::class, 'enviarNotificaciones'])->name('torneos.fixture.notificar');
        Route::post('/torneos/{torneo}/fixture/notificar-todos', [TorneoFixtureController::class, 'enviarNotificacionesTodos'])->name('torneos.fixture.notificar-todos');
        Route::post('/torneos/{torneo}/fixture/marcar-campeon', [TorneoFixtureController::class, 'marcarCampeon'])->name('torneos.fixture.marcar-campeon');

        // Torneos - Llaves/Bracket
        Route::get('/torneos/{torneo}/llaves', [TorneoLlaveController::class, 'index'])->name('torneos.llaves.index');
        Route::post('/torneos/{torneo}/llaves/generar', [TorneoLlaveController::class, 'generate'])->name('torneos.llaves.generate');
        Route::post('/torneos/{torneo}/llaves/resetear', [TorneoLlaveController::class, 'reset'])->name('torneos.llaves.reset');
        Route::post('/torneos/{torneo}/llaves/{llave}/programar', [TorneoLlaveController::class, 'programarPartido'])->name('torneos.llaves.programar');
        Route::post('/torneos/{torneo}/llaves/{llave}/resultado', [TorneoLlaveController::class, 'cargarResultado'])->name('torneos.llaves.resultado');
        Route::post('/torneos/{torneo}/llaves/notificar', [TorneoLlaveController::class, 'enviarNotificaciones'])->name('torneos.llaves.notificar');
        Route::post('/torneos/{torneo}/llaves/notificar-todos', [TorneoLlaveController::class, 'enviarNotificacionesTodos'])->name('torneos.llaves.notificar-todos');
        Route::put('/torneos/{torneo}/fixture/{partido}', [TorneoFixtureController::class, 'actualizarPartido'])->name('torneos.fixture.actualizar');
        Route::post('/torneos/{torneo}/fixture/{partido}/resultado', [TorneoFixtureController::class, 'cargarResultado'])->name('torneos.fixture.resultado');
    });

    // Jugadores - Creación rápida
    Route::post('/jugadores', [JugadorController::class, 'store'])->name('jugadores.store');

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
    Route::post('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leer-todas');

    // Inscripciones de jugadores a torneos
    Route::get('/torneos/{torneo}/inscribirse', [InscripcionController::class, 'crear'])->name('torneos.inscripciones.crear');
    Route::post('/torneos/{torneo}/inscribirse', [InscripcionController::class, 'store'])->name('torneos.inscripciones.store');
    Route::get('/torneos/{torneo}/inscribirse/buscar', [InscripcionController::class, 'buscarJugadores'])->name('torneos.inscripciones.buscar');
    Route::get('/inscripciones/{inscripcion}/invitar', [InscripcionController::class, 'mostrarInvitaciones'])->name('inscripciones.invitar');
    Route::post('/inscripciones/{inscripcion}/invitar', [InscripcionController::class, 'invitar'])->name('inscripciones.invitar.post');
    Route::delete('/inscripciones/{inscripcion}', [InscripcionController::class, 'cancelar'])->name('inscripciones.cancelar');

    // Respuesta a invitaciones
    Route::post('/inscripciones/invitacion/{token}/aceptar', [InvitacionController::class, 'aceptar'])->name('inscripciones.invitacion.aceptar');
    Route::post('/inscripciones/invitacion/{token}/rechazar', [InvitacionController::class, 'rechazar'])->name('inscripciones.invitacion.rechazar');
});

// RUTAS AUTENICADAS (API)
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'api'], function () {

    // Permisos
    Route::post('permisos/asignar/{permission}', [PermissionController::class, 'assign']);
    Route::post('permisos/quitar/{permission}', [PermissionController::class, 'deny']);
    Route::apiResource('permisos', PermissionController::class)->only(['index']);

    // Roles
    Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'destroy'])->parameter('roles', 'rol');

    // Usuarios
    Route::get('users/me', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class)->parameter('users', 'user');
});
