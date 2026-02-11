<?php

namespace App\Workers;

use App\Repositories\MessageRepository;
use Illuminate\Support\Collection;

/**
 * Worker para la lógica de negocio de mensajes
 *
 * Maneja la creación, validación y obtención de mensajes
 */
class MessageWorker
{
    /**
     * @var MessageRepository
     */
    protected MessageRepository $messageRepository;

    /**
     * @var ContentValidationWorker
     */
    protected ContentValidationWorker $contentValidator;

    /**
     * Constructor del MessageWorker
     *
     * @param MessageRepository $messageRepository
     * @param ContentValidationWorker $contentValidator
     */
    public function __construct(
        MessageRepository $messageRepository,
        ContentValidationWorker $contentValidator
    ) {
        $this->messageRepository = $messageRepository;
        $this->contentValidator = $contentValidator;
    }

    /**
     * Crear un nuevo mensaje
     *
     * @param array $data Datos del mensaje
     * @param bool $isProfessor Si el usuario es profesor
     * @return array ['success' => bool, 'message' => Message|null, 'approved' => bool]
     */
    public function createMessage(array $data, bool $isProfessor): array
    {
        // Determinar si se aprueba automáticamente
        $approved = $isProfessor ? 'true' : 'pending';

        // Validar contenido peligroso
        $dangerousContent = $this->contentValidator->validate($data['text'], $data['title']);

        // Sanitizar contenido
        $sanitizedTitle = htmlspecialchars($data['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $sanitizedText = htmlspecialchars($data['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Crear el mensaje
        $messageData = [
            'id' => uniqid(),
            'user' => $data['username'],
            'title' => $sanitizedTitle,
            'text' => $sanitizedText,
            'asignatura' => $data['asignatura'],
            'approved' => $approved,
            'status' => 'active',
            'timestamp' => date('H:i d/m/Y'),
            'dangerous_content' => $dangerousContent
        ];

        $message = $this->messageRepository->create($messageData);

        return [
            'success' => true,
            'message' => $message,
            'approved' => $approved === 'true'
        ];
    }

    /**
     * Obtener mensajes aprobados ordenados
     *
     * @return Collection
     */
    public function getApprovedMessages(): Collection
    {
        return $this->messageRepository->getApproved()
                                      ->sortByDesc('timestamp');
    }

    /**
     * Obtener mensajes de un usuario categorizados
     *
     * @param string $username Nombre de usuario
     * @return array ['approved' => array, 'pending' => array, 'deleted' => array]
     */
    public function getUserMessagesCategorized(string $username): array
    {
        $userMessages = $this->messageRepository->getUserMessages($username);

        $approved = $userMessages->filter(function($message) {
            return $message->status === 'active' && $message->approved === 'true';
        })->sortByDesc('timestamp')->values()->all();

        $pending = $userMessages->filter(function($message) {
            return $message->status === 'active' && $message->approved === 'pending';
        })->sortByDesc('timestamp')->values()->all();

        $deleted = $userMessages->filter(function($message) {
            return $message->status === 'deleted';
        })->sortByDesc('timestamp')->values()->all();

        return [
            'approved' => $approved,
            'pending' => $pending,
            'deleted' => $deleted
        ];
    }
}
