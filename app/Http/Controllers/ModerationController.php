<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModerationController extends Controller {
    
    public function index() {
        // Solo entran los profesores
        if (session('es_profesor') !== "True") {
            return redirect()->route('home')->with('error', 'Acceso denegado');
        }

        $mensajes = json_decode(Storage::get('json/messages.json') ?? '[]', true);
        
        // Filtramos: pendientes o marcados como peligrosos por el filtro
        $pendientes = array_filter($mensajes, function($m) {
            return $m['approved'] === 'pending' || $m['dangerous_content'] === 'true';
        });

        return view('moderation', ['pendientes' => $pendientes]);
    }

    public function process(Request $request) {
        $mensajes = json_decode(Storage::get('json/messages.json') ?? '[]', true);
        $id = $request->message_id;
        $accion = $request->action; // 'approve' o 'delete'

        foreach ($mensajes as &$m) {
            if ($m['id'] === $id) {
                if ($accion === 'approve') {
                    $m['approved'] = 'true';
                    $m['dangerous_content'] = 'false'; // Al aprobarlo, quitamos la alerta
                } else {
                    $m['status'] = 'deleted';
                }
            }
        }

        Storage::put('json/messages.json', json_encode($mensajes, JSON_PRETTY_PRINT));
        return back()->with('success', 'Acción realizada');
    }
}