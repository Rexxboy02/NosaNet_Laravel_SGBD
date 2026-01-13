{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'NosaNet - Home')

@section('content')
<div class="messages-container">
    @if(auth_check())
        <div class="post-form">
            <h2 id="chanquete2">Publicar Nuevo Mensaje</h2>
            <form method="post" action="{{ route('messages.store') }}">
                @csrf
                
                <div class="form-group">
                    <label for="title">Título:</label>
                    <input type="text" name="title" id="title" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="text">Mensaje:</label>
                    <textarea name="text" id="text" required maxlength="250" 
                              placeholder="Escribe tu mensaje aquí (máximo 250 caracteres)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="asignatura">Asignatura:</label>
                    <select required id="asignatura" name="asignatura">
                        <option value="" disabled selected>Elige una opción</option>
                        <option value="Matemáticas">Matemáticas</option>
                        <option value="Lengua">Lengua</option>
                        <option value="Inglés">Inglés</option>
                        <option value="Historia">Historia</option>
                        <option value="Geografía">Geografía</option>
                        <option value="Física">Física</option>
                        <option value="Química">Química</option>
                        <option value="Biología">Biología</option>
                        <option value="Informática">Informática</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">Publicar Mensaje</button>
            </form>
        </div>
        
        <div class="messages-list">
            <h2 id="recientes">Mensajes Recientes</h2>
            
            {{-- Usar funciones de PHP nativas para arrays --}}
            @if(empty($messages) || count($messages) === 0)
                <div class="no-messages">
                    <p>No hay mensajes públicos todavía. ¡Sé el primero en publicar!</p>
                </div>
            @else
                {{-- Convertir a array si es colección --}}
                @php
                    if ($messages instanceof \Illuminate\Support\Collection) {
                        $messagesArray = $messages->all();
                    } else {
                        $messagesArray = $messages;
                    }
                    
                    // Ordenar por timestamp descendente si es array
                    if (is_array($messagesArray)) {
                        usort($messagesArray, function($a, $b) {
                            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                        });
                    }
                @endphp
                
                @foreach($messagesArray as $message)
                    <div class="message-card">
                        <div class="message-header">
                            <span class="message-user">{{ $message['user'] ?? 'Usuario' }}</span>
                            <span class="message-time">{{ $message['timestamp'] ?? 'Sin fecha' }}</span>
                        </div>
                        
                        <div class="message-title">{{ $message['title'] ?? 'Sin título' }}</div>
                        <div class="message-text">{{ $message['asignatura'] ?? 'Sin asignatura' }}</div>
                        <div class="message-text">{{ $message['text'] ?? 'Sin contenido' }}</div>
                        
                        <span class="message-status status-approved">
                            Aprobado
                        </span>
                        
                        @if(is_professor() && ($message['approved'] ?? '') === 'pending')
                            <div class="moderation-actions">
                                <form method="post" action="{{ route('moderation.approve') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="message_id" value="{{ $message['id'] ?? '' }}">
                                    <button type="submit" class="btn-approve">Aprobar</button>
                                </form>
                                
                                <form method="post" action="{{ route('moderation.delete') }}" 
                                      onsubmit="return confirm('¿Estás seguro?')" 
                                      style="display: inline; margin-left: 10px;">
                                    @csrf
                                    <input type="hidden" name="message_id" value="{{ $message['id'] ?? '' }}">
                                    <input type="text" name="delete_reason" placeholder="Razón..." 
                                           class="delete-reason" required>
                                    <button type="submit" class="btn-delete">Eliminar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @else
        <div class="no-messages">
            <h2 class="colores">Bienvenido a NosaNet</h2>
            <p class="colores">Inicia sesión para ver y publicar mensajes.</p>
            <a href="{{ route('login') }}" class="btn-submit">
                Iniciar Sesión
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Contador de caracteres
    const textarea = document.getElementById('text');
    if (textarea) {
        const counter = document.createElement('div');
        counter.style.textAlign = 'right';
        counter.style.fontSize = '0.8rem';
        counter.style.color = '#718096';
        counter.style.marginTop = '0.5rem';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const length = textarea.value.length;
            counter.textContent = `${length}/250 caracteres`;
            counter.style.color = length > 250 ? '#e53e3e' : '#718096';
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    }
</script>
@endpush
@endsection