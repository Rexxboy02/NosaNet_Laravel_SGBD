<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Modelo Message
 *
 * Representa la entidad de mensaje en la base de datos
 * Los mensajes pueden estar aprobados, pendientes o eliminados
 */
class Message extends Model
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
     *
     * @return Collection
     * @deprecated Use MessageRepository::getPending() en su lugar
     */
    public static function getPending(): Collection
    {
        return self::where('approved', 'pending')
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Obtener mensajes aprobados y activos
     *
     * @return Collection
     * @deprecated Use MessageRepository::getApproved() en su lugar
     */
    public static function getApproved(): Collection
    {
        return self::where('approved', 'true')
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Obtener mensajes eliminados
     *
     * @return Collection
     * @deprecated Use MessageRepository::getDeleted() en su lugar
     */
    public static function getDeleted(): Collection
    {
        return self::where('status', 'deleted')->get();
    }

    /**
     * Obtener todos los mensajes de un usuario específico
     *
     * @param string $username Nombre de usuario
     * @return Collection
     * @deprecated Use MessageRepository::getUserMessages() en su lugar
     */
    public static function getUserMessages($username): Collection
    {
        return self::where('user', $username)->get();
    }
}