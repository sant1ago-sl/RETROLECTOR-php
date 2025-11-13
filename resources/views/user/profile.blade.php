@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Volver al Dashboard
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
                    <i class="fas fa-user me-2"></i> <span>Mi Perfil</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.update-profile') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', auth()->user()->nombre) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="{{ old('apellido', auth()->user()->apellido) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required readonly>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion', auth()->user()->direccion) }}">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="idioma_preferencia" class="form-label">Idioma preferido</label>
                                <select class="form-select" id="idioma_preferencia" name="idioma_preferencia">
                                    <option value="es" {{ auth()->user()->idioma_preferencia == 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="en" {{ auth()->user()->idioma_preferencia == 'en' ? 'selected' : '' }}>Inglés</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tema_preferencia" class="form-label">Tema</label>
                                <select class="form-select" id="tema_preferencia" name="tema_preferencia">
                                    <option value="light" {{ auth()->user()->tema_preferencia == 'light' ? 'selected' : '' }}>Claro</option>
                                    <option value="dark" {{ auth()->user()->tema_preferencia == 'dark' ? 'selected' : '' }}>Oscuro</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-secondary text-white d-flex align-items-center">
                    <i class="fas fa-key me-2"></i> <span>Cambiar Contraseña</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.change-password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-secondary"><i class="fas fa-key me-2"></i>Cambiar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    transition: transform 0.2s;
}

.stat-item:hover {
    transform: translateY(-2px);
}

.stat-item h3 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    font-weight: 600;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    padding: 0.75rem 0;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection 