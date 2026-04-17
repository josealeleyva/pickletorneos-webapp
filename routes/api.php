<?php

use App\Http\Controllers\Auth\AccessTokenController;
use App\Http\Controllers\Api\EquipoPlantillaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('tokens', [AccessTokenController::class, 'index']);
Route::delete('tokens', [AccessTokenController::class, 'destroyAll']);
Route::post('login', [AccessTokenController::class, 'store']);
Route::post('logout', [AccessTokenController::class, 'destroy']);

// Rutas para equipos plantilla (autocomplete)
// Usar auth:sanctum,web para permitir tanto autenticación de API como web
Route::middleware('auth:sanctum,web')->group(function () {
    Route::get('equipos/autocomplete', [EquipoPlantillaController::class, 'autocomplete']);
    Route::get('equipos/plantilla/jugadores', [EquipoPlantillaController::class, 'jugadores']);
});
