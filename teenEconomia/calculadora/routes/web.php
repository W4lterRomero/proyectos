<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnualidadDiferidaController;

Route::get('/', [AnualidadDiferidaController::class, 'inicio'])->name('inicio');
Route::get('/documentacion', [AnualidadDiferidaController::class, 'documentacion'])->name('documentacion');
Route::get('/calculadora', [AnualidadDiferidaController::class, 'mostrarCalculadora'])->name('calculadora.form');
Route::post('/calculadora', [AnualidadDiferidaController::class, 'calcular'])->name('calculadora.calcular');
