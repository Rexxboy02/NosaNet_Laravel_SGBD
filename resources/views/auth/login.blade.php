@extends('layouts.app')

@section('title', 'NosaNet - Iniciar Sesión')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/js-sha256@0.10.1/src/sha256.min.js"></script>
    <script>
        document.getElementById("login-form").addEventListener("submit", function(e) {
            e.preventDefault();
            const pwd = document.getElementById("password");
            pwd.value = sha256(pwd.value);
            this.submit();
        });
    </script>
@endpush

@section('content')
<div id="formulario">
    <h1>Iniciar Sesión</h1>
    
    <form method="post" action="{{ route('login') }}" id="login-form">
        @csrf
        
        <label for="username">Nombre de usuario:</label>
        <input type="text" name="username" id="username" required 
               value="{{ old('username') }}">
        
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
        
        <input type="submit" value="Iniciar Sesión">
        
        @error('username')
            <p class="error-message">{{ $message }}</p>
        @enderror
        
        @error('password')
            <p class="error-message">{{ $message }}</p>
        @enderror
        
        @if(session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif
    </form>
    
    <div class="auth-links">
        <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </div>
</div>
@endsection