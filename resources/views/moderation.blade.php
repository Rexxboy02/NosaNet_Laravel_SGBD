<!DOCTYPE html>
<html>
<head>
    <title>Panel de Moderación</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
</head>
<body>
    <h1>Mensajes Pendientes de Revisión</h1>
    <a href="{{ route('home') }}">Volver al inicio</a>

    @foreach($pendientes as $m)
        <div class="message-card" style="border: 2px solid {{ $m['dangerous_content'] == 'true' ? 'red' : 'gray' }}">
            <h3>{{ $m['title'] }}</h3>
            <p>{{ $m['text'] }}</p>
            <p><strong>Autor:</strong> {{ $m['user'] }}</p>
            
            @if($m['dangerous_content'] == 'true')
                <p style="color:red">⚠️ ADVERTENCIA: Filtro de seguridad activado</p>
            @endif

            <form action="{{ route('moderation.action') }}" method="POST">
                @csrf
                <input type="hidden" name="message_id" value="{{ $m['id'] }}">
                <button type="submit" name="action" value="approve">Aprobar</button>
                <button type="submit" name="action" value="delete" style="background:red">Eliminar</button>
            </form>
        </div>
    @endforeach
</body>
</html>