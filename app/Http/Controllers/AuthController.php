<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[A-Za-z0-9._-]{1,24}$/',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'isProfessor' => 'sometimes|in:True,False'
        ], [
            'username.regex' => 'Nombre de usuario inválido. Solo puede contener letras, numeros y los símbolos \'_\', \'-\' y \'.\'.'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Verificar si el usuario ya existe
        if (User::findByUsername($request->username)) {
            return redirect()->back()
                ->withErrors(['username' => 'Nombre de usuario en uso'])
                ->withInput();
        }
        
        if (User::findByEmail($request->email)) {
            return redirect()->back()
                ->withErrors(['email' => 'Correo electrónico en uso'])
                ->withInput();
        }
        
        // Crear usuario
        $user = User::create([
            'id' => uniqid(),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'isProfessor' => $request->isProfessor ?? 'False',
            'theme' => 'light'
        ]);
        
        return redirect()->route('login')
            ->with('success', 'Registro exitoso. Ya puedes iniciar sesión.');
    }
}