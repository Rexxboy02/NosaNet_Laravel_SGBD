<?php

namespace App\Http\Controllers;

use App\Workers\MessageWorker;

/**
 * Controlador de la página principal
 *
 * Maneja la visualización de la página de inicio
 */
class HomeController extends Controller
{
    /**
     * @var MessageWorker
     */
    protected MessageWorker $messageWorker;

    /**
     * Constructor del HomeController
     *
     * @param MessageWorker $messageWorker Worker de mensajes
     */
    public function __construct(MessageWorker $messageWorker)
    {
        $this->messageWorker = $messageWorker;
    }

    /**
     * Mostrar la página de inicio con mensajes aprobados
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Delegar al worker la obtención de mensajes aprobados
        $messages = $this->messageWorker->getApprovedMessages();

        return view('home', compact('messages'));
    }
}