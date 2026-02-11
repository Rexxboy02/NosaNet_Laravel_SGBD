<?php

namespace App\Http\Controllers;

use App\Workers\AuthWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

/**
 * Controlador de login
 *
 * Maneja las peticiones de inicio y cierre de sesión
 */
class LoginController extends Controller
{
    /**
     * @var AuthWorker
     */
    protected AuthWorker $authWorker;

    /**
     * Constructor del LoginController
     *
     * @param AuthWorker $authWorker Worker de autenticación
     */
    public function __construct(AuthWorker $authWorker)
    {
        $this->authWorker = $authWorker;
    }

    /**
     * Mostrar formulario de login
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión
     *
     * @param Request $request Petición HTTP con credenciales
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validar datos de entrada
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Delegar autenticación al worker
        $result = $this->authWorker->authenticate($request->username, $request->password);

        if (!$result['success']) {
            $errorKey = $result['error'] === 'Usuario no encontrado' ? 'username' : 'password';
            return redirect()->back()
                ->withErrors([$errorKey => $result['error']])
                ->withInput();
        }

        $user = $result['user'];

        // Configurar sesión
        Session::put('user_id', $user->id);
        Session::put('username', $user->username);
        Session::put('email', $user->email);
        Session::put('is_professor', $user->isProfessor);

        $theme = $user->theme ?? 'light';
        Session::put('theme', $theme);

        // Configurar cookie de tema
        Cookie::queue('theme', $theme, 30 * 24 * 60);

        // Regenerar ID de sesión por seguridad
        Session::regenerate();

        return redirect()->route('home')
            ->with('success', 'Bienvenido ' . $user->username)
            ->withCookie(cookie('theme', $theme, 30 * 24 * 60));
    }

    /**
     * Cerrar sesión
     *
     * @param Request $request Petición HTTP
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Session::flush();
        Session::invalidate();
        $request->session()->regenerateToken();

        $cookie = Cookie::forget('theme');

        return redirect()->route('home')
            ->with('success', 'Sesión cerrada correctamente')
            ->withCookie($cookie);
    }
}