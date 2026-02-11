<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'isProfessor',
        'theme'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Encontrar usuario por nombre de usuario
     */
    public static function findByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    /**
     * Encontrar usuario por email
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Obtener usuario por ID
     */
    public static function findById($id)
    {
        return self::find($id);
    }
}