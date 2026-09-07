<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImagenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí definimos las rutas principales del sistema.
|
*/

// Index público (clientes y visitantes)
Route::get('/', [ServicioController::class, 'indexPublic'])->name('index');

// Páginas públicas de servicios
Route::get('/servicios/cortes', [ServicioController::class, 'cortes'])->name('servicios.cortes');
Route::get('/servicios/barba', [ServicioController::class, 'barba'])->name('servicios.barba');
Route::get('/servicios/coloracion', [ServicioController::class, 'coloracion'])->name('servicios.coloracion');

// Dashboards y panel admin (solo autenticados)
Route::middleware('auth')->group(function () {
    // Turnos genéricos (para servicios)
    Route::get('/turnos/create', [TurnoController::class, 'create'])->name('turnos.create');
    Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');

    // Panel principal del administrador
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard_admin');

    // Panel del barbero y sus turnos
    Route::get('/dashboard/barbero', [TurnoController::class, 'misTurnosBarbero'])->name('barbero.dashboard_barbero');
    Route::get('/barbero/turnos', [TurnoController::class, 'misTurnosBarbero'])->name('barbero.turnos');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Secciones del panel admin
    |--------------------------------------------------------------------------
    */

    // Gestión de usuarios
    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/create', [UsuarioController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [UsuarioController::class, 'store'])->name('admin.usuarios.store');
    Route::get('/admin/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
    Route::post('/admin/usuarios/{id}/activate', [UsuarioController::class, 'activate'])->name('admin.usuarios.activate');

    // Gestión de sucursales
    Route::get('/admin/sucursales', [SucursalController::class, 'index'])->name('admin.sucursales.index');
    Route::get('/admin/sucursales/create', [SucursalController::class, 'create'])->name('admin.sucursales.create');
    Route::post('/admin/sucursales', [SucursalController::class, 'store'])->name('admin.sucursales.store');
    Route::get('/admin/sucursales/{id}/edit', [SucursalController::class, 'edit'])->name('admin.sucursales.edit');
    Route::put('/admin/sucursales/{id}', [SucursalController::class, 'update'])->name('admin.sucursales.update');
    Route::delete('/admin/sucursales/{id}', [SucursalController::class, 'destroy'])->name('admin.sucursales.destroy');
    Route::post('/admin/sucursales/{id}/activate', [SucursalController::class, 'activate'])->name('admin.sucursales.activate');

    // Visualización de turnos (admin) → SOLO ver
    Route::get('/admin/turnos', [TurnoController::class, 'index'])->name('admin.turnos.index');

   // Turnos del usuario (cliente) → CRUD completo
Route::get('/usuario/turnos', [TurnoController::class, 'misTurnosUsuario'])->name('usuario.turnos');
Route::get('/usuario/turnos/create', [TurnoController::class, 'create'])->name('usuario.turnos.create');
Route::post('/usuario/turnos', [TurnoController::class, 'store'])->name('usuario.turnos.store');
Route::put('/usuario/turnos/{id}', [TurnoController::class, 'update'])->name('usuario.turnos.update');
Route::delete('/usuario/turnos/{id}', [TurnoController::class, 'destroy'])->name('usuario.turnos.destroy');


    // Gestión de servicios
    Route::get('/admin/servicios', [ServicioController::class, 'index'])->name('admin.servicios.index');
    Route::get('/admin/servicios/create', [ServicioController::class, 'create'])->name('admin.servicios.create');
    Route::post('/admin/servicios', [ServicioController::class, 'store'])->name('admin.servicios.store');
    Route::get('/admin/servicios/{id}/edit', [ServicioController::class, 'edit'])->name('admin.servicios.edit');
    Route::put('/admin/servicios/{id}', [ServicioController::class, 'update'])->name('admin.servicios.update');
    Route::delete('/admin/servicios/{id}', [ServicioController::class, 'destroy'])->name('admin.servicios.destroy');
    Route::post('/admin/servicios/{id}/activate', [ServicioController::class, 'activate'])->name('admin.servicios.activate');

    // Estadísticas
    Route::get('/admin/estadisticas', [AdminController::class, 'estadisticas'])->name('admin.estadisticas');
    Route::get('/admin/estadisticas/dashboard', [AdminController::class, 'estadisticas'])->name('admin.estadisticas.dashboard');

    // Gestión de imágenes
    Route::get('/admin/imagenes', [ImagenController::class, 'index'])->name('admin.imagenes.index');
    Route::post('/admin/imagenes', [ImagenController::class, 'store'])->name('admin.imagenes.store');
    Route::put('/admin/imagenes/{imagen}', [ImagenController::class, 'update'])->name('admin.imagenes.update');
    Route::delete('/admin/imagenes/{imagen}', [ImagenController::class, 'destroy'])->name('admin.imagenes.destroy');

    // AJAX: obtener barberos por sucursal
    Route::get('/barberos/{id}', [TurnoController::class, 'getBarberosPorSucursal'])->name('barberos.porSucursal');
});

// Rutas de autenticación generadas por Breeze
require __DIR__.'/auth.php';
