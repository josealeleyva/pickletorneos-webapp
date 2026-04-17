<?php

use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizadorController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\SugerenciaController;
use App\Http\Controllers\Admin\TorneoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rutas del panel de administración para superadministradores
|
*/

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Organizadores
    Route::prefix('organizadores')->name('organizadores.')->group(function () {
        Route::get('/', [OrganizadorController::class, 'index'])->name('index');
        Route::get('/{user}', [OrganizadorController::class, 'show'])->name('show');
        Route::post('/{user}/toggle-estado', [OrganizadorController::class, 'toggleEstado'])->name('toggle-estado');
        Route::post('/{user}/otorgar-credito', [OrganizadorController::class, 'otorgarCredito'])->name('otorgar-credito');
    });

    // Torneos
    Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos.index');
    Route::get('/torneos/{torneo}', [TorneoController::class, 'show'])->name('torneos.show');

    // Pagos
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');

    // Sugerencias
    Route::prefix('sugerencias')->name('sugerencias.')->group(function () {
        Route::get('/', [SugerenciaController::class, 'index'])->name('index');
        Route::get('/{sugerencia}', [SugerenciaController::class, 'show'])->name('show');
        Route::post('/{sugerencia}/responder', [SugerenciaController::class, 'responder'])->name('responder');
        Route::post('/{sugerencia}/cambiar-estado', [SugerenciaController::class, 'cambiarEstado'])->name('cambiar-estado');
    });

    // Configuración del Sistema
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
        Route::put('/{configuracion}', [ConfiguracionController::class, 'update'])->name('update');
        Route::post('/clear-cache', [ConfiguracionController::class, 'clearCache'])->name('clear-cache');
    });
});
