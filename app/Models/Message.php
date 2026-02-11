<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Message extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user',
        'title',
        'text',
        'asignatura',
        'approved',
        'status',
        'timestamp',
        'dangerous_content',
        'approve_reason',
        'delete_reason',
        'moderated_at',
        'moderated_by',
        'deleted_at',
        'deleted_by'
    ];

    /**
     * Obtener mensajes pendientes de aprobación
     */
    public static function getPending(): Collection
    {
        return self::where('approved', 'pending')
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Obtener mensajes aprobados
     */
    public static function getApproved(): Collection
    {
        return self::where('approved', 'true')
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Obtener mensajes eliminados
     */
    public static function getDeleted(): Collection
    {
        return self::where('status', 'deleted')->get();
    }

    /**
     * Obtener mensajes de un usuario específico
     */
    public static function getUserMessages($username): Collection
    {
        return self::where('user', $username)->get();
    }
}