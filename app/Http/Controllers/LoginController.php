<?php
// app/Http/Controllers/LoginController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }
    
    /**
     * Manejar el proceso de login
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        
        // Buscar el usuario por nombre de usuario
        $user = User::findByUsername($request->username);
        
        // Si no se encuentra el usuario, redirigir de vuelta con error
        if (!$user) {
            return redirect()->back()
                ->withErrors(['username' => 'Usuario no encontrado'])
                ->withInput();
        }
        
        // Verificar la contraseña usando Hash::check
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Contraseña incorrecta'])
                ->withInput();
        }

        // Guardar datos en sesión
        Session::put('user_id', $user->id);
        Session::put('username', $user->username);
        Session::put('email', $user->email);
        Session::put('is_professor', $user->isProfessor);

        // Cargar tema del usuario desde la base de datos
        $theme = $user->theme ?? 'light';
        Session::put('theme', $theme);

        // Crear cookie del tema que dura 30 días
        Cookie::queue('theme', $theme, 30 * 24 * 60);

        // Regenerar ID de sesión por seguridad
        Session::regenerate();

        return redirect()->route('home')
            ->with('success', 'Bienvenido ' . $user->username)
            ->withCookie(cookie('theme', $theme, 30 * 24 * 60));
    }
    
    /**
     * Manejar logout
     */
    public function logout(Request $request)
    {
        // Limpiar toda la sesión
        Session::flush();
        
        // Invalidar la sesión
        Session::invalidate();
        
        // Regenerar token CSRF
        $request->session()->regenerateToken();
        
        // Crear cookie de tema vacía que expira en el pasado
        $cookie = Cookie::forget('theme');
        
        return redirect()->route('home')
            ->with('success', 'Sesión cerrada correctamente')
            ->withCookie($cookie);
    }
}