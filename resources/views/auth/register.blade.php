<!DOCTYPE html>
<html>
<head>
    <title>Registro - NosaNet</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
</head>
<body>
    <div class="auth-container">
        <h2>Crear Cuenta</h2>
        <form action="{{ route('register.post') }}" method="POST" class="auth-form">
    @csrf
    <h2>Crear Cuenta</h2>
    <input type="text" name="username" placeholder="Usuario" required>
    
    <input type="email" name="email" placeholder="Correo Electrónico" required>
    
    <input type="password" name="password" placeholder="Contraseña" required>
    
    <label class="checkbox-container">
        <input type="checkbox" name="isProfessor" value="True"> Soy Profesor
    </label>
    
    <button type="submit" class="btn-main">Registrarme</button>
</form>
        <a href="{{ route('login') }}">Volver al login</a>
    </div>
</body>
</html>