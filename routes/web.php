<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecomendacionController;

Route::get('/', function () {
    return view('welcome');
});


// --- Rutas del Login ---
Route::get('/login', [LoginController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::get('/crear-nick', [LoginController::class, 'mostrarNick'])->name('crear.nick');
Route::post('/guardar-nick', [LoginController::class, 'guardarNick']);
// --- Rutas del Panel ---
Route::get('/u/{nick}', [HomeController::class, 'mostrar'])->name('user.dashboard');
Route::get('/u/{nick}/mi-pelo', [HomeController::class, 'showMiPelo'])->name('user.mipelo');
Route::get('/u/{nick}/peinados-cortes', [HomeController::class, 'showPeinadosCortes'])->name('user.peinadoscortes');

// --- Rutas del Cuestionario ---
Route::get('/cuestionarios', [CuestionarioController::class, 'listar'])->name('cuestionarios.listar');
Route::get('/u/{nick}/cuestionario/{id_cuestionario}', [CuestionarioController::class, 'mostrarParaNick'])->name('cuestionarios.mostrarParaNick');
Route::post('/u/{nick}/cuestionario/{id_cuestionario}/enviar', [CuestionarioController::class, 'procesarEnvioParaNick'])->name('cuestionarios.enviarParaNick');
// --- Rutas de las Recomendaciones ---
Route::get('/gracias/{nick}/{recomendacionId?}', [CuestionarioController::class, 'mostrarPaginaGracias'])->name('cuestionarios.gracias');
Route::get('/recomendacion/{id}', [RecomendacionController::class, 'verRecomendacion'])->name('recomendacion.ver_producto');
