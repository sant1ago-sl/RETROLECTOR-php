@extends('layouts.app')

@section('title', 'Gestionar Libros')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-books me-3"></i>Gestión de Libros
                    </h1>
                    <p class="lead text-muted">Administra el catálogo completo de la biblioteca</p>
                </div>
                <!-- Quitar el botón de agregar libro del header -->
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; min-width: 250px;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 show position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; min-width: 250px;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-triangle me-2"></i>Ocurrió un error. Por favor, revisa el formulario.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Filtros y Búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros y Búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.books.index') }}" id="filtrosForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="busqueda" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="{{ request('busqueda') }}" placeholder="Título, autor, ISBN...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Todas</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="autor" class="form-label">Autor</label>
                                <select class="form-select" id="autor" name="autor">
                                    <option value="">Todos</option>
                                    @foreach($autores as $autor)
                                        <option value="{{ $autor->id }}" {{ request('autor') == $autor->id ? 'selected' : '' }}>
                                            {{ $autor->nombre }} {{ $autor->apellido }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos</option>
                                    <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="prestado" {{ request('estado') == 'prestado' ? 'selected' : '' }}>Prestado</option>
                                    <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                                    <option value="perdido" {{ request('estado') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Avanzado</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="nuevo" id="nuevo" value="1" {{ request('nuevo') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nuevo">Nuevo</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sin_stock" id="sin_stock" value="1" {{ request('sin_stock') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sin_stock">Sin stock</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="solo_online" id="solo_online" value="1" {{ request('solo_online') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="solo_online">Solo online</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="solo_fisico" id="solo_fisico" value="1" {{ request('solo_fisico') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="solo_fisico">Solo físico</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="con_pdf" id="con_pdf" value="1" {{ request('con_pdf') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="con_pdf">Con PDF</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="fecha_inicio" class="form-label">Desde</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="fecha_fin" class="form-label">Hasta</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-2 mb-3 align-self-end">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-success rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-book fa-2x text-white"></i>
                    </div>
                    <h4 class="text-success fw-bold">{{ $total }}</h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-star fa-2x text-white"></i>
                    </div>
                    <h4 class="text-primary fw-bold">{{ $nuevos }}</h4>
                    <p class="text-muted mb-0">Nuevos (7 días)</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-danger rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle fa-2x text-white"></i>
                    </div>
                    <h4 class="text-danger fw-bold">{{ $sinStock }}</h4>
                    <p class="text-muted mb-0">Sin stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-info rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-globe fa-2x text-white"></i>
                    </div>
                    <h4 class="text-info fw-bold">{{ $soloOnline }}</h4>
                    <p class="text-muted mb-0">Solo online</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box fa-2x text-white"></i>
                    </div>
                    <h4 class="text-warning fw-bold">{{ $soloFisico }}</h4>
                    <p class="text-muted mb-0">Solo físico</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-secondary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-pdf fa-2x text-white"></i>
                    </div>
                    <h4 class="text-secondary fw-bold">{{ $conPDF }}</h4>
                    <p class="text-muted mb-0">Con PDF</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-end">
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle me-2"></i>Añadir libro
            </a>
        </div>
    </div>

    <!-- Lista de Libros -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Catálogo de Libros
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">{{ $libros->total() }} libros</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="cambiarVista('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="cambiarVista('table')">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($libros->count() > 0)
                        <!-- Vista de Tabla -->
                        <div id="vistaTabla" class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Autor</th>
                                        <th>Categoría</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Préstamos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($libros as $libro)
                                        <tr class="animate__animated animate__fadeIn">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($libro->imagen_portada)
                                                        <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="{{ $libro->titulo }}" class="me-3 rounded" style="width: 48px; height: 64px; object-fit: cover;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 64px;">
                                                            <i class="fas fa-book fa-lg text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $libro->titulo }}</div>
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            @php
                                                                $isNuevo = \Carbon\Carbon::parse($libro->created_at)->gt(now()->subDays(7));
                                                                $soloOnline = $libro->precio_compra_fisica <= 0 && $libro->precio_prestamo_fisico <= 0 && ($libro->precio_compra_online > 0 || $libro->precio_prestamo_online > 0);
                                                                $soloFisico = $libro->precio_compra_online <= 0 && $libro->precio_prestamo_online <= 0 && ($libro->precio_compra_fisica > 0 || $libro->precio_prestamo_fisico > 0);
                                                                $conPDF = !empty($libro->archivo_pdf);
                                                            @endphp
                                                            @if($isNuevo)
                                                                <span class="badge bg-success">Nuevo</span>
                                                            @endif
                                                            @if($libro->stock <= 0)
                                                                <span class="badge bg-danger">Sin stock</span>
                                                            @endif
                                                            @if($soloOnline)
                                                                <span class="badge bg-info">Solo online</span>
                                                            @endif
                                                            @if($soloFisico)
                                                                <span class="badge bg-warning text-dark">Solo físico</span>
                                                            @endif
                                                            @if($conPDF)
                                                                <span class="badge bg-primary">Con PDF</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $libro->autor->nombre ?? '-' }} {{ $libro->autor->apellido ?? '' }}</td>
                                            <td>{{ $libro->categoria->nombre ?? '-' }}</td>
                                            <td>{{ $libro->stock }}</td>
                                            <td>
                                                <span class="badge bg-{{ $libro->estado == 'disponible' ? 'success' : ($libro->estado == 'prestado' ? 'warning' : 'secondary') }}">{{ ucfirst($libro->estado) }}</span>
                                            </td>
                                            <td>{{ $libro->prestamos()->count() }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.books.edit', $libro->id) }}" class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                                    <a href="{{ route('books.read', $libro->id) }}" class="btn btn-info" title="Vista previa gratuita" target="_blank"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('books.show', $libro->id) }}" class="btn btn-secondary" title="Ver como usuario" target="_blank"><i class="fas fa-user"></i></a>
                                                    <form action="{{ route('admin.books.delete', $libro->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este libro?')"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Vista de Grid -->
                        <div id="vistaGrid" class="d-none p-4">
                            <div class="row">
                                @foreach($libros as $libro)
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp">
                                            <div class="card-img-top text-center p-3" style="height: 200px; background: #f8f9fa;">
                                                @if($libro->imagen_portada)
                                                    <img src="{{ $libro->imagen_portada }}" 
                                                         alt="{{ $libro->titulo }}" 
                                                         class="img-fluid h-100" 
                                                         style="object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center h-100">
                                                        <i class="fas fa-book fa-3x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title mb-2">{{ Str::limit($libro->titulo, 40) }}</h6>
                                                <p class="text-muted small mb-2">{{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="badge bg-secondary">{{ $libro->categoria->nombre }}</span>
                                                    <div class="d-flex gap-1">
                                                        @if($libro->stock <= 2 && $libro->stock > 0)
                                                            <span class="badge bg-warning text-dark">{{ $libro->stock }}</span>
                                                        @elseif($libro->stock == 0)
                                                            <span class="badge bg-danger">0</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $libro->stock }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="btn-group btn-group-sm">
                                                        @if(true)
                                                            <a href="{{ route('admin.books.edit', $libro->id) }}" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('books.show', $libro) }}" 
                                                               class="btn btn-outline-info" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="confirmarEliminacion({{ $libro->id }}, '{{ $libro->titulo }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        Mostrando {{ $libros->firstItem() }} a {{ $libros->lastItem() }} de {{ $libros->total() }} libros
                                    </small>
                                </div>
                                <div>
                                    {{ $libros->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">No se encontraron libros</h4>
                            <p class="text-muted mb-4">Intenta ajustar los filtros de búsqueda o agrega un nuevo libro</p>
                            <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Agregar Primer Libro
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalEliminacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar el libro <strong id="tituloLibro"></strong>?</p>
                <p class="text-muted small">Esta acción no se puede deshacer y eliminará todos los datos asociados al libro.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminacion" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}

.table tbody tr {
    transition: background-color 0.3s ease;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}

.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
function cambiarVista(tipo) {
    if (tipo === 'grid') {
        document.getElementById('vistaTabla').classList.add('d-none');
        document.getElementById('vistaGrid').classList.remove('d-none');
    } else {
        document.getElementById('vistaGrid').classList.add('d-none');
        document.getElementById('vistaTabla').classList.remove('d-none');
    }
}

function confirmarEliminacion(id, titulo) {
    document.getElementById('tituloLibro').textContent = titulo;
    document.getElementById('formEliminacion').action = `/admin/books/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEliminacion'));
    modal.show();
}

// Auto-submit del formulario de filtros cuando cambien los selects
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#filtrosForm select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filtrosForm').submit();
        });
    });

    // Búsqueda con debounce
    let timeout;
    const busquedaInput = document.getElementById('busqueda');
    busquedaInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            document.getElementById('filtrosForm').submit();
        }, 500);
    });
});
</script>
@endpush
@endsection 