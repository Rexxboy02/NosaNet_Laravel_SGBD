<?php

namespace App\Workers;

use App\Repositories\MessageRepository;
use Illuminate\Support\Collection;

/**
 * Worker para la lógica de negocio de moderación
 *
 * Maneja la aprobación y eliminación de mensajes
 */
class ModerationWorker
{
    /**
     * @var MessageRepository
     */
    protected MessageRepository $messageRepository;

    /**
     * Constructor del ModerationWorker
     *
     * @param MessageRepository $messageRepository
     */
    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Obtener mensajes pendientes de moderación
     *
     * @return Collection
     */
    public function getPendingMessages(): Collection
    {
        return $this->messageRepository->getPending()
                                      ->sortByDesc('timestamp');
    }

    /**
     * Aprobar un mensaje
     *
     * @param string $messageId ID del mensaje
     * @param string $reason Razón de aprobación
     * @param string $moderatorUsername Username del moderador
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function approveMessage(string $messageId, string $reason, string $moderatorUsername): array
    {
        $message = $this->messageRepository->findById($messageId);

        if (!$message) {
            return [
                'success' => false,
                'error' => 'No se pudo encontrar el mensaje'
            ];
        }

        $sanitizedReason = htmlspecialchars($reason, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $this->messageRepository->approve($message, $sanitizedReason, $moderatorUsername);

        return [
            'success' => true,
            'error' => null
        ];
    }

    /**
     * Eliminar un mensaje
     *
     * @param string $messageId ID del mensaje
     * @param string $reason Razón de eliminación
     * @param string $moderatorUsername Username del moderador
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function deleteMessage(string $messageId, string $reason, string $moderatorUsername): array
    {
        $message = $this->messageRepository->findById($messageId);

        if (!$message) {
            return [
                'success' => false,
                'error' => 'No se pudo encontrar el mensaje'
            ];
        }

        $sanitizedReason = htmlspecialchars($reason, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $this->messageRepository->deleteMessage($message, $sanitizedReason, $moderatorUsername);

        return [
            'success' => true,
            'error' => null
        ];
    }

    /**
     * Verificar si un usuario es profesor/moderador
     *
     * @param string|null $isProfessor Valor de isProfessor de la sesión
     * @return bool
     */
    public function isModerator(?string $isProfessor): bool
    {
        return $isProfessor === 'True';
    }
}
