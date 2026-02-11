<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo User
 *
 * Representa la entidad de usuario en la base de datos
 * Los usuarios pueden ser profesores o estudiantes
 */
class User extends Model
{
    /**
     * Clave primaria de la tabla
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la clave primaria es auto-incremental
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipo de dato de la clave primaria
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'isProfessor',
        'theme'
    ];

    /**
     * Atributos que deben ocultarse en arrays
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Encontrar usuario por nombre de usuario
     *
     * @param string $username Nombre de usuario
     * @return User|null
     * @deprecated Use UserRepository::findByUsername() en su lugar
     */
    public static function findByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    /**
     * Encontrar usuario por email
     *
     * @param string $email Email del usuario
     * @return User|null
     * @deprecated Use UserRepository::findByEmail() en su lugar
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Obtener usuario por ID
     *
     * @param string $id ID del usuario
     * @return User|null
     * @deprecated Use UserRepository::findById() en su lugar
     */
    public static function findById($id)
    {
        return self::find($id);
    }
}