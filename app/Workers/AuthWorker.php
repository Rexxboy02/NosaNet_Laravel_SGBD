<?php

namespace App\Workers;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

/**
 * Worker para la lógica de negocio de autenticación
 *
 * Maneja la lógica de registro, login y validaciones de autenticación
 */
class AuthWorker
{
    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * Constructor del AuthWorker
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Registrar un nuevo usuario
     *
     * @param array $data Datos del usuario (username, email, password, isProfessor)
     * @return array ['success' => bool, 'user' => User|null, 'error' => string|null]
     */
    public function register(array $data): array
    {
        // Verificar si el usuario ya existe
        if ($this->userRepository->findByUsername($data['username'])) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Nombre de usuario en uso'
            ];
        }

        // Verificar si el email ya está en uso
        if ($this->userRepository->findByEmail($data['email'])) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Correo electrónico en uso'
            ];
        }

        // Crear el usuario
        $userData = [
            'id' => uniqid(),
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'isProfessor' => $data['isProfessor'] ?? 'False',
            'theme' => 'light'
        ];

        $user = $this->userRepository->create($userData);

        return [
            'success' => true,
            'user' => $user,
            'error' => null
        ];
    }

    /**
     * Autenticar un usuario
     *
     * @param string $username Nombre de usuario
     * @param string $password Contraseña
     * @return array ['success' => bool, 'user' => User|null, 'error' => string|null]
     */
    public function authenticate(string $username, string $password): array
    {
        // Buscar el usuario
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Usuario no encontrado'
            ];
        }

        // Verificar la contraseña
        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Contraseña incorrecta'
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'error' => null
        ];
    }

    /**
     * Actualizar el tema del usuario
     *
     * @param string $username Nombre de usuario
     * @param string $theme Tema (light o dark)
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function updateTheme(string $username, string $theme): array
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'error' => 'Usuario no encontrado'
            ];
        }

        if (!in_array($theme, ['light', 'dark'])) {
            return [
                'success' => false,
                'error' => 'Tema inválido'
            ];
        }

        $user->theme = $theme;
        $this->userRepository->save($user);

        return [
            'success' => true,
            'error' => null
        ];
    }
}
