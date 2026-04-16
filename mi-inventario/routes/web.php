<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;

// ── Raíz ──────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Autenticación ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',           [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');
    Route::get('/forgot-password',  [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/register',          [AuthController::class, 'register'])->name('register.post');
});

Route::post('/login', function(Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }
    return back()->withInput($request->only('email'))
        ->withErrors(['email' => 'El correo o la contraseña son incorrectos.']);
})->name('login.post');

Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// ── Vistas principales ─────────────────────────────────────────────────────
Route::get('/dashboard',     fn() => view('dashboard.index'))->name('dashboard');
Route::get('/inventario',    [InventarioController::class, 'index'])->name('inventario');
Route::get('/picking',       fn() => view('picking.index'))->name('picking');
Route::get('/reportes',      fn() => view('reportes.index'))->name('reportes');
Route::get('/ubicaciones',   fn() => view('ubicaciones.index'))->name('ubicaciones');
Route::get('/usuarios',      fn() => view('usuarios.index'))->name('usuarios');
Route::get('/configuracion', fn() => view('configuracion.index'))->name('configuracion');
Route::get('/perfil',         fn() => view('perfil.index'))->name('perfil');
Route::put('/perfil/actualizar', [AuthController::class, 'actualizarPerfil'])->name('perfil.actualizar');
Route::put('/perfil/password',   [AuthController::class, 'cambiarPassword'])->name('perfil.password');

// ── Proxy API FastAPI ──────────────────────────────────────────────────────
Route::prefix('inventario/api')->group(function () {

    // Productos
    Route::get('/productos',         [InventarioController::class, 'listar']);
    Route::post('/productos',        [InventarioController::class, 'crear']);
    Route::put('/productos/{id}',    [InventarioController::class, 'actualizar']);
    Route::delete('/productos/{id}', [InventarioController::class, 'eliminar']);

    // Alertas y movimientos
    Route::get('/alertas',           [InventarioController::class, 'alertasStockBajo']);
    Route::get('/movimientos',       [InventarioController::class, 'movimientos']);
    Route::post('/movimientos',      [InventarioController::class, 'registrarMovimiento']);

    // Incidentes
    Route::get('/incidentes',              [InventarioController::class, 'incidentes']);
    Route::put('/incidentes/{id}/resolver',[InventarioController::class, 'resolverIncidente']);

    // Picking
    Route::get('/picking',             [InventarioController::class, 'listarPicking']);
    Route::post('/picking',            [InventarioController::class, 'crearOrden']);
    Route::get('/picking/{id}',        [InventarioController::class, 'detalleOrden']);
    Route::put('/picking/{id}/estado', [InventarioController::class, 'actualizarEstadoOrden']);
    Route::delete('/picking/{id}',     [InventarioController::class, 'eliminarOrden']);

    // Ubicaciones
    Route::get('/ubicaciones',         [InventarioController::class, 'listarUbicaciones']);
    Route::post('/ubicaciones',        [InventarioController::class, 'crearUbicacion']);
    Route::put('/ubicaciones/{id}',    [InventarioController::class, 'actualizarUbicacion']);
    Route::delete('/ubicaciones/{id}', [InventarioController::class, 'eliminarUbicacion']);

    // Usuarios
    Route::get('/usuarios',            [InventarioController::class, 'listarUsuarios']);
    Route::post('/usuarios',           [InventarioController::class, 'crearUsuario']);
    Route::put('/usuarios/{id}',       [InventarioController::class, 'actualizarUsuario']);
    Route::delete('/usuarios/{id}',    [InventarioController::class, 'eliminarUsuario']);
});