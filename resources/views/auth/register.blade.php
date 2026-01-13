@extends('layouts.app')

@section('title', 'NosaNet - Registro')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/js-sha256@0.10.1/src/sha256.min.js"></script>
    <script>
        document.getElementById("register-form").addEventListener("submit", function(e) {
            e.preventDefault();
            const pwd = document.getElementById("password");
            pwd.value = sha256(pwd.value);
            this.submit();
        });
    </script>
@endpush

@section('content')
<div id="formulario">
    <h1>Crear Cuenta</h1>
    
    <form method="post" action="{{ route('register') }}" id="register-form">
        @csrf
        
        <label for="username">Nombre de usuario:</label>
        <input type="text" name="username" id="username" required 
               value="{{ old('username') }}">
        
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
        
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" id="email" required 
               value="{{ old('email') }}">
        
        <div class="checkbox-container">
            <input type="checkbox" name="isProfessor" id="isProfessor" value="True"
                   {{ old('isProfessor') ? 'checked' : '' }}>
            <label for="isProfessor">¿Eres profesor?</label>
        </div>
        
        <input type="submit" value="Registrarse">
        
        @if($errors->any())
            <div class="error-message">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        @if(session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif
    </form>
    
    <div class="auth-links">
        <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
    </div>
</div>
@endsection