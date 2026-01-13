<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller {
    public function index() {
        $mensajes = json_decode(Storage::get('json/messages.json') ?? '[]', true);
        $aprobados = array_filter($mensajes, fn($m) => $m['approved'] === 'true' && $m['status'] === 'active');
        return view('home', ['messages' => $aprobados]);
    }

    public function store(Request $request) {
        $esPeligroso = $this->filtroSeguridad($request->text);
        $mensajes = json_decode(Storage::get('json/messages.json') ?? '[]', true);
        
        $mensajes[] = [
            'id' => uniqid(),
            'user' => session('usuario'),
            'title' => $request->title,
            'text' => $request->text,
            'approved' => (session('es_profesor') === "True" && !$esPeligroso) ? 'true' : 'pending',
            'status' => 'active',
            'timestamp' => now()->format('H:i d/m/Y'),
            'dangerous_content' => $esPeligroso ? 'true' : 'false'
        ];

        Storage::put('json/messages.json', json_encode($mensajes, JSON_PRETTY_PRINT));
        return redirect()->route('home');
    }

    private function filtroSeguridad($t) {
        $p = ['/<\\s*script\\b/i', '/maric[oó]n\\b/ui', '/imb[eé]cil\\b/ui'];
        foreach ($p as $patron) if (preg_match($patron, $t)) return true;
        return false;
    }
}