<?php

namespace App\Http\Controllers;

use App\Workers\AuthWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador de autenticación
 *
 * Maneja las peticiones relacionadas con el registro de usuarios
 */
class AuthController extends Controller
{
    /**
     * @var AuthWorker
     */
    protected AuthWorker $authWorker;

    /**
     * Constructor del AuthController
     *
     * @param AuthWorker $authWorker Worker de autenticación
     */
    public function __construct(AuthWorker $authWorker)
    {
        $this->authWorker = $authWorker;
    }

    /**
     * Mostrar formulario de registro
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro de un nuevo usuario
     *
     * @param Request $request Petición HTTP con los datos del usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validar datos de entrada
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

        // Delegar la lógica de negocio al worker
        $result = $this->authWorker->register([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'isProfessor' => $request->isProfessor ?? 'False'
        ]);

        if (!$result['success']) {
            return redirect()->back()
                ->withErrors(['username' => $result['error']])
                ->withInput();
        }

        return redirect()->route('login')
            ->with('success', 'Registro exitoso. Ya puedes iniciar sesión.');
    }
}