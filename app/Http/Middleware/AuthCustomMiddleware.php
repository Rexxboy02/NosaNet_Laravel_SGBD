<?php
// app/Http/Middleware/AuthCustomMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AuthCustomMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('username')) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página');
        }
        
        return $next($request);
    }
}