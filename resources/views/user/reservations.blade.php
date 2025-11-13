@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Volver al Dashboard
        </a>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">Mis Reservas</h1>
                    <p class="lead text-muted">Gestiona tus reservas de libros</p>
                </div>
                <a href="{{ route('books.catalog') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Reservar Nuevo Libro
                </a>
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

    <div class="row">
        <div class="col-12">
            @if($reservas->count() > 0)
                <div class="card shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Historial de Reservas
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Autor</th>
                                        <th>Fecha Reserva</th>
                                        <th>Fecha Expiración</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservas as $reserva)
                                        <tr class="animate__animated animate__fadeIn">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($reserva->libro->imagen_portada)
                                                        <img src="{{ $reserva->libro->imagen_portada }}" 
                                                             alt="{{ $reserva->libro->titulo }}" 
                                                             class="me-3" 
                                                             style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 70px; border-radius: 4px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $reserva->libro->titulo }}</h6>
                                                        <small class="text-muted">{{ $reserva->libro->categoria->nombre }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $reserva->libro->autor->nombre }} {{ $reserva->libro->autor->apellido }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $reserva->fecha_reserva->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                @if($reserva->estado === 'pendiente')
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $reserva->fecha_expiracion->format('d/m/Y') }}
                                                    </span>
                                                    @if($reserva->fecha_expiracion->isPast())
                                                        <br><small class="text-danger">¡Expirada!</small>
                                                    @elseif($reserva->fecha_expiracion->diffInDays(now()) <= 1)
                                                        <br><small class="text-warning">Expira pronto</small>
                                                    @endif
                                                @elseif($reserva->estado === 'completada')
                                                    <span class="badge bg-success">Completada</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($reserva->estado) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($reserva->estado)
                                                    @case('pendiente')
                                                        <span class="badge bg-warning">Pendiente</span>
                                                        @break
                                                    @case('completada')
                                                        <span class="badge bg-success">Completada</span>
                                                        @break
                                                    @case('cancelada')
                                                        <span class="badge bg-secondary">Cancelada</span>
                                                        @break
                                                    @case('expirada')
                                                        <span class="badge bg-danger">Expirada</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($reserva->estado) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($reserva->estado === 'pendiente')
                                                        @if($reserva->libro->estado === 'disponible' && $reserva->libro->stock > 0)
                                                            <form action="{{ route('user.borrow', $reserva->libro) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-success btn-sm" title="Prestar libro">
                                                                    <i class="fas fa-book"></i> Prestar
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        @if(!$reserva->fecha_expiracion->isPast())
                                                            <form action="{{ route('user.cancel-reservation', $reserva) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancelar reserva">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                    
                                                    <a href="{{ route('books.show', $reserva->libro) }}" 
                                                       class="btn btn-outline-info btn-sm" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-info-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                @if($reservas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reservas->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 animate__animated animate__fadeIn">
                    <div class="mb-4">
                        <i class="fas fa-calendar-check fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No tienes reservas aún</h4>
                    <p class="text-muted mb-4">Reserva libros que no estén disponibles para ser notificado cuando lo estén</p>
                    <a href="{{ route('books.catalog') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Explorar Catálogo
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    @if($reservas->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Estadísticas de Reservas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-warning">{{ $reservas->where('estado', 'pendiente')->count() }}</h3>
                                    <p class="text-muted mb-0">Pendientes</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-success">{{ $reservas->where('estado', 'completada')->count() }}</h3>
                                    <p class="text-muted mb-0">Completadas</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-danger">{{ $reservas->where('estado', 'expirada')->count() }}</h3>
                                    <p class="text-muted mb-0">Expiradas</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-info">{{ $reservas->count() }}</h3>
                                <p class="text-muted mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información sobre reservas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información sobre Reservas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock text-warning me-2"></i>Duración de Reservas</h6>
                            <p class="text-muted">Las reservas tienen una duración de 7 días. Si el libro no está disponible en ese tiempo, la reserva expira automáticamente.</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-bell text-success me-2"></i>Notificaciones</h6>
                            <p class="text-muted">Recibirás una notificación cuando el libro reservado esté disponible para préstamo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmar cancelación de reservas
    const cancelForms = document.querySelectorAll('form[action*="cancel-reservation"]');
    cancelForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que quieres cancelar esta reserva?')) {
                e.preventDefault();
            }
        });
    });
});

    function refreshReservations() {
        fetch(window.location.href + '?ajax=1')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.table-responsive');
                if (newTable) {
                    document.querySelector('.table-responsive').innerHTML = newTable.innerHTML;
                }
            });
    }
    setInterval(refreshReservations, 30000);
</script>
@endpush
@endsection 