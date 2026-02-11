<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorio para la gestión de usuarios
 *
 * Maneja todas las operaciones de persistencia relacionadas con usuarios
 */
class UserRepository
{
    /**
     * Buscar usuario por nombre de usuario
     *
     * @param string $username Nombre de usuario a buscar
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * Buscar usuario por email
     *
     * @param string $email Email del usuario a buscar
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Buscar usuario por ID
     *
     * @param string $id ID del usuario
     * @return User|null
     */
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Crear un nuevo usuario
     *
     * @param array $data Datos del usuario
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Actualizar un usuario existente
     *
     * @param User $user Usuario a actualizar
     * @param array $data Datos a actualizar
     * @return bool
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Obtener todos los profesores
     *
     * @return Collection
     */
    public function getProfessors(): Collection
    {
        return User::where('isProfessor', 'True')->get();
    }

    /**
     * Obtener todos los estudiantes
     *
     * @return Collection
     */
    public function getStudents(): Collection
    {
        return User::where('isProfessor', 'False')->get();
    }

    /**
     * Guardar cambios en un usuario
     *
     * @param User $user Usuario a guardar
     * @return bool
     */
    public function save(User $user): bool
    {
        return $user->save();
    }
}
