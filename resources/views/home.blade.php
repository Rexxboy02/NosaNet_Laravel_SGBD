<!DOCTYPE html>
<html lang="es">
<head>
    <title>NosaNet - Home</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
</head>
<body data-theme="{{ session('theme', 'light') }}">
    <nav>
        @if(session('usuario'))
            <p>Usuario: {{ session('usuario') }} ({{ session('es_profesor') == 'True' ? 'Profesor' : 'Alumno' }})</p>
            <form action="{{ route('logout') }}" method="POST"> @csrf <button>Salir</button> </form>
        @else
            <a href="{{ route('login') }}">Login</a> | <a href="{{ route('register') }}">Registro</a>
        @endif
    </nav>

    <hr>

    @if(session('usuario'))
        <form action="{{ route('messages.store') }}" method="POST">
            @csrf
            <input type="text" name="title" placeholder="Título" required><br>
            <textarea name="text" placeholder="Escribe algo..." required></textarea><br>
            <button type="submit">Publicar</button>
        </form>
    @endif

    <h2>Mensajes</h2>
    @foreach($messages as $m)
        <div style="border:1px solid #ccc; margin:10px; padding:10px;">
            <h3>{{ $m['title'] }}</h3>
            <p>{{ $m['text'] }}</p>
            <small>Publicado por {{ $m['user'] }} el {{ $m['timestamp'] }}</small>
        </div>
    @endforeach
</body>
</html>