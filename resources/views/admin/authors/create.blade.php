@extends('layouts.app')

@section('title', 'Agregar Nuevo Autor')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus me-2"></i>Agregar Nuevo Autor
                    </h1>
                    <p class="text-muted mb-0">Completa la información del autor</p>
                </div>
                <div>
                    <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Información del Autor
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>Por favor, corrige los errores del formulario.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}" 
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Apellido -->
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">
                                    <i class="fas fa-user me-1"></i>Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="{{ old('apellido') }}" 
                                       required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Nacionalidad -->
                            <div class="col-md-6 mb-3">
                                <label for="nacionalidad" class="form-label">
                                    <i class="fas fa-flag me-1"></i>Nacionalidad
                                </label>
                                <input type="text" 
                                       class="form-control @error('nacionalidad') is-invalid @enderror" 
                                       id="nacionalidad" 
                                       name="nacionalidad" 
                                       value="{{ old('nacionalidad') }}">
                                @error('nacionalidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Fecha de Nacimiento
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       value="{{ old('fecha_nacimiento') }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Biografía -->
                        <div class="mb-3">
                            <label for="biografia" class="form-label">
                                <i class="fas fa-book-open me-1"></i>Biografía
                            </label>
                            <textarea class="form-control @error('biografia') is-invalid @enderror" 
                                      id="biografia" 
                                      name="biografia" 
                                      rows="4" 
                                      placeholder="Breve biografía del autor...">{{ old('biografia') }}</textarea>
                            @error('biografia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Máximo 1000 caracteres. <span id="charCount">0</span>/1000
                            </div>
                        </div>

                        <!-- Foto -->
                        <div class="mb-3">
                            <label for="foto" class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto del Autor
                            </label>
                            <input type="file" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   id="foto" 
                                   name="foto" 
                                   accept="image/*">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="estado" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>Estado
                            </label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado">
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Autor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const biografia = document.getElementById('biografia');
    const charCount = document.getElementById('charCount');
    if (biografia && charCount) {
        biografia.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            if (count > 1000) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
        charCount.textContent = biografia.value.length;
    }
    // Validación de archivo
    const foto = document.getElementById('foto');
    if (foto) {
        foto.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Máximo 2MB.');
                    this.value = '';
                }
            }
        });
    }
});
</script>
@endpush
@endsection 