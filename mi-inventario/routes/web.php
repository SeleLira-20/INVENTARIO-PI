<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;

// ── Raíz ──────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Autenticación (solo para invitados) ───────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',           [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
});

// ── Login POST diagnóstico ─────────────────────────────────────────────────
Route::post('/login', function(Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return response()->json([
            'auth'       => Auth::check(),
            'session_id' => session()->getId(),
            'user'       => Auth::user(),
        ]);
    }
    return response()->json(['error' => 'Credenciales incorrectas']);
})->name('login.post');

// ── Logout ────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Rutas sin middleware temporalmente para diagnóstico ───────────────────
Route::get('/dashboard',     fn() => view('dashboard.index'))->name('dashboard');
Route::get('/picking',       fn() => view('picking.index'))->name('picking');
Route::get('/reportes',      fn() => view('reportes.index'))->name('reportes');
Route::get('/ubicaciones',   fn() => view('ubicaciones.index'))->name('ubicaciones');
Route::get('/usuarios',      fn() => view('usuarios.index'))->name('usuarios');
Route::get('/configuracion', fn() => view('configuracion.index'))->name('configuracion');
Route::get('/perfil',        fn() => view('perfil.index'))->name('perfil');
Route::get('/register',      [AuthController::class, 'showRegister'])->name('register');

// ── Inventario ────────────────────────────────────────────────────────────
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');

// ── Proxy hacia la API FastAPI ────────────────────────────────────────────
Route::prefix('inventario/api')->group(function () {
    Route::get('/productos',         [InventarioController::class, 'listar']);
    Route::post('/productos',        [InventarioController::class, 'crear']);
    Route::put('/productos/{id}',    [InventarioController::class, 'actualizar']);
    Route::delete('/productos/{id}', [InventarioController::class, 'eliminar']);
    Route::get('/alertas',           [InventarioController::class, 'alertasStockBajo']);
    Route::get('/movimientos',       [InventarioController::class, 'movimientos']);
    Route::post('/movimientos',      [InventarioController::class, 'registrarMovimiento']);
});