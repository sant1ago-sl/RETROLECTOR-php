@extends('layouts.app')

@section('title', 'Gestión de Autores')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i>Gestión de Autores
                    </h1>
                    <p class="text-muted mb-0">Administra los autores de la biblioteca</p>
                </div>
                <div>
                    <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Autor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $autores->total() }}</h3>
                    <p class="text-muted mb-0">Total de Autores</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $autores->where('estado', 'activo')->count() }}</h3>
                    <p class="text-muted mb-0">Autores Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $autores->where('libros_count', '>', 0)->count() }}</h3>
                    <p class="text-muted mb-0">Con Libros</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $autores->where('libros_count', 0)->count() }}</h3>
                    <p class="text-muted mb-0">Sin Libros</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Autores -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Autores</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar autor..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Autor</th>
                            <th>Nacionalidad</th>
                            <th>Libros</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($autores as $autor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($autor->foto)
                                            <img src="{{ $autor->foto }}" 
                                                 alt="{{ $autor->nombre_completo }}" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $autor->nombre_completo }}</h6>
                                            @if($autor->fecha_nacimiento)
                                                <small class="text-muted">
                                                    {{ $autor->fecha_nacimiento->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($autor->nacionalidad)
                                        <span class="badge bg-info">{{ $autor->nacionalidad }}</span>
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($autor->libros_count > 0)
                                        <span class="badge bg-success">{{ $autor->libros_count }} libros</span>
                                    @else
                                        <span class="badge bg-warning">Sin libros</span>
                                    @endif
                                </td>
                                <td>
                                    @if($autor->estado === 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $autor->created_at->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.authors.show', $autor) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.authors.edit', $autor) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($autor->libros_count === 0)
                                            <form action="{{ route('admin.authors.delete', $autor) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este autor?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay autores registrados</p>
                                    <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Agregar Primer Autor
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($autores->hasPages())
            <div class="card-footer bg-white">
                {{ $autores->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection 