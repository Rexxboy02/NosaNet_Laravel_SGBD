<?php

namespace App\Http\Controllers;

use App\Workers\ModerationWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Controlador de moderación
 *
 * Maneja las peticiones de moderación de mensajes
 */
class ModerationController extends Controller
{
    /**
     * @var ModerationWorker
     */
    protected ModerationWorker $moderationWorker;

    /**
     * Constructor del ModerationController
     *
     * @param ModerationWorker $moderationWorker Worker de moderación
     */
    public function __construct(ModerationWorker $moderationWorker)
    {
        $this->moderationWorker = $moderationWorker;
    }

    /**
     * Mostrar panel de moderación con mensajes pendientes
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!$this->moderationWorker->isModerator(Session::get('is_professor'))) {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }

        $pendingMessages = $this->moderationWorker->getPendingMessages();

        return view('moderation', compact('pendingMessages'));
    }

    /**
     * Aprobar un mensaje
     *
     * @param string $id ID del mensaje
     * @param Request $request Petición HTTP con la razón de aprobación
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id, Request $request)
    {
        if (!$this->moderationWorker->isModerator(Session::get('is_professor'))) {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }

        if (!$id) {
            return redirect()->route('moderation.index')
                ->with('error', 'ID de mensaje no proporcionado');
        }

        $request->validate([
            'approve_reason' => 'required|min:3|max:500'
        ]);

        // Delegar al worker la aprobación del mensaje
        $result = $this->moderationWorker->approveMessage(
            $id,
            $request->approve_reason,
            Session::get('username')
        );

        if ($result['success']) {
            return redirect()->route('moderation.index')
                ->with('success', 'Mensaje aprobado correctamente');
        } else {
            return redirect()->route('moderation.index')
                ->with('error', $result['error']);
        }
    }

    /**
     * Eliminar un mensaje
     *
     * @param string $id ID del mensaje
     * @param Request $request Petición HTTP con la razón de eliminación
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        if (!$this->moderationWorker->isModerator(Session::get('is_professor'))) {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }

        $request->validate([
            'delete_reason' => 'required|min:3|max:500'
        ]);

        // Delegar al worker la eliminación del mensaje
        $result = $this->moderationWorker->deleteMessage(
            $id,
            $request->delete_reason,
            Session::get('username')
        );

        if ($result['success']) {
            return redirect()->route('moderation.index')
                ->with('success', 'Mensaje eliminado correctamente');
        } else {
            return redirect()->route('moderation.index')
                ->with('error', $result['error']);
        }
    }
}