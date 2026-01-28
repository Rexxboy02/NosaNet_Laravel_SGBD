<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ModerationController extends Controller
{
    public function index()
    {
        if (!Session::has('is_professor') || Session::get('is_professor') !== 'True') {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }
        
        $pendingMessages = Message::getPending();
        
        // Corregir esta parte
        if (is_array($pendingMessages)) {
            // Ya es un array
        } elseif (method_exists($pendingMessages, 'all')) {
            $pendingMessages = $pendingMessages->all();
        } else {
            $pendingMessages = [];
        }
        
        // Ordenar por timestamp descendente
        usort($pendingMessages, function($a, $b) {
            $timeA = isset($a['timestamp']) ? strtotime(str_replace('/', '-', $a['timestamp'])) : 0;
            $timeB = isset($b['timestamp']) ? strtotime(str_replace('/', '-', $b['timestamp'])) : 0;
            return $timeB - $timeA;
        });
        
        return view('moderation', compact('pendingMessages'));
    }
    
    public function approve($id, Request $request)
    {
        if (!Session::has('is_professor') || Session::get('is_professor') !== 'True') {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }
        
        if (!$id) {
            return redirect()->route('moderation.index')
                ->with('error', 'ID de mensaje no proporcionado');
        }
        
        $request->validate([
            'approve_reason' => 'required|min:3|max:500' // Cambiado a approve_reason
        ]);
        
        $updated = Message::update($id, [
            'approved' => 'true',
            'approve_reason' => htmlspecialchars($request->approve_reason), // Cambiado aquí
            'moderated_at' => date('H:i d/m/Y'),
            'moderated_by' => Session::get('username'),
            'status' => 'active' // Asegúrate de actualizar el estado
        ]);
        
        if ($updated) {
            return redirect()->route('moderation.index')
                ->with('success', 'Mensaje aprobado correctamente');
        } else {
            return redirect()->route('moderation.index')
                ->with('error', 'No se pudo encontrar el mensaje');
        }
    }
    
    public function delete($id, Request $request)
    {
        if (!Session::has('is_professor') || Session::get('is_professor') !== 'True') {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos de moderación');
        }
        
        $request->validate([
            'delete_reason' => 'required|min:3|max:500'
        ]);
        
        $updated = Message::update($id, [
            'status' => 'deleted',
            'delete_reason' => htmlspecialchars($request->delete_reason),
            'deleted_at' => date('H:i d/m/Y'),
            'deleted_by' => Session::get('username'),
            'approved' => 'false' // Asegúrate de marcar como no aprobado
        ]);
        
        if ($updated) {
            return redirect()->route('moderation.index')
                ->with('success', 'Mensaje eliminado correctamente');
        } else {
            return redirect()->route('moderation.index')
                ->with('error', 'No se pudo encontrar el mensaje');
        }
    }
}