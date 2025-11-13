@extends('layouts.app')

@section('title', 'Crear Nueva Categoría')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nueva Categoría</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required maxlength="255">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" maxlength="500">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" name="color" id="color" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', '#007bff') }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icono" class="form-label">Icono (FontAwesome)</label>
                                    <input type="text" name="icono" id="icono" class="form-control @error('icono') is-invalid @enderror" value="{{ old('icono') }}" placeholder="fas fa-tag" maxlength="50">
                                    <div class="form-text">Ejemplo: fas fa-tag, fas fa-book, fas fa-heart</div>
                                    @error('icono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Crear Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Previsualización del color en tiempo real
document.getElementById('color').addEventListener('input', function(e) {
    const color = e.target.value;
    // Puedes agregar lógica de previsualización aquí
});
</script>
@endsection 