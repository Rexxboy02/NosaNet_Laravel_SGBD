<!DOCTYPE html>
<html>
<head>
    <title>Login - NosaNet</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
</head>
<body>
    <div class="auth-container">
        <h2>Iniciar Sesión</h2>
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </div>
</body>
</html>