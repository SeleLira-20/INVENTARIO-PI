<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;

Route::get('/', function () {
    return redirect('/login');
});

// ── Autenticación ──────────────────────────────────────────────────────────
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('forgot-password');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// ── Rutas principales ──────────────────────────────────────────────────────
Route::get('/dashboard',     fn() => view('dashboard.index'))->name('dashboard');
Route::get('/picking',       fn() => view('picking.index'))->name('picking');
Route::get('/reportes',      fn() => view('reportes.index'))->name('reportes');
Route::get('/ubicaciones',   fn() => view('ubicaciones.index'))->name('ubicaciones');
Route::get('/usuarios',      fn() => view('usuarios.index'))->name('usuarios');
Route::get('/configuracion', fn() => view('configuracion.index'))->name('configuracion');
Route::get('/perfil',        fn() => view('perfil.index'))->name('perfil');

// ── Inventario — vista principal ────────────────────────────────────────────
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');

// ── Inventario — endpoints proxy hacia la API FastAPI (opcionales) ──────────
// Úsalos si en el futuro quieres que las llamadas pasen por Laravel
// en lugar de ir directo al API desde el Blade.
Route::prefix('inventario/api')->group(function () {
    Route::get('/productos',        [InventarioController::class, 'listar']);
    Route::post('/productos',       [InventarioController::class, 'crear']);
    Route::put('/productos/{id}',   [InventarioController::class, 'actualizar']);
    Route::delete('/productos/{id}',[InventarioController::class, 'eliminar']);
    Route::get('/alertas',          [InventarioController::class, 'alertasStockBajo']);
    Route::get('/movimientos',       [InventarioController::class, 'movimientos']);
});