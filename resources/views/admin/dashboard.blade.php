@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 fw-bold mb-2 text-gradient">
                        <i class="fas fa-cogs me-3"></i>Panel de Administración
                    </h1>
                    <p class="lead text-muted">Bienvenido, {{ Auth::user()->nombre }}. Aquí tienes una visión completa de tu biblioteca.</p>
                </div>
                <div class="text-end">
                    <div class="text-muted">
                        <i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
                    </div>
                    <small class="text-muted">Última actualización: {{ now()->diffForHumans() }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas del Sistema -->
    @if(count($alertas) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Alertas del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($alertas as $alerta)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="alert alert-{{ $alerta['tipo'] }} border-0 d-flex align-items-center">
                                        <i class="{{ $alerta['icono'] }} me-3 fa-lg"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $alerta['titulo'] }}</h6>
                                            <p class="mb-2 small">{{ $alerta['mensaje'] }}</p>
                                            <a href="{{ $alerta['accion'] }}" class="btn btn-sm btn-outline-{{ $alerta['tipo'] }}">
                                                Ver detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient-primary rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($stats['total_usuarios']) }}</h2>
                            <p class="text-muted mb-0">Usuarios Registrados</p>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-primary" style="width: 85%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient-success rounded-circle p-3 me-3">
                            <i class="fas fa-book fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($stats['total_libros']) }}</h2>
                            <p class="text-muted mb-0">Libros en Catálogo</p>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-success" style="width: 92%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient-info rounded-circle p-3 me-3">
                            <i class="fas fa-handshake fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($stats['prestamos_activos']) }}</h2>
                            <p class="text-muted mb-0">Préstamos Activos</p>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-info" style="width: 78%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient-warning rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-check fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($stats['reservas_pendientes']) }}</h2>
                            <p class="text-muted mb-0">Reservas Pendientes</p>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-warning" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de estadísticas rápidas del admin -->
    @if(Auth::user()->isAdmin())
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-cog me-2"></i>Tu Actividad como Administrador
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <h4 class="text-primary fw-bold">{{ $stats['libros_creados_por_mi'] ?? 0 }}</h4>
                            <small class="text-muted">Libros creados por ti</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h4 class="text-success fw-bold">{{ $stats['prestamos_mis_libros'] ?? 0 }}</h4>
                            <small class="text-muted">Préstamos de tus libros</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h4 class="text-info fw-bold">{{ $stats['ventas_mis_libros'] ?? 0 }}</h4>
                            <small class="text-muted">Ventas de tus libros</small>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Sugerencia:</strong> Agrega portadas atractivas y descripciones completas para que tus libros destaquen más en el catálogo.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gráficos y Estadísticas Detalladas -->
    <div class="row mb-4">
        <!-- Gráfico de Préstamos por Mes -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Préstamos por Mes (Últimos 6 meses)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="prestamosChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Estadísticas Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-primary fw-bold">{{ number_format($stats['libros_disponibles']) }}</h4>
                                <small class="text-muted">Libros Disponibles</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-danger fw-bold">{{ number_format($stats['prestamos_vencidos']) }}</h4>
                            <small class="text-muted">Préstamos Vencidos</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-info fw-bold">{{ number_format($stats['libros_prestados']) }}</h4>
                                <small class="text-muted">Libros Prestados</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-warning fw-bold">{{ number_format($stats['resenas_pendientes']) }}</h4>
                            <small class="text-muted">Reseñas Pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row">
        <!-- Libros Más Populares -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Libros Más Populares
                    </h5>
                </div>
                <div class="card-body">
                    @if($librosPopulares->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($librosPopulares as $libro)
                                @php
                                    $isUrl = $libro->imagen_portada && (str_starts_with($libro->imagen_portada, 'http://') || str_starts_with($libro->imagen_portada, 'https://'));
                                @endphp
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        @if($libro->imagen_portada)
                                            <img src="{{ $isUrl ? $libro->imagen_portada : asset('storage/' . $libro->imagen_portada) }}"
                                                 alt="{{ $libro->titulo }}"
                                                 class="me-3"
                                                 style="width: 50px; height: 70px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 70px; border-radius: 8px;">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $libro->titulo }}</h6>
                                            <p class="text-muted mb-1 small">{{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">{{ $libro->prestamos_count }} préstamos</span>
                                                <span class="badge bg-secondary">{{ $libro->categoria->nombre }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('admin.books.edit', $libro) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de popularidad aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usuarios Más Activos -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Usuarios Más Activos
                    </h5>
                </div>
                <div class="card-body">
                    @if($usuariosActivos->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($usuariosActivos as $usuario)
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $usuario->nombre_completo }}</h6>
                                            <p class="text-muted mb-1 small">{{ $usuario->email }}</p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">{{ $usuario->prestamos_count }} préstamos</span>
                                                <small class="text-muted">Miembro desde {{ $usuario->created_at->format('M Y') }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('admin.users.show', $usuario) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de actividad aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Préstamos Recientes y Acciones Rápidas -->
    <div class="row">
        <!-- Préstamos Recientes -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Préstamos Recientes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Libro</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestamosRecientes as $prestamo)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-gradient-primary rounded-circle p-1 me-2">
                                                    <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $prestamo->usuario->nombre_completo }}</div>
                                                    <small class="text-muted">{{ $prestamo->usuario->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $prestamo->libro->titulo }}</div>
                                                <small class="text-muted">{{ $prestamo->libro->autor->nombre_completo }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $prestamo->fecha_prestamo->diffForHumans() }}</small>
                                            </div>
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
                                            <a href="{{ route('admin.loans.show', $prestamo) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.books.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Agregar Nuevo Libro
                        </a>
                        <a href="{{ route('admin.authors.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Agregar Nuevo Autor
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>Gestionar Usuarios
                        </a>
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-handshake me-2"></i>Ver Préstamos
                        </a>
                        <a href="{{ route('admin.reviews.pending') }}" class="btn btn-outline-warning">
                            <i class="fas fa-star me-2"></i>Moderar Reseñas
                        </a>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-bell me-2"></i>Gestionar Notificaciones
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark">
                            <i class="fas fa-tags me-2"></i>Gestionar Categorías
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.text-gradient {
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #667eea, #764ba2) !important;
}

.bg-gradient-success {
    background: linear-gradient(45deg, #f093fb, #f5576c) !important;
}

.bg-gradient-info {
    background: linear-gradient(45deg, #4facfe, #00f2fe) !important;
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #43e97b, #38f9d7) !important;
}

.progress-bar {
    transition: width 1s ease-in-out;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}

.list-group-item {
    transition: background-color 0.3s ease;
}

.list-group-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de préstamos por mes
    const ctx = document.getElementById('prestamosChart').getContext('2d');
    
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const datos = @json($prestamosPorMes);
    
    // Preparar datos para el gráfico
    const datosGrafico = new Array(12).fill(0);
    datos.forEach(item => {
        datosGrafico[item.mes - 1] = item.total;
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Préstamos',
                data: datosGrafico,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#667eea'
                }
            }
        }
    });

    // Animaciones de contadores
    const counters = document.querySelectorAll('h2');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/,/g, ''));
        const increment = target / 100;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    });
});
</script>
@endpush
@endsection 