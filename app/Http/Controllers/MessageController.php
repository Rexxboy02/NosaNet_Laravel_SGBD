<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MessageController extends Controller
{
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
        $approved = $isProfessor ? 'true' : 'pending';
        
        // Validar contenido peligroso
        $dangerous = $this->validateContent($request->text, $request->title);
        
        $message = Message::create([
            'id' => uniqid(),
            'user' => Session::get('username'),
            'title' => htmlspecialchars($request->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'text' => htmlspecialchars($request->text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'asignatura' => $request->asignatura,
            'approved' => $approved,
            'status' => 'active',
            'timestamp' => date('H:i d/m/Y'),
            'dangerous_content' => $dangerous
        ]);
        
        $successMsg = $approved === 'true' 
            ? 'Mensaje publicado exitosamente'
            : 'Mensaje enviado para moderación';
        
        return redirect()->route('home')->with('success', $successMsg);
    }
    
    private function validateContent($text, $title)
    {
        $textLower = mb_strtolower(trim($text), 'UTF-8');
        $titleLower = mb_strtolower(trim($title), 'UTF-8');
        
        $palabrasOfensivas = [
            '/maric[oó]n\b/ui',
            '/imb[eé]cil\b/ui',
            '/hijo\s*de\s*puta/ui',
            '/tu\s*puta\s*madre\b/ui',
            '/capullo\b/ui',
            '/gilipollas\b/ui',
            '/retrasado\b/ui',
            '/puta\b/ui',
        ];
        
        $patronesPeligrosos = [
            '/<\s*script\b/i',
            '/\bon\w+\s*=/i',
            '/\bjavascript\s*:/i',
            '/\bdata\s*:/i',
            '/\bvbscript\s*:/i',
            '/\bdrop\s+table\b/i',
            '/\btruncate\s+table\b/i',
            '/\bdelete\s+from\b/i',
            '/;.*\b(drop|truncate|delete)\b/i',
            '/\bunion\b.*\bselect\b/i',
            '/<\s*(iframe|object|embed|form)\b/i',
            '/\\x[0-9a-f]{2}/i',
            '/&#x?[0-9a-f]+;/i',
        ];
        
        foreach ($palabrasOfensivas as $patron) {
            if (preg_match($patron, $textLower) || preg_match($patron, $titleLower)) {
                return 'words';
            }
        }
        
        foreach ($patronesPeligrosos as $patron) {
            if (preg_match($patron, $textLower) || preg_match($patron, $titleLower)) {
                return 'attack';
            }
        }
        
        return 'false';
    }
}