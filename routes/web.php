<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TipoServicioController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PagoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberoController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\BackupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Catálogo visual previo y creación de turnos (Libre para visitantes).
Route::get('/usuario/catalogo-servicios', [TurnoController::class, 'catalogoServicios'])->name('usuario.catalogo');
Route::get('/usuario/turnos/crear', [TurnoController::class, 'create'])->name('usuario.turnos.create');

// Calificaciones (Requiere autenticación)
Route::middleware(['auth'])->group(function () {
    Route::post('/calificaciones', [CalificacionController::class, 'store'])->name('calificaciones.store');
});

// Vista pública unificada de Sucursales y Reseñas
Route::get('/sucursales', [SucursalController::class, 'publicIndex'])->name('sucursales.public');
Route::post('/sucursales/contacto', [SucursalController::class, 'enviarContacto'])->name('sucursales.contacto');

// Index público (clientes y visitantes)
Route::get('/', [PublicController::class, 'index'])->name('usuario.index');

// Mostrar detalle de un servicio puntual
Route::get('/servicios/{id}', [PublicController::class, 'showServicio'])->name('servicios.public.show');

// Mostrar todos los modelos de un tipo de servicio
Route::get('/tiposervicios/{id}', [PublicController::class, 'showTipo'])->name('tiposervicios.public.show');

// Dashboards y panel admin (solo autenticados)
Route::middleware('auth')->group(function () {

    // Perfil de usuario (Módulo FEDELOOK)
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.updatePassword');
    Route::delete('/perfil', [PerfilController::class, 'destroy'])->name('perfil.destroy');

    // Rutas AJAX para manejo instantáneo del Avatar (Opción B)
    Route::post('/perfil/avatar/actualizar', [PerfilController::class, 'actualizarAvatar'])->name('perfil.avatar.update');
    Route::delete('/perfil/avatar/eliminar', [PerfilController::class, 'eliminarAvatar'])->name('perfil.avatar.destroy');

    // Consulta AJAX de disponibilidad en vivo (Reprogramación / Creación)
    Route::get('/turnos/horarios-ocupados', [TurnoController::class, 'getHorariosOcupados'])->name('turnos.horariosOcupados');
    // Endpoint AJAX para consultar horarios disponibles dinámicamente
    Route::get('/turnos/horarios-disponibles', [TurnoController::class, 'getHorariosDisponibles'])->name('turnos.horariosDisponibles');

    // Turnos genéricos
    Route::get('/turnos/create', [TurnoController::class, 'create'])->name('turnos.create');
    Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');

    // Panel principal del administrador
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard_admin');

    // Panel del barbero y sus turnos (BarberoController)
    Route::get('/dashboard/barbero', [BarberoController::class, 'misTurnosBarbero'])->name('barbero.dashboard_barbero');
    Route::get('/barbero/turnos', [BarberoController::class, 'misTurnosBarbero'])->name('barbero.turnos');
    Route::get('/barbero/progreso', [BarberoController::class, 'progresoBarbero'])->name('barbero.progreso');

    // La actualización del estado del turno se mantiene en TurnoController
    Route::put('/barbero/turnos/{id}', [TurnoController::class, 'update'])->name('barbero.turnos.update');

    // Perfil de usuario nativo (Laravel Breeze / Jetstream si aplica)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    Route::match(['post', 'patch'], '/admin/sucursales/{id}/activate', [SucursalController::class, 'activate'])->name('admin.sucursales.activate');

    // --- MÓDULO EXCLUSIVO DE PERSONAL DE BARBERÍA ---
    Route::get('/admin/personal', [PersonalController::class, 'index'])->name('admin.personal.index');
    Route::get('/admin/personal/nuevo', [PersonalController::class, 'create'])->name('admin.personal.create');
    Route::post('/admin/personal/guardar', [PersonalController::class, 'store'])->name('admin.personal.store');
    Route::put('/admin/personal/{id}/actualizar-turno', [PersonalController::class, 'updateTurno'])->name('admin.personal.updateTurno');
    Route::get('/admin/personal/{id}/editar', [PersonalController::class, 'edit'])->name('admin.personal.edit');
    Route::put('/admin/personal/{id}', [PersonalController::class, 'update'])->name('admin.personal.update');
    Route::delete('/admin/personal/{id}', [PersonalController::class, 'destroy'])->name('admin.personal.destroy');
    Route::patch('/admin/personal/{id}/activar', [PersonalController::class, 'activate'])->name('admin.personal.activate');
    Route::put('personal/turnos/{id}/horario', [PersonalController::class, 'updateTurnoHorario'])->name('admin.personal.updateTurnoHorario');

    // Visualización de turnos (admin)
    Route::get('/admin/turnos', [TurnoController::class, 'index'])->name('admin.turnos.index');
    // Creación manual de turnos (Desktop / Mostrador para Admin y Barberos)
    Route::post('/admin/turnos/manual', [TurnoController::class, 'storeAdmin'])->name('admin.turnos.storeAdmin');
    Route::put('/admin/turnos/{id}/admin', [TurnoController::class, 'updateAdmin'])->name('admin.turnos.updateAdmin');
    Route::put('/admin/turnos/{id}/cancelar', [TurnoController::class, 'cancelarAdmin'])->name('admin.turnos.cancelarAdmin');
    Route::post('/admin/turnos/procesar-inasistencia', [TurnoController::class, 'procesarInasistenciaAdmin'])->name('admin.turnos.procesar_inasistencia');
    // Ruta para la Bomba Atómica (Cancelación de emergencia del Admin)
    Route::put('/admin/turnos/{id}/emergencia', [TurnoController::class, 'cancelarEmergencia'])->name('admin.turnos.emergencia');
    
    // Rutas operativas del Admin / Barbero (Completar y Ausente)
    Route::patch('/admin/turnos/{id}/completar', [TurnoController::class, 'completado'])->name('admin.turnos.completar');
    Route::patch('/admin/turnos/{id}/ausente', [TurnoController::class, 'ausente'])->name('admin.turnos.ausente');

    // Turnos del usuario (cliente) -> CRUD completo
    Route::get('/usuario/turnos', [TurnoController::class, 'misTurnosUsuario'])->name('usuario.turnos');
    Route::post('/usuario/turnos', [TurnoController::class, 'store'])->name('usuario.turnos.store');
    Route::get('/usuario/turnos/{id}/edit', [TurnoController::class, 'edit'])->name('usuario.turnos.edit');
    Route::put('/usuario/turnos/{id}', [TurnoController::class, 'update'])->name('usuario.turnos.update');
    
    // Ruta corregida de cancelación lógica del cliente
    Route::patch('/usuario/turnos/{id}/cancelar', [TurnoController::class, 'cancelarCliente'])->name('usuario.turnos.cancelar');

    // Gestión de servicios
    Route::get('/admin/servicios', [ServicioController::class, 'index'])->name('admin.servicios.index');
    Route::get('/admin/servicios/create', [ServicioController::class, 'create'])->name('admin.servicios.create');
    Route::post('/admin/servicios', [ServicioController::class, 'store'])->name('admin.servicios.store');
    Route::get('/admin/servicios/{id}/edit', [ServicioController::class, 'edit'])->name('admin.servicios.edit');
    Route::put('/admin/servicios/{id}', [ServicioController::class, 'update'])->name('admin.servicios.update');
    Route::delete('/admin/servicios/{id}', [ServicioController::class, 'destroy'])->name('admin.servicios.destroy');
    Route::post('/admin/servicios/{id}/activate', [ServicioController::class, 'activate'])->name('admin.servicios.activate');

    // Gestión de tipos de servicio
    Route::get('/admin/tiposervicios', [TipoServicioController::class, 'index'])->name('admin.TipoServicios.index');
    Route::get('/admin/tiposervicios/create', [TipoServicioController::class, 'create'])->name('admin.TipoServicios.create');
    Route::post('/admin/tiposervicios', [TipoServicioController::class, 'store'])->name('admin.TipoServicios.store');
    Route::get('/admin/tiposervicios/{id}', [TipoServicioController::class, 'show'])->name('admin.TipoServicios.show');
    Route::get('/admin/tiposervicios/{id}/edit', [TipoServicioController::class, 'edit'])->name('admin.TipoServicios.edit');
    Route::put('/admin/tiposervicios/{id}', [TipoServicioController::class, 'update'])->name('admin.TipoServicios.update');
    Route::delete('/admin/tiposervicios/{id}', [TipoServicioController::class, 'destroy'])->name('admin.TipoServicios.destroy');
    Route::get('/tipos-servicio/{sucursal}', [TipoServicioController::class, 'porSucursal']);
    Route::post('/admin/tiposervicios/{id}/activate', [TipoServicioController::class, 'activate'])->name('admin.TipoServicios.activate');

    // Estadísticas
    Route::get('/admin/estadisticas', [AdminController::class, 'estadisticas'])->name('admin.estadisticas');
    Route::get('/admin/estadisticas/dashboard', [AdminController::class, 'estadisticas'])->name('admin.estadisticas.dashboard');

    // Pagos
    Route::get('/admin/pagos', [AdminController::class, 'pagos'])->name('admin.pagos.index');

    // --- MÓDULO DE MODERACIÓN DE CALIFICACIONES (ADMIN) ---
    Route::get('/admin/calificaciones', [AdminController::class, 'calificaciones'])->name('admin.calificaciones.index');
    Route::patch('/admin/calificaciones/{id}/toggle', [AdminController::class, 'toggleCalificacion'])->name('admin.calificaciones.toggle');

    // --- MÓDULO DE CONFIGURACIÓN GENERAL (WHATSAPP Y REDES) ---
    Route::get('/admin/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion.index');
    Route::post('/admin/configuracion', [ConfiguracionController::class, 'update'])->name('admin.configuracion.update');

    // --- MÓDULO DE RESPALDOS / BACKUPS (ADMIN) ---
    Route::get('/admin/backup', [BackupController::class, 'index'])->name('admin.backup.index');
    Route::post('/admin/backup/frecuencia', [BackupController::class, 'updateFrecuencia'])->name('admin.backup.frecuencia');
    Route::post('/admin/backup/generar', [BackupController::class, 'descargarAhora'])->name('admin.backup.generar');

    // Gestión de imágenes
    Route::get('/admin/imagenes', [ImagenController::class, 'index'])->name('admin.imagenes.index');
    Route::post('/admin/imagenes', [ImagenController::class, 'store'])->name('admin.imagenes.store');
    Route::put('/admin/imagenes/{imagen}', [ImagenController::class, 'update'])->name('admin.imagenes.update');
    Route::delete('/admin/imagenes/{imagen}', [ImagenController::class, 'destroy'])->name('admin.imagenes.destroy');

    // AJAX: obtener barberos por sucursal
    Route::get('/barberos/{id}', [TurnoController::class, 'getBarberosPorSucursal'])->name('barberos.porSucursal');

});

// Mercado Pago
Route::get('/pago', [PagoController::class, 'crearPreferencia'])->name('pago.checkout');
Route::get('/pago/success', [PagoController::class, 'success'])->name('pago.success');
Route::get('/pago/failure', [PagoController::class, 'failure'])->name('pago.failure');
Route::get('/pago/pending', [PagoController::class, 'pending'])->name('pago.pending');

require __DIR__.'/auth.php';

// Rutas de autenticación con Google
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);