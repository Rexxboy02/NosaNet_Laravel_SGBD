<?php

namespace App\Http\Controllers;

use App\Workers\MessageWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Controlador de mensajes
 *
 * Maneja las peticiones relacionadas con la creación y visualización de mensajes
 */
class MessageController extends Controller
{
    /**
     * @var MessageWorker
     */
    protected MessageWorker $messageWorker;

    /**
     * Constructor del MessageController
     *
     * @param MessageWorker $messageWorker Worker de mensajes
     */
    public function __construct(MessageWorker $messageWorker)
    {
        $this->messageWorker = $messageWorker;
    }

    /**
     * Guardar un nuevo mensaje
     *
     * @param Request $request Petición HTTP con los datos del mensaje
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!Session::has('username')) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para publicar mensajes');
        }

        $request->validate([
            'title' => 'required|max:100',
            'text' => 'required|max:250',
            'asignatura' => 'required'
        ]);

        $isProfessor = Session::get('is_professor') === 'True';

        // Delegar la creación del mensaje al worker
        $result = $this->messageWorker->createMessage([
            'username' => Session::get('username'),
            'title' => $request->title,
            'text' => $request->text,
            'asignatura' => $request->asignatura
        ], $isProfessor);

        $successMsg = $result['approved']
            ? 'Mensaje publicado exitosamente'
            : 'Mensaje enviado para moderación';

        return redirect()->route('home')->with('success', $successMsg);
    }

    /**
     * Mostrar los mensajes del usuario autenticado
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function myMessages()
    {
        $username = Session::get('username');

        if (!$username) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus mensajes');
        }

        // Delegar al worker la obtención de mensajes categorizados
        $messages = $this->messageWorker->getUserMessagesCategorized($username);

        return view('messages.myMessages', [
            'approvedMessages' => $messages['approved'],
            'pendingMessages' => $messages['pending'],
            'deletedMessages' => $messages['deleted']
        ]);
    }
}