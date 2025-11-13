@extends('layouts.app')
@section('title', 'Editar Libro')
@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Editar Libro</h2>
    <form method="POST" action="{{ route('admin.books.update', $libro) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="titulo" class="form-label">Título *</label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $libro->titulo) }}" required>
                @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn', $libro->isbn) }}">
                @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="sinopsis" class="form-label">Sinopsis</label>
            <textarea class="form-control @error('sinopsis') is-invalid @enderror" id="sinopsis" name="sinopsis" rows="4">{{ old('sinopsis', $libro->sinopsis) }}</textarea>
            @error('sinopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="anio_publicacion" class="form-label">Año de Publicación</label>
                <input type="number" class="form-control @error('anio_publicacion') is-invalid @enderror" id="anio_publicacion" name="anio_publicacion" value="{{ old('anio_publicacion', $libro->anio_publicacion) }}" min="1800" max="{{ date('Y') + 1 }}">
                @error('anio_publicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="editorial" class="form-label">Editorial</label>
                <input type="text" class="form-control @error('editorial') is-invalid @enderror" id="editorial" name="editorial" value="{{ old('editorial', $libro->editorial) }}">
                @error('editorial')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="paginas" class="form-label">Número de Páginas</label>
                <input type="number" class="form-control @error('paginas') is-invalid @enderror" id="paginas" name="paginas" value="{{ old('paginas', $libro->paginas) }}" min="1">
                @error('paginas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="idioma" class="form-label">Idioma</label>
                <input type="text" class="form-control @error('idioma') is-invalid @enderror" id="idioma" name="idioma" value="{{ old('idioma', $libro->idioma) }}">
                @error('idioma')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $libro->ubicacion) }}">
                @error('ubicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="autor_id" class="form-label">Autor</label>
                <select class="form-select @error('autor_id') is-invalid @enderror" id="autor_id" name="autor_id" required>
                    @foreach($autores as $autor)
                        <option value="{{ $autor->id }}" {{ old('autor_id', $libro->autor_id) == $autor->id ? 'selected' : '' }}>{{ $autor->nombre_completo }}</option>
                    @endforeach
                </select>
                @error('autor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id" required>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $libro->categoria_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="imagen_portada" class="form-label">Imagen de Portada</label>
                <input type="file" class="form-control @error('imagen_portada') is-invalid @enderror" id="imagen_portada" name="imagen_portada">
                @if($libro->imagen_portada)
                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="Portada" class="img-thumbnail mt-2" style="max-width: 120px;">
                @endif
                @error('imagen_portada')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="archivo_pdf" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf" name="archivo_pdf">
                @if($libro->archivo_pdf)
                    <a href="{{ asset('storage/' . $libro->archivo_pdf) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">Ver PDF actual</a>
                @endif
                @error('archivo_pdf')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection 