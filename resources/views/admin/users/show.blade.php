@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-user me-3"></i>Detalles del Usuario
                    </h1>
                    <p class="lead text-muted">Información completa y actividad de {{ $usuario->nombre_completo }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Usuarios
                    </a>
                    <a href="{{ route('admin.users.edit', $usuario) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Usuario -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Información Personal
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="bg-gradient-primary rounded-circle mx-auto mb-4" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                    
                    <h4 class="mb-2">{{ $usuario->nombre_completo }}</h4>
                    <p class="text-muted mb-3">{{ $usuario->email }}</p>
                    
                    @switch($usuario->estado)
                        @case('activo')
                            <span class="badge bg-success fs-6 mb-3">Usuario Activo</span>
                            @break
                        @case('inactivo')
                            <span class="badge bg-warning fs-6 mb-3">Usuario Inactivo</span>
                            @break
                        @case('suspendido')
                            <span class="badge bg-danger fs-6 mb-3">Usuario Suspendido</span>
                            @break
                        @default
                            <span class="badge bg-secondary fs-6 mb-3">{{ ucfirst($usuario->estado) }}</span>
                    @endswitch

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="text-primary fw-bold fs-4">{{ $estadisticas['total_prestamos'] }}</div>
                            <small class="text-muted">Total Préstamos</small>
                        </div>
                        <div class="col-4">
                            <div class="text-success fw-bold fs-4">{{ $estadisticas['prestamos_activos'] }}</div>
                            <small class="text-muted">Activos</small>
                        </div>
                        <div class="col-4">
                            <div class="text-danger fw-bold fs-4">{{ $estadisticas['prestamos_vencidos'] }}</div>
                            <small class="text-muted">Vencidos</small>
                        </div>
                    </div>

                    <hr>

                    <div class="text-start">
                        <div class="mb-3">
                            <strong><i class="fas fa-phone me-2 text-muted"></i>Teléfono:</strong>
                            <p class="mb-0">{{ $usuario->telefono ?? 'No proporcionado' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>Dirección:</strong>
                            <p class="mb-0">{{ $usuario->direccion ?? 'No proporcionada' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-calendar me-2 text-muted"></i>Miembro desde:</strong>
                            <p class="mb-0">{{ $usuario->created_at->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($usuario->last_login_at)
                            <div class="mb-3">
                                <strong><i class="fas fa-clock me-2 text-muted"></i>Último acceso:</strong>
                                <p class="mb-0">{{ $usuario->last_login_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-language me-2 text-muted"></i>Idioma preferido:</strong>
                            <p class="mb-0">{{ $usuario->idioma_preferencia == 'es' ? 'Español' : 'English' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-palette me-2 text-muted"></i>Tema preferido:</strong>
                            <p class="mb-0">{{ $usuario->tema_preferencia == 'claro' ? 'Claro' : 'Oscuro' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas y Actividad -->
        <div class="col-lg-8">
            <!-- Estadísticas Generales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-gradient-success rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-book fa-2x text-white"></i>
                            </div>
                            <h4 class="text-success fw-bold">{{ $estadisticas['total_prestamos'] }}</h4>
                            <p class="text-muted mb-0">Préstamos Totales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-gradient-info rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                            <h4 class="text-info fw-bold">{{ $estadisticas['total_reservas'] }}</h4>
                            <p class="text-muted mb-0">Reservas Totales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-gradient-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-heart fa-2x text-white"></i>
                            </div>
                            <h4 class="text-warning fw-bold">{{ $estadisticas['total_favoritos'] }}</h4>
                            <p class="text-muted mb-0">Favoritos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-gradient-danger rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                            <h4 class="text-danger fw-bold">{{ $estadisticas['prestamos_vencidos'] }}</h4>
                            <p class="text-muted mb-0">Vencidos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Préstamos Activos -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake me-2"></i>Préstamos Activos
                    </h5>
                </div>
                <div class="card-body">
                    @if($usuario->prestamos->where('estado', 'prestado')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario->prestamos->where('estado', 'prestado') as $prestamo)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($prestamo->libro->imagen_portada)
                                                        <img src="{{ $prestamo->libro->imagen_portada }}" 
                                                             alt="{{ $prestamo->libro->titulo }}" 
                                                             class="me-3" 
                                                             style="width: 40px; height: 56px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 56px; border-radius: 4px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $prestamo->libro->titulo }}</div>
                                                        <small class="text-muted">{{ $prestamo->libro->autor->nombre_completo }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</td>
                                            <td>
                                                @if($prestamo->fecha_devolucion_esperada < now())
                                                    <span class="text-danger fw-bold">{{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}</span>
                                                @else
                                                    {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($prestamo->fecha_devolucion_esperada < now())
                                                    <span class="badge bg-danger">Vencido</span>
                                                @else
                                                    <span class="badge bg-success">Activo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm" onclick="marcarDevuelto({{ $prestamo->id }})">
                                                    <i class="fas fa-check me-1"></i>Marcar Devuelto
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tiene préstamos activos</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reservas Pendientes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Reservas Pendientes
                    </h5>
                </div>
                <div class="card-body">
                    @if($usuario->reservas->where('estado', 'pendiente')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Fecha Reserva</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario->reservas->where('estado', 'pendiente') as $reserva)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($reserva->libro->imagen_portada)
                                                        <img src="{{ $reserva->libro->imagen_portada }}" 
                                                             alt="{{ $reserva->libro->titulo }}" 
                                                             class="me-3" 
                                                             style="width: 40px; height: 56px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 56px; border-radius: 4px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $reserva->libro->titulo }}</div>
                                                        <small class="text-muted">{{ $reserva->libro->autor->nombre_completo }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $reserva->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning">Pendiente</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-success btn-sm" onclick="marcarDisponible({{ $reserva->id }})">
                                                    <i class="fas fa-check me-1"></i>Marcar Disponible
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tiene reservas pendientes</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Libros Favoritos -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-heart me-2"></i>Libros Favoritos
                    </h5>
                </div>
                <div class="card-body">
                    @if($usuario->favoritos->count() > 0)
                        <div class="row">
                            @foreach($usuario->favoritos->take(6) as $favorito)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-img-top text-center p-2" style="height: 120px; background: #f8f9fa;">
                                            @if($favorito->libro->imagen_portada)
                                                <img src="{{ $favorito->libro->imagen_portada }}" 
                                                     alt="{{ $favorito->libro->titulo }}" 
                                                     class="img-fluid h-100" 
                                                     style="object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-book fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-3">
                                            <h6 class="card-title mb-1">{{ Str::limit($favorito->libro->titulo, 30) }}</h6>
                                            <p class="text-muted small mb-2">{{ $favorito->libro->autor->nombre_completo }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-secondary">{{ $favorito->libro->categoria->nombre }}</span>
                                                <a href="{{ route('books.show', $favorito->libro) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($usuario->favoritos->count() > 6)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-warning">
                                    Ver todos los favoritos ({{ $usuario->favoritos->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tiene libros favoritos</p>
                        </div>
                    @endif
                </div>
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
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.table tbody tr {
    transition: background-color 0.3s ease;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endpush

@push('scripts')
<script>
function marcarDevuelto(prestamoId) {
    if (confirm('¿Estás seguro de que quieres marcar este préstamo como devuelto?')) {
        // Aquí se haría la petición AJAX para marcar como devuelto
        alert('Préstamo marcado como devuelto');
    }
}

function marcarDisponible(reservaId) {
    if (confirm('¿Estás seguro de que quieres marcar esta reserva como disponible?')) {
        // Aquí se haría la petición AJAX para marcar como disponible
        alert('Reserva marcada como disponible');
    }
}
</script>
@endpush
@endsection 