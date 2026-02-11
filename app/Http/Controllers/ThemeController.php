<?php
// app/Http/Controllers/ThemeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class ThemeController extends Controller
{
    /**
     * Cambiar entre tema claro y oscuro
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Request $request)
    {
        // Obtener tema actual de la sesión
        $currentTheme = Session::get('theme', 'light');
        
        // Alternar entre light/dark
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';
        
        // Guardar en sesión
        Session::put('theme', $newTheme);
        
        // Guardar en base de datos si el usuario está autenticado
        if (auth_check()) {
            $username = Session::get('username');
            $user = User::findByUsername($username);

            if ($user) {
                // Actualizar el tema del usuario en la base de datos
                $user->theme = $newTheme;
                $user->save();
            }
        }
        
        // Crear cookie que dura 30 días
        $cookie = Cookie::make('theme', $newTheme, 30 * 24 * 60);
        
        // Redirigir a la página anterior con la cookie
        return redirect()->back()->withCookie($cookie);
    }
}
