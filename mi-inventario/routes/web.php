<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

// Main Routes
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/inventario', function () {
    return view('inventario.index');
})->name('inventario');

Route::get('/picking', function () {
    return view('picking.index');
})->name('picking');

Route::get('/reportes', function () {
    return view('reportes.index');
})->name('reportes');

Route::get('/ubicaciones', function () {
    return view('ubicaciones.index');
})->name('ubicaciones');

Route::get('/usuarios', function () {
    return view('usuarios.index');
})->name('usuarios');

Route::get('/configuracion', function () {
    return view('configuracion.index');
})->name('configuracion');

Route::get('/perfil', function () {
    return view('perfil.index');
})->name('perfil');