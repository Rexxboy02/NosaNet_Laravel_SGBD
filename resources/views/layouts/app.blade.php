<!DOCTYPE html>
<html lang="es" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NosaNet')</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @stack('styles')
</head>
<body>
    <header>
        <a href="{{ route('home') }}" style="font-size: 1.5rem; font-weight: 700;">NosaNet</a>
        <div>
            @if(auth_check())
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('messages.my') }}">Mis Posts</a>
                
                @if(is_professor())
                    <a href="{{ route('moderation.index') }}">Moderación</a>
                @endif
                
                <div class="profile-dropdown">
                    <a href="#" class="profile-btn">Perfil</a>
                    <div class="dropdown-content">
                        <div class="user-info">
                            <div><strong>{{ session('username') }}</strong></div>
                            <div>{{ session('email') }}</div>
                            <div>
                                <span class="user-role">
                                    {{ is_professor() ? 'Profesor' : 'Alumno' }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="logout-btn">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('register') }}">Registrarse</a>
                <a href="{{ route('login') }}">Iniciar Sesión</a>
            @endif
        </div>
    </header>
    
    <main>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Selector de Tema -->
    <div class="theme-toggle-container">
        <form method="post" action="{{ route('theme.toggle') }}" class="theme-toggle-form">
            @csrf
            <button type="submit" class="theme-toggle-btn" title="Cambiar tema">
                <span class="theme-icon">
                    {{ session('theme', 'light') === 'dark' ? '☀️' : '🌙' }}
                </span>
                <span class="theme-text">
                    {{ session('theme', 'light') === 'dark' ? 'Modo Claro' : 'Modo Oscuro' }}
                </span>
            </button>
        </form>
    </div>
    
    @stack('scripts')
</body>
</html>