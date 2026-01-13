<?php
// app/Http/Controllers/LoginController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        
        $user = User::findByUsername($request->username);
        
        if (!$user) {
            return redirect()->back()
                ->withErrors(['username' => 'Usuario no encontrado'])
                ->withInput();
        }
        
        if (!Hash::check($request->password, $user['password'])) {
            return redirect()->back()
                ->withErrors(['password' => 'Contraseña incorrecta'])
                ->withInput();
        }
        
        // Crear sesión personalizada (ya que no usamos Eloquent)
        Session::put('user_id', $user['id']);
        Session::put('username', $user['username']);
        Session::put('email', $user['email']);
        Session::put('is_professor', $user['isProfessor']);
        Session::put('theme', $user['theme'] ?? 'light');
        
        return redirect()->route('home')
            ->with('success', 'Bienvenido ' . $user['username']);
    }
    
    public function logout()
    {
        Session::flush();
        return redirect()->route('home');
    }
}