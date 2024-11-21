<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VeterinarioController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\AmoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoriasController;
use App\Http\Controllers\ConsultasController;

Route::post('/veterinario/login', [VeterinarioController::class, 'login']);
Route::post('/veterinario/register', [VeterinarioController::class, 'register'])->name('veterinario.register');

Route::middleware('auth:sanctum')->group(function () {

    route::get('veterinario-auth', [VeterinarioController::class, 'me']);

    // Rutas para gestionar Amos
    Route::post('/amos-store', [AmoController::class, 'registro']);
    Route::get('/amos', [AmoController::class, 'index']);
    Route::get('/amos/{id}', [AmoController::class, 'show']);
    Route::put('/amos-update/{id}', [AmoController::class, 'update']);
    Route::delete('/amos-delete/{id}', [AmoController::class, 'destroy']);
    Route::get('/pdf/amos', [AmoController::class, 'generarPdfAmos']);
    Route::get('/excel/amos', [AmoController::class, 'generarExcelAmos'])->name('exportar.amos');
    Route::get('/countAmos', [AmoController::class, 'countAmos']);

    // Rutas para gestionar mascotas
    Route::post('/mascotas-store', [MascotaController::class, 'store']);
    Route::get('/mascotas', [MascotaController::class, 'index']);
    Route::get('/mascotas/{id}', [MascotaController::class, 'show']);
    Route::put('/mascotas-update/{id}', [MascotaController::class, 'update']);
    Route::delete('/mascotas-delete/{id}', [MascotaController::class, 'destroy']);
    Route::get('/pdf/mascotas', [MascotaController::class, 'generarPdfMascotas']);
    Route::get('/excel/mascotas', [MascotaController::class, 'generarExcelMascotas'])->name('exportar.mascotas');
    Route::get('/countMascotas', [MascotaController::class, 'countMascotas']);

    // Rutas para historias clinicas
    Route::get('/historias', [HistoriasController::class, 'index']);
    Route::post('/historias-store', [HistoriasController::class, 'store']);
    Route::get('/historias/{id}', [HistoriasController::class, 'show']);
    Route::put('/historias-update/{id}', [HistoriasController::class, 'update']);
    Route::delete('/historias-delete/{id}', [HistoriasController::class, 'destroy']);
    Route::get('/pdf/historias', [HistoriasController::class, 'generarPdfHistorias']);
    Route::get('/excel/historias', [HistoriasController::class, 'generarExcelHistorias']);
    Route::get('/countHistorias', [HistoriasController::class, 'countHistorias']);
});


// Ruta para obtener información del usuario autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
