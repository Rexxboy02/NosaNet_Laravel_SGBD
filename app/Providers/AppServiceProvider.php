<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar helpers
        require_once app_path('Helpers/helpers.php');
    }
    
    public function boot()
    {
        // Middleware personalizado
        $this->app['router']->aliasMiddleware('auth.custom', \App\Http\Middleware\AuthCustomMiddleware::class);
        $this->app['router']->aliasMiddleware('professor', \App\Http\Middleware\ProfessorMiddleware::class);
    }
}