@extends('layouts.app')

@section('title', 'Panel Administrativo - Retrolector')

@section('content')
<div class="admin-login-bg d-flex align-items-center justify-content-center min-vh-100">
    <div class="admin-login-card">
        <div class="admin-login-header text-center mb-4">
            <div class="admin-logo-container mb-3">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2 class="admin-login-title">Panel Administrativo</h2>
            <p class="admin-login-subtitle">Acceso exclusivo para administradores</p>
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

        <!-- Formulario Admin -->
        <form method="POST" action="{{ route('admin.login') }}" class="admin-login-form">
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
            
            <button type="submit" class="btn btn-admin-primary btn-lg w-100 mb-3">
                <i class="fas fa-user-shield me-2"></i>
                Acceder al Panel
            </button>
        </form>

        <div class="admin-login-footer text-center mt-4">
            <p class="mb-0">
                <small class="text-muted">
                    ¿Eres usuario? 
                    <a href="{{ route('login') }}" class="user-link">Inicia sesión aquí</a>
                </small>
            </p>
        </div>
    </div>
</div>

<style>
.admin-login-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

[data-bs-theme="dark"] .admin-login-bg {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.admin-login-card {
    background: #fff;
    border-radius: 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    padding: 2.5rem 2rem;
    max-width: 420px;
    width: 100%;
    margin: 2rem 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

[data-bs-theme="dark"] .admin-login-card {
    background: rgba(52, 73, 94, 0.95);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.1);
}

.admin-login-header .admin-logo-container {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem auto;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.admin-login-header .admin-logo-container i {
    font-size: 2.5rem;
    color: #fff;
}

.admin-login-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
}

[data-bs-theme="dark"] .admin-login-title {
    color: #ecf0f1;
}

.admin-login-subtitle {
    color: #7f8c8d;
    font-size: 1rem;
    font-weight: 500;
}

[data-bs-theme="dark"] .admin-login-subtitle {
    color: #bdc3c7;
}

.form-label {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

[data-bs-theme="dark"] .form-label {
    color: #ecf0f1;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

[data-bs-theme="dark"] .form-control {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

[data-bs-theme="dark"] .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #667eea;
}

.btn-admin-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-admin-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.admin-login-footer .user-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.admin-login-footer .user-link:hover {
    color: #5a6fd8;
    text-decoration: underline;
}

[data-bs-theme="dark"] .admin-login-footer .user-link {
    color: #74b9ff;
}

[data-bs-theme="dark"] .admin-login-footer .user-link:hover {
    color: #0984e3;
}

.alert {
    border-radius: 12px;
    border: none;
}

.alert-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: #fff;
}

.invalid-feedback {
    color: #ff6b6b;
    font-weight: 500;
}

[data-bs-theme="dark"] .invalid-feedback {
    color: #fd79a8;
}
</style>
@endsection 