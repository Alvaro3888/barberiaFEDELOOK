<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TipoServicio;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pasar automáticamente la lista de tipos de servicio activos a los layouts
        View::composer(['layouts.app','layouts.public','layouts.barbero'], function ($view) {
            $view->with('tipos', TipoServicio::where('activo', true)->get());
        });
    }
}
