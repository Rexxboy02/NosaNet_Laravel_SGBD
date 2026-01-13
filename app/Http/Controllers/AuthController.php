<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller {
    public function login(Request $request) {
        $users = json_decode(Storage::get('json/users.json') ?? '[]', true);
        foreach ($users as $u) {
            if ($u['username'] === $request->username && password_verify($request->password, $u['password'])) {
                session([
                    'usuario' => $u['username'],
                    'es_profesor' => $u['isProfessor'],
                    'theme' => $u['theme'] ?? 'light'
                ]);
                return redirect()->route('home');
            }
        }
        return back()->with('error', 'Credenciales incorrectas');
    }

    public function register(Request $request) {
    $users = json_decode(Storage::get('json/users.json') ?? '[]', true);
    
    $users[] = [
        'id' => uniqid(),
        'username' => $request->username,
        'email' => $request->email, // <-- GUARDAMOS EL EMAIL
        'password' => password_hash($request->password, PASSWORD_DEFAULT),
        'isProfessor' => $request->isProfessor ?? 'False',
        'theme' => 'light'
    ];
    
    Storage::put('json/users.json', json_encode($users, JSON_PRETTY_PRINT));
    return redirect()->route('login')->with('success', 'Cuenta creada correctamente');
}

    public function logout() {
        session()->flush();
        return redirect()->route('login');
    }
}