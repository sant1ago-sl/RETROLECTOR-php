@extends('layouts.app')

@section('title', 'Mis Préstamos')

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
                    <h1 class="display-6 fw-bold mb-2">Mis Préstamos</h1>
                    <p class="lead text-muted">Gestiona tus préstamos de libros</p>
                </div>
                <a href="{{ route('books.catalog') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Prestar Nuevo Libro
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
            @if($prestamos->count() > 0)
                <div class="card shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-book-reader me-2"></i>Historial de Préstamos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Autor</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamos as $prestamo)
                                        <tr class="animate__animated animate__fadeIn">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($prestamo->libro->imagen_portada)
                                                        <img src="{{ $prestamo->libro->imagen_portada }}" 
                                                             alt="{{ $prestamo->libro->titulo }}" 
                                                             class="me-3" 
                                                             style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 70px; border-radius: 4px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $prestamo->libro->titulo }}</h6>
                                                        <small class="text-muted">{{ $prestamo->libro->categoria->nombre }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $prestamo->libro->autor->nombre }} {{ $prestamo->libro->autor->apellido }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                @if($prestamo->estado === 'prestado')
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                                                    </span>
                                                    @if($prestamo->fecha_devolucion_esperada->isPast())
                                                        <br><small class="text-danger">¡Vencido!</small>
                                                    @elseif($prestamo->fecha_devolucion_esperada->diffInDays(now()) <= 3)
                                                        <br><small class="text-warning">Próximo a vencer</small>
                                                    @endif
                                                @elseif($prestamo->estado === 'devuelto')
                                                    <span class="badge bg-success">
                                                        {{ $prestamo->fecha_devolucion_real ? $prestamo->fecha_devolucion_real->format('d/m/Y') : 'Devuelto' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($prestamo->estado) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($prestamo->estado)
                                                    @case('prestado')
                                                        <span class="badge bg-success">Activo</span>
                                                        @break
                                                    @case('devuelto')
                                                        <span class="badge bg-info">Devuelto</span>
                                                        @break
                                                    @case('vencido')
                                                        <span class="badge bg-danger">Vencido</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($prestamo->estado) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($prestamo->estado === 'prestado')
                                                        @if($prestamo->libro->archivo_pdf)
                                                            <a href="{{ route('books.read', $prestamo->libro) }}" 
                                                               class="btn btn-outline-primary btn-sm" 
                                                               title="Leer libro"
                                                               target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                        
                                                        @if(!$prestamo->fecha_devolucion_esperada->isPast())
                                                            <form action="{{ route('user.renew', $prestamo) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-warning btn-sm" title="Renovar préstamo">
                                                                    <i class="fas fa-sync-alt"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        <form action="{{ route('user.return', $prestamo) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Devolver libro">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <a href="{{ route('books.show', $prestamo->libro) }}" 
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
                @if($prestamos->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $prestamos->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 animate__animated animate__fadeIn">
                    <div class="mb-4">
                        <i class="fas fa-book-reader fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No tienes préstamos aún</h4>
                    <p class="text-muted mb-4">Explora nuestro catálogo y presta tu primer libro</p>
                    <a href="{{ route('books.catalog') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Explorar Catálogo
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    @if($prestamos->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Estadísticas de Préstamos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-primary">{{ $prestamos->where('estado', 'prestado')->count() }}</h3>
                                    <p class="text-muted mb-0">Activos</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-success">{{ $prestamos->where('estado', 'devuelto')->count() }}</h3>
                                    <p class="text-muted mb-0">Devueltos</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h3 class="text-danger">{{ $prestamos->where('estado', 'vencido')->count() }}</h3>
                                    <p class="text-muted mb-0">Vencidos</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-info">{{ $prestamos->count() }}</h3>
                                <p class="text-muted mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmar acciones importantes
    const forms = document.querySelectorAll('form[action*="return"], form[action*="renew"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = this.action.includes('return') ? 'devolver' : 'renovar';
            if (!confirm(`¿Estás seguro de que quieres ${action} este libro?`)) {
                e.preventDefault();
            }
        });
    });
});

    function refreshLoans() {
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
    setInterval(refreshLoans, 30000);
</script>
@endpush
@endsection 