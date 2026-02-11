<?php

namespace App\Http\Controllers;

use App\Workers\AuthWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

/**
 * Controlador de tema
 *
 * Maneja el cambio de tema claro/oscuro
 */
class ThemeController extends Controller
{
    /**
     * @var AuthWorker
     */
    protected AuthWorker $authWorker;

    /**
     * Constructor del ThemeController
     *
     * @param AuthWorker $authWorker Worker de autenticación
     */
    public function __construct(AuthWorker $authWorker)
    {
        $this->authWorker = $authWorker;
    }

    /**
     * Alternar entre tema claro y oscuro
     *
     * @param Request $request Petición HTTP
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Request $request)
    {
        $currentTheme = Session::get('theme', 'light');
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';

        Session::put('theme', $newTheme);

        // Guardar en base de datos si el usuario está autenticado
        if (auth_check()) {
            $username = Session::get('username');
            $this->authWorker->updateTheme($username, $newTheme);
        }

        $cookie = Cookie::make('theme', $newTheme, 30 * 24 * 60);

        return redirect()->back()->withCookie($cookie);
    }
}
