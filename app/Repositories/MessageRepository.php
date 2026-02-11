<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorio para la gestión de mensajes
 *
 * Maneja todas las operaciones de persistencia relacionadas con mensajes
 */
class MessageRepository
{
    /**
     * Buscar mensaje por ID
     *
     * @param string $id ID del mensaje
     * @return Message|null
     */
    public function findById(string $id): ?Message
    {
        return Message::find($id);
    }

    /**
     * Crear un nuevo mensaje
     *
     * @param array $data Datos del mensaje
     * @return Message
     */
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    /**
     * Obtener mensajes pendientes de moderación
     *
     * @return Collection
     */
    public function getPending(): Collection
    {
        return Message::where('approved', 'pending')
                      ->where('status', 'active')
                      ->get();
    }

    /**
     * Obtener mensajes aprobados y activos
     *
     * @return Collection
     */
    public function getApproved(): Collection
    {
        return Message::where('approved', 'true')
                      ->where('status', 'active')
                      ->get();
    }

    /**
     * Obtener mensajes eliminados
     *
     * @return Collection
     */
    public function getDeleted(): Collection
    {
        return Message::where('status', 'deleted')->get();
    }

    /**
     * Obtener todos los mensajes de un usuario
     *
     * @param string $username Nombre de usuario
     * @return Collection
     */
    public function getUserMessages(string $username): Collection
    {
        return Message::where('user', $username)->get();
    }

    /**
     * Obtener mensajes aprobados de un usuario
     *
     * @param string $username Nombre de usuario
     * @return Collection
     */
    public function getUserApprovedMessages(string $username): Collection
    {
        return Message::where('user', $username)
                      ->where('approved', 'true')
                      ->where('status', 'active')
                      ->get();
    }

    /**
     * Obtener mensajes pendientes de un usuario
     *
     * @param string $username Nombre de usuario
     * @return Collection
     */
    public function getUserPendingMessages(string $username): Collection
    {
        return Message::where('user', $username)
                      ->where('approved', 'pending')
                      ->where('status', 'active')
                      ->get();
    }

    /**
     * Obtener mensajes eliminados de un usuario
     *
     * @param string $username Nombre de usuario
     * @return Collection
     */
    public function getUserDeletedMessages(string $username): Collection
    {
        return Message::where('user', $username)
                      ->where('status', 'deleted')
                      ->get();
    }

    /**
     * Actualizar un mensaje
     *
     * @param Message $message Mensaje a actualizar
     * @param array $data Datos a actualizar
     * @return bool
     */
    public function update(Message $message, array $data): bool
    {
        return $message->update($data);
    }

    /**
     * Guardar cambios en un mensaje
     *
     * @param Message $message Mensaje a guardar
     * @return bool
     */
    public function save(Message $message): bool
    {
        return $message->save();
    }

    /**
     * Aprobar un mensaje
     *
     * @param Message $message Mensaje a aprobar
     * @param string $reason Razón de aprobación
     * @param string $moderatorUsername Username del moderador
     * @return bool
     */
    public function approve(Message $message, string $reason, string $moderatorUsername): bool
    {
        $message->approved = 'true';
        $message->approve_reason = $reason;
        $message->moderated_at = date('H:i d/m/Y');
        $message->moderated_by = $moderatorUsername;
        $message->status = 'active';

        return $message->save();
    }

    /**
     * Eliminar un mensaje (soft delete)
     *
     * @param Message $message Mensaje a eliminar
     * @param string $reason Razón de eliminación
     * @param string $moderatorUsername Username del moderador
     * @return bool
     */
    public function deleteMessage(Message $message, string $reason, string $moderatorUsername): bool
    {
        $message->status = 'deleted';
        $message->delete_reason = $reason;
        $message->deleted_at = date('H:i d/m/Y');
        $message->deleted_by = $moderatorUsername;
        $message->approved = 'false';

        return $message->save();
    }
}
