@extends('layouts.app')
@section('title', 'Detalle del Libro')
@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-book me-2"></i>Detalle del Libro</h2>
    <div class="row">
        <div class="col-md-4">
            @if($libro->imagen_portada)
                <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="Portada" class="img-fluid rounded shadow-sm mb-3">
            @else
                <div class="bg-light text-center py-5 rounded">Sin imagen</div>
            @endif
            @if($libro->archivo_pdf)
                <a href="{{ asset('storage/' . $libro->archivo_pdf) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">Ver PDF</a>
            @endif
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr><th>Título</th><td>{{ $libro->titulo }}</td></tr>
                <tr><th>ISBN</th><td>{{ $libro->isbn }}</td></tr>
                <tr><th>Autor</th><td>{{ $libro->autor->nombre_completo ?? '-' }}</td></tr>
                <tr><th>Categoría</th><td>{{ $libro->categoria->nombre ?? '-' }}</td></tr>
                <tr><th>Año de Publicación</th><td>{{ $libro->anio_publicacion }}</td></tr>
                <tr><th>Editorial</th><td>{{ $libro->editorial }}</td></tr>
                <tr><th>Páginas</th><td>{{ $libro->paginas }}</td></tr>
                <tr><th>Idioma</th><td>{{ $libro->idioma }}</td></tr>
                <tr><th>Ubicación</th><td>{{ $libro->ubicacion }}</td></tr>
                <tr><th>Estado</th><td>{{ ucfirst($libro->estado) }}</td></tr>
                <tr><th>Stock</th><td>{{ $libro->stock }}</td></tr>
                <tr><th>Sinopsis</th><td>{{ $libro->sinopsis }}</td></tr>
            </table>
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.books.edit', $libro) }}" class="btn btn-warning me-2"><i class="fas fa-edit me-1"></i>Editar</a>
                <form method="POST" action="{{ route('admin.books.destroy', $libro) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este libro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 