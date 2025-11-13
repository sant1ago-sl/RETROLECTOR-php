@extends('layouts.app')

@section('title', 'Agregar Libro')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Agregar Nuevo Libro
                    </h1>
                    <p class="text-muted mb-0">Completa la información del libro</p>
                </div>
                <div>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Información del Libro
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="titulo" class="form-label"><i class="fas fa-heading me-1"></i> Título *</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required placeholder="Ej: El nombre del viento">
                                @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="isbn" class="form-label"><i class="fas fa-barcode me-1"></i> ISBN</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn') }}" placeholder="Ej: 978-84-123456-7-8">
                                @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="autor_nombre_completo" class="form-label"><i class="fas fa-user-edit me-1"></i> Autor *</label>
                                <input type="text" class="form-control @error('autor_nombre_completo') is-invalid @enderror" id="autor_nombre_completo" name="autor_nombre_completo" value="{{ old('autor_nombre_completo') }}" required placeholder="Ej: Patrick Rothfuss">
                                <div class="form-text">Escribe el nombre completo del autor. Si no existe, se creará automáticamente.</div>
                                @error('autor_nombre_completo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="categoria_id" class="form-label"><i class="fas fa-tag me-1"></i> Categoría *</label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3 mt-2">
                            <label for="sinopsis" class="form-label"><i class="fas fa-align-left me-1"></i> Sinopsis</label>
                            <textarea class="form-control @error('sinopsis') is-invalid @enderror" id="sinopsis" name="sinopsis" rows="3" placeholder="Breve descripción del libro">{{ old('sinopsis') }}</textarea>
                            @error('sinopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="anio_publicacion" class="form-label"><i class="fas fa-calendar-alt me-1"></i> Año</label>
                                <input type="number" class="form-control @error('anio_publicacion') is-invalid @enderror" id="anio_publicacion" name="anio_publicacion" value="{{ old('anio_publicacion') }}" min="1800" max="{{ date('Y') + 1 }}" placeholder="Ej: 2020">
                                @error('anio_publicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="editorial" class="form-label"><i class="fas fa-building me-1"></i> Editorial</label>
                                <input type="text" class="form-control @error('editorial') is-invalid @enderror" id="editorial" name="editorial" value="{{ old('editorial') }}" placeholder="Ej: Plaza & Janés">
                                @error('editorial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="paginas" class="form-label"><i class="fas fa-file-alt me-1"></i> Páginas</label>
                                <input type="number" class="form-control @error('paginas') is-invalid @enderror" id="paginas" name="paginas" value="{{ old('paginas') }}" min="1" placeholder="Ej: 500">
                                @error('paginas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="idioma" class="form-label"><i class="fas fa-language me-1"></i> Idioma</label>
                                <input type="text" class="form-control @error('idioma') is-invalid @enderror" id="idioma" name="idioma" value="{{ old('idioma', 'Español') }}" placeholder="Ej: Español">
                                @error('idioma')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="ubicacion" class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Ubicación</label>
                                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: Estante A, Nivel 2">
                                @error('ubicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="stock" class="form-label"><i class="fas fa-boxes me-1"></i> Stock *</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 1) }}" min="0" required>
                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="imagen_portada" class="form-label"><i class="fas fa-image me-1"></i> Imagen de Portada</label>
                                <input type="file" class="form-control @error('imagen_portada') is-invalid @enderror" id="imagen_portada" name="imagen_portada" accept="image/*">
                                <div class="form-text">Formatos: JPG, PNG, GIF. Máx 2MB.</div>
                                @error('imagen_portada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3 mt-2">
                            <label for="archivo_pdf" class="form-label"><i class="fas fa-file-pdf me-1"></i> PDF del libro (opcional)</label>
                            <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                            <div class="form-text">Solo PDF. Máx 20MB.</div>
                            @error('archivo_pdf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Guardar Libro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
                        
                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                       id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ISBN -->
                            <div class="col-md-4 mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn') }}">
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Autor y Categoría -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="autor_id" class="form-label">Autor *</label>
                                <select class="form-select @error('autor_id') is-invalid @enderror" 
                                        id="autor_id" name="autor_id" required onchange="toggleAutorPersonalizado(this.value)">
                                    <option value="">Seleccionar autor</option>
                                    @foreach($autores as $autor)
                                        <option value="{{ $autor->id }}" {{ old('autor_id') == $autor->id ? 'selected' : '' }}>
                                            {{ $autor->nombre }} {{ $autor->apellido }}
                                        </option>
                                    @endforeach
                                    <option value="nuevo">Nuevo autor...</option>
                                </select>
                                @error('autor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <!-- Autor personalizado -->
                                <div id="autorPersonalizado" style="display: none; margin-top: 1rem;">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control mb-2" name="autor_nombre" placeholder="Nombre del autor" value="{{ old('autor_nombre') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control mb-2" name="autor_apellido" placeholder="Apellido del autor" value="{{ old('autor_apellido') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="categoria_id" class="form-label">Categoría *</label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                        id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sinopsis -->
                        <div class="mb-3">
                            <label for="sinopsis" class="form-label">Sinopsis</label>
                            <textarea class="form-control @error('sinopsis') is-invalid @enderror" 
                                      id="sinopsis" name="sinopsis" rows="4">{{ old('sinopsis') }}</textarea>
                            @error('sinopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información de publicación -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="anio_publicacion" class="form-label">Año de Publicación</label>
                                <input type="number" class="form-control @error('anio_publicacion') is-invalid @enderror" 
                                       id="anio_publicacion" name="anio_publicacion" 
                                       value="{{ old('anio_publicacion') }}" min="1800" max="{{ date('Y') + 1 }}">
                                @error('anio_publicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="editorial" class="form-label">Editorial</label>
                                <input type="text" class="form-control @error('editorial') is-invalid @enderror" 
                                       id="editorial" name="editorial" value="{{ old('editorial') }}">
                                @error('editorial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="paginas" class="form-label">Número de Páginas</label>
                                <input type="number" class="form-control @error('paginas') is-invalid @enderror" 
                                       id="paginas" name="paginas" value="{{ old('paginas') }}" min="1">
                                @error('paginas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Idioma y ubicación -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="idioma" class="form-label">Idioma</label>
                                <input type="text" class="form-control @error('idioma') is-invalid @enderror" 
                                       id="idioma" name="idioma" value="{{ old('idioma', 'Español') }}">
                                @error('idioma')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" 
                                       id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" 
                                       placeholder="Ej: Estante A, Nivel 2">
                                @error('ubicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Stock *</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                       id="stock" name="stock" value="{{ old('stock', 1) }}" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Imagen de portada -->
                        <div class="mb-3">
                            <label for="imagen_portada" class="form-label">Imagen de Portada</label>
                            <input type="file" class="form-control @error('imagen_portada') is-invalid @enderror" 
                                   id="imagen_portada" name="imagen_portada" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</div>
                            @error('imagen_portada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vista previa de imagen -->
                        <div class="mb-3" id="imagePreview" style="display: none;">
                            <label class="form-label">Vista Previa</label>
                            <div class="text-center">
                                <img id="preview" src="" alt="Vista previa" 
                                     class="img-thumbnail" style="max-width: 200px; max-height: 300px;">
                            </div>
                        </div>

                        <!-- Subida de PDF -->
                        <div class="mb-3">
                            <label for="archivo_pdf" class="form-label">Archivo PDF del libro (opcional)</label>
                            <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                            <div class="form-text">Solo PDF. Máximo 20MB.</div>
                            @error('archivo_pdf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="pdfPreview" class="mt-2 text-success" style="display:none;"></div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Guardar Libro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Vista previa de imagen
document.getElementById('imagen_portada').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewDiv.style.display = 'none';
    }
});

function toggleAutorPersonalizado(val) {
    const div = document.getElementById('autorPersonalizado');
    if (val === 'nuevo') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
// Mostrar nombre del PDF seleccionado
const pdfInput = document.getElementById('archivo_pdf');
if (pdfInput) {
    pdfInput.addEventListener('change', function() {
        const preview = document.getElementById('pdfPreview');
        if (this.files && this.files[0]) {
            preview.textContent = 'Archivo seleccionado: ' + this.files[0].name;
            preview.style.display = 'block';
        } else {
            preview.textContent = '';
            preview.style.display = 'none';
        }
    });
}
// Mostrar campos personalizados si ya venía seleccionado por validación
window.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('autor_id').value === 'nuevo') {
        toggleAutorPersonalizado('nuevo');
    }
});
</script>
@endsection 