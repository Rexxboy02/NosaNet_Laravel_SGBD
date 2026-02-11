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
        
        $pendingMessages = Message::getPending()->sortByDesc('timestamp');

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
            'approve_reason' => 'required|min:3|max:500'
        ]);

        $message = Message::find($id);

        if ($message) {
            $message->approved = 'true';
            $message->approve_reason = htmlspecialchars($request->approve_reason);
            $message->moderated_at = date('H:i d/m/Y');
            $message->moderated_by = Session::get('username');
            $message->status = 'active';
            $message->save();

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

        $message = Message::find($id);

        if ($message) {
            $message->status = 'deleted';
            $message->delete_reason = htmlspecialchars($request->delete_reason);
            $message->deleted_at = date('H:i d/m/Y');
            $message->deleted_by = Session::get('username');
            $message->approved = 'false';
            $message->save();

            return redirect()->route('moderation.index')
                ->with('success', 'Mensaje eliminado correctamente');
        } else {
            return redirect()->route('moderation.index')
                ->with('error', 'No se pudo encontrar el mensaje');
        }
    }
}