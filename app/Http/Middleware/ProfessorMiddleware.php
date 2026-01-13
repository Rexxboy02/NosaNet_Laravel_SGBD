<?php
// app/Http/Middleware/ProfessorMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class ProfessorMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('is_professor') || Session::get('is_professor') !== 'True') {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }
        
        return $next($request);
    }
}