{{-- resources/views/messages/myMessages.blade.php --}}
@extends('layouts.app')

@section('title', 'Mis Posts - NosaNet')

@section('content')
<div class="tabs-container">
    <h1>Mis Posts</h1>
    
    <div class="tabs">
        <button class="tab active" onclick="showTab('aprobados')">
            Aprobados <span class="posts-count">{{ count($approvedMessages) }}</span>
        </button>
        <button class="tab" onclick="showTab('pendientes')">
            Pendientes <span class="posts-count">{{ count($pendingMessages) }}</span>
        </button>
        <button class="tab" onclick="showTab('eliminados')">
            Eliminados <span class="posts-count">{{ count($deletedMessages) }}</span>
        </button>
    </div>
    
    <!-- Tab Aprobados -->
    <div id="aprobados" class="tab-content active">
        <h2>Mensajes Aprobados</h2>
        @if(empty($approvedMessages))
            <div class="empty-state">
                <h3>No tienes mensajes aprobados</h3>
                <p>Los mensajes que sean aprobados por los moderadores aparecerán aquí.</p>
            </div>
        @else
            @foreach(array_reverse($approvedMessages) as $message)
                <div class="message-card">
                    <div class="message-header">
                        <span class="message-user">{{ $message['user'] }}</span>
                        <span class="message-time">{{ $message['timestamp'] }}</span>
                    </div>
                    <div class="message-title">{{ $message['title'] }}</div>
                    <div class="message-text">{{ $message['text'] }}</div>
                    <span class="message-status status-approved">Aprobado</span>
                    
                    @if(isset($message['moderated_at']))
                        <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #718096;">
                            Moderado el: {{ $message['moderated_at'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
    
    <!-- ... resto del código similar ... -->
</div>
@endsection