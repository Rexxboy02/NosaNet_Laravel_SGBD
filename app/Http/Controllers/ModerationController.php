<?php
// app/Http\Controllers/ModerationController.php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor');
    }
    
    public function index()
    {
        $pendingMessages = Message::getPending();
        return view('moderation', compact('pendingMessages'));
    }
    
    public function approve(Request $request)
    {
        $messageId = $request->message_id;
        
        Message::update($messageId, [
            'approved' => 'true',
            'moderated_at' => date('H:i d/m/Y'),
            'moderated_by' => Session::get('username')
        ]);
        
        return redirect()->route('moderation.index')
            ->with('success', 'Mensaje aprobado correctamente');
    }
    
    public function delete(Request $request)
    {
        $request->validate([
            'message_id' => 'required',
            'delete_reason' => 'required'
        ]);
        
        Message::update($request->message_id, [
            'status' => 'deleted',
            'delete_reason' => htmlspecialchars($request->delete_reason),
            'deleted_at' => date('H:i d/m/Y'),
            'deleted_by' => Session::get('username')
        ]);
        
        return redirect()->route('moderation.index')
            ->with('success', 'Mensaje eliminado correctamente');
    }
}