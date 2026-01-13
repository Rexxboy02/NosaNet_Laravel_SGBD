{{-- resources/views/moderation.blade.php --}}
@extends('layouts.app')

@section('title', 'Moderación - NosaNet')

@section('content')
<div class="messages-container">
    <div class="messages-list">
        <h1 id="chanquete">Panel de Moderación</h1>
        <p style="text-align: center; color: #718096; margin-bottom: 2rem;">
            Mensajes pendientes de revisión
        </p>
        
        @if($pendingMessages->isEmpty())
            <div class="no-messages">
                <h3>No hay mensajes pendientes de moderación</h3>
                <p>Todos los mensajes han sido revisados.</p>
            </div>
        @else
            <div class="pending-count" style="text-align: center; margin-bottom: 1.5rem; font-weight: 600; color: #742a2a;">
                {{ $pendingMessages->count() }} mensajes pendientes de revisión
            </div>
            
            @foreach($pendingMessages as $message)
                <div class="message-card">
                    <div class="message-header">
                        <span class="message-user">{{ $message['user'] ?? 'Usuario' }}</span>
                        <span class="message-time">{{ $message['timestamp'] ?? 'Hora desconocida' }}</span>
                    </div>
                    
                    <div class="message-title">{{ $message['title'] ?? 'Sin título' }}</div>
                    <div class="message-text">{{ $message['asignatura'] ?? 'Sin asignatura' }}</div>
                    <div class="message-text">{{ $message['text'] ?? 'Sin contenido' }}</div>
                    
                    @if($message['dangerous_content'] == 'words')
                        <div class="warning-dangerous" style="background-color: red; border-radius: 6px; border: 1px solid black; padding: 0.5rem; color: white">
                            Palabra ofensiva detectada !!
                        </div>
                    @elseif($message['dangerous_content'] == 'attack')
                        <div class="warning-dangerous" style="background-color: red; border-radius: 6px; border: 1px solid black; padding: 0.5rem; color: white">
                            ⚠️ Contenido potencialmente peligroso detectado ⚠️
                        </div>
                    @endif
                    
                    <span class="message-status status-pending">Pendiente de moderación</span>
                    
                    <div class="moderation-actions">
                        <form method="post" action="{{ route('moderation.approve') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="message_id" value="{{ $message['id'] }}">
                            <button type="submit" class="btn-approve" 
                                    onclick="return confirm('¿Aprobar este mensaje?')">
                                Aprobar Mensaje
                            </button>
                        </form>
                        
                        <form method="post" action="{{ route('moderation.delete') }}" 
                              style="display: inline; margin-left: 10px;">
                            @csrf
                            <input type="hidden" name="message_id" value="{{ $message['id'] }}">
                            <input type="text" name="delete_reason" placeholder="Razón de eliminación..." 
                                   class="delete-reason" required>
                            <button type="submit" class="btn-delete" 
                                    onclick="return confirm('¿Eliminar este mensaje?')">
                                Eliminar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection