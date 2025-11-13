@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-users me-3"></i>Gestión de Usuarios
                    </h1>
                    <p class="lead text-muted">Administra todos los usuarios registrados en la biblioteca</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                    <form method="GET" action="{{ route('admin.users.index') }}" id="filtrosForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="busqueda" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                                       value="{{ request('busqueda') }}" placeholder="Nombre, email, teléfono...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos</option>
                                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ request('estado') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="idioma" class="form-label">Idioma</label>
                                <select class="form-select" id="idioma" name="idioma">
                                    <option value="">Todos</option>
                                    <option value="es" {{ request('idioma') == 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="en" {{ request('idioma') == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="orden" class="form-label">Ordenar por</label>
                                <select class="form-select" id="orden" name="orden">
                                    <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Fecha de registro</option>
                                    <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                                    <option value="prestamos_count" {{ request('orden') == 'prestamos_count' ? 'selected' : '' }}>Préstamos</option>
                                    <option value="last_login_at" {{ request('orden') == 'last_login_at' ? 'selected' : '' }}>Último acceso</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <h4 class="text-primary fw-bold">{{ $usuarios->total() }}</h4>
                    <p class="text-muted mb-0">Total de Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-success rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle fa-2x text-white"></i>
                    </div>
                    <h4 class="text-success fw-bold">{{ $usuarios->where('estado', 'activo')->count() }}</h4>
                    <p class="text-muted mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                    <h4 class="text-warning fw-bold">{{ $usuarios->where('estado', 'inactivo')->count() }}</h4>
                    <p class="text-muted mb-0">Usuarios Inactivos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="bg-gradient-danger rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-ban fa-2x text-white"></i>
                    </div>
                    <h4 class="text-danger fw-bold">{{ $usuarios->where('estado', 'suspendido')->count() }}</h4>
                    <p class="text-muted mb-0">Usuarios Suspendidos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Usuarios Registrados
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2">{{ $usuarios->total() }} usuarios</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="cambiarVista('table')">
                                <i class="fas fa-list"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="cambiarVista('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($usuarios->count() > 0)
                        <!-- Vista de Tabla -->
                        <div id="vistaTabla" class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Contacto</th>
                                        <th>Estado</th>
                                        <th>Actividad</th>
                                        <th>Préstamos</th>
                                        <th>Último Acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                        <tr class="animate__animated animate__fadeIn">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $usuario->nombre_completo }}</h6>
                                                        <small class="text-muted">Miembro desde {{ $usuario->created_at->format('M Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $usuario->email }}</div>
                                                    @if($usuario->telefono)
                                                        <small class="text-muted">{{ $usuario->telefono }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($usuario->estado)
                                                    @case('activo')
                                                        <span class="badge bg-success">Activo</span>
                                                        @break
                                                    @case('inactivo')
                                                        <span class="badge bg-warning">Inactivo</span>
                                                        @break
                                                    @case('suspendido')
                                                        <span class="badge bg-danger">Suspendido</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($usuario->estado) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-1">{{ $usuario->prestamos_count }}</span>
                                                    <span class="badge bg-info me-1">{{ $usuario->reservas_count }}</span>
                                                    <span class="badge bg-warning">{{ $usuario->favoritos_count }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-1">{{ $usuario->prestamos_count }}</span>
                                                    <span class="badge bg-secondary">{{ $usuario->reservas_count }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($usuario->last_login_at)
                                                    <div>
                                                        <div class="fw-bold">{{ $usuario->last_login_at->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $usuario->last_login_at->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Nunca</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.users.show', $usuario) }}" 
                                                       class="btn btn-outline-info btn-sm" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $usuario) }}" 
                                                       class="btn btn-outline-primary btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="confirmarEliminacion({{ $usuario->id }}, '{{ $usuario->nombre_completo }}')" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                                @foreach($usuarios as $usuario)
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp">
                                            <div class="card-body text-center">
                                                <div class="bg-gradient-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user fa-2x text-white"></i>
                                                </div>
                                                <h6 class="card-title mb-2">{{ $usuario->nombre_completo }}</h6>
                                                <p class="text-muted small mb-2">{{ $usuario->email }}</p>
                                                
                                                <div class="d-flex justify-content-center mb-3">
                                                    @switch($usuario->estado)
                                                        @case('activo')
                                                            <span class="badge bg-success">Activo</span>
                                                            @break
                                                        @case('inactivo')
                                                            <span class="badge bg-warning">Inactivo</span>
                                                            @break
                                                        @case('suspendido')
                                                            <span class="badge bg-danger">Suspendido</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($usuario->estado) }}</span>
                                                    @endswitch
                                                </div>
                                                
                                                <div class="row text-center mb-3">
                                                    <div class="col-4">
                                                        <div class="text-primary fw-bold">{{ $usuario->prestamos_count }}</div>
                                                        <small class="text-muted">Préstamos</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="text-info fw-bold">{{ $usuario->reservas_count }}</div>
                                                        <small class="text-muted">Reservas</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="text-warning fw-bold">{{ $usuario->favoritos_count }}</div>
                                                        <small class="text-muted">Favoritos</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.users.show', $usuario) }}" 
                                                           class="btn btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.users.edit', $usuario) }}" 
                                                           class="btn btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmarEliminacion({{ $usuario->id }}, '{{ $usuario->nombre_completo }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
                                        Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
                                    </small>
                                </div>
                                <div>
                                    {{ $usuarios->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">No se encontraron usuarios</h4>
                            <p class="text-muted mb-4">Intenta ajustar los filtros de búsqueda</p>
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
                <p>¿Estás seguro de que quieres eliminar al usuario <strong id="nombreUsuario"></strong>?</p>
                <p class="text-muted small">Esta acción no se puede deshacer y eliminará todos los datos asociados al usuario.</p>
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

function confirmarEliminacion(id, nombre) {
    document.getElementById('nombreUsuario').textContent = nombre;
    document.getElementById('formEliminacion').action = `/admin/users/${id}`;
    
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