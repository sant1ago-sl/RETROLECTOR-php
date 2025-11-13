@extends('layouts.app')

@section('title', 'Iniciar Sesión - Retrolector')

@section('content')
<div class="login-bg-library d-flex align-items-center justify-content-center min-vh-100">
    <div class="login-card-library">
        <div class="login-header text-center mb-4">
            <div class="logo-container mb-3">
                <i class="fas fa-book-reader"></i>
            </div>
            <h2 class="login-title">Bienvenido a Retrolector</h2>
            <p class="login-subtitle">Accede a tu biblioteca digital</p>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger animate__animated animate__shakeX">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('success'))
            <script>Swal.fire('¡Éxito!', '{{ session('success') }}', 'success');</script>
        @endif
        
        @if(session('error'))
            <script>Swal.fire('Error', '{{ session('error') }}', 'error');</script>
        @endif

        <!-- Formulario Cliente -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            <div class="form-group mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Correo Electrónico
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña
                </label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Recordar mi sesión
                </label>
            </div>
            
            <button type="submit" class="btn btn-library-primary btn-lg w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>
                Iniciar Sesión
            </button>
        </form>

        <div class="login-footer text-center mt-4">
            <p class="mb-2">
                ¿No tienes cuenta? 
                <a href="{{ route('register') }}" class="register-link">Regístrate aquí</a>
            </p>
            <p class="mb-0">
                <small class="text-muted">
                    ¿Eres administrador? 
                    <a href="{{ route('admin.login') }}" class="admin-link">Accede aquí</a>
                </small>
            </p>
        </div>
    </div>
</div>
<style>
.login-bg-library {
    min-height: 100vh;
    background: #f8f6f1;
    display: flex;
    align-items: center;
    justify-content: center;
}
[data-bs-theme="dark"] .login-bg-library {
    background: #232526;
}
.login-card-library {
    background: #fff;
    border-radius: 2rem;
    box-shadow: 0 8px 32px rgba(52, 52, 52, 0.08);
    padding: 2.5rem 2rem;
    max-width: 420px;
    width: 100%;
    margin: 2rem 0;
    border: 1px solid #f3f1e7;
    transition: background 0.3s, color 0.3s;
}
[data-bs-theme="dark"] .login-card-library {
    background: #353b48;
    color: #fff;
    border-color: #232526;
}
.login-header .logo-container {
    width: 70px;
    height: 70px;
    background: linear-gradient(90deg, #b97a56 0%, #a67c52 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem auto;
    box-shadow: 0 4px 16px rgba(185,122,86,0.12);
}
.login-header .logo-container i {
    font-size: 2rem;
    color: #fff;
}
.login-title {
    color: #3e2c18;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
[data-bs-theme="dark"] .login-title {
    color: #f3e9d2;
}
.login-subtitle {
    color: #7f8c8d;
    font-size: 1rem;
}
[data-bs-theme="dark"] .login-subtitle {
    color: #bfc9d1;
}
.login-tabs .nav-pills {
    background: #f3f1e7;
    border-radius: 15px;
    padding: 5px;
}
[data-bs-theme="dark"] .login-tabs .nav-pills {
    background: #232526;
}
.login-tabs .nav-link {
    border-radius: 10px;
    color: #7f8c8d;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    padding: 0.75rem 1.5rem;
}
.login-tabs .nav-link.active {
    background: linear-gradient(90deg, #b97a56 0%, #a67c52 100%);
    color: #fff;
    box-shadow: 0 5px 15px rgba(185,122,86,0.12);
}
.login-tabs .nav-link:hover:not(.active) {
    background: #f3f1e7;
    color: #b97a56;
}
[data-bs-theme="dark"] .login-tabs .nav-link:hover:not(.active) {
    background: #353b48;
    color: #f3e9d2;
}
.form-label {
    color: #3e2c18;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
[data-bs-theme="dark"] .form-label {
    color: #f3e9d2;
}
.form-control {
    border: 2px solid #ecf0f1;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}
[data-bs-theme="dark"] .form-control {
    background: #232526;
    border-color: #414345;
    color: #fff;
}
.form-control:focus {
    border-color: #b97a56;
    box-shadow: 0 0 0 0.2rem rgba(185,122,86,0.15);
    background: #fff;
}
[data-bs-theme="dark"] .form-control:focus {
    background: #353b48;
    border-color: #b97a56;
}
.form-check-input:checked {
    background-color: #b97a56;
    border-color: #b97a56;
}
.btn-library-primary {
    background: linear-gradient(90deg, #b97a56 0%, #a67c52 100%);
    color: #fff;
    border: none;
    border-radius: 30px;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(185,122,86,0.08);
    transition: background 0.2s, box-shadow 0.2s;
}
.btn-library-primary:hover {
    background: linear-gradient(90deg, #a67c52 0%, #b97a56 100%);
    color: #fff;
    box-shadow: 0 8px 24px rgba(185,122,86,0.18);
}
.register-link {
    color: #b97a56;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.register-link:hover {
    color: #a67c52;
    text-decoration: underline;
}
.login-footer {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #ecf0f1;
}
[data-bs-theme="dark"] .login-footer {
    border-top-color: #414345;
}
@media (max-width: 768px) {
    .login-card-library {
        padding: 2rem 1rem;
        margin: 1rem;
    }
    .login-title {
        font-size: 1.5rem;
    }
    .logo-container {
        width: 60px;
        height: 60px;
    }
    .logo-container i {
        font-size: 1.5rem;
    }
}
</style>
@endsection 