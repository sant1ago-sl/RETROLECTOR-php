@extends('layouts.app')

@section('title', 'Panel de Usuario')

@section('content')
<div class="container-fluid py-5 animate__animated animate__fadeIn">
    <div class="row">
        <!-- Menú lateral de accesos rápidos -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('books.catalog') }}" method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar libros, autores...">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <ul class="nav flex-column nav-pills gap-2">
                        <li class="nav-item"><a href="{{ route('user.dashboard') }}" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                        <li class="nav-item"><a href="{{ route('user.profile') }}" class="nav-link"><i class="fas fa-user me-2"></i> Mi Perfil</a></li>
                        <li class="nav-item"><a href="{{ route('user.loans') }}" class="nav-link"><i class="fas fa-book me-2"></i> Mis Préstamos</a></li>
                        <li class="nav-item"><a href="{{ route('user.reservations') }}" class="nav-link"><i class="fas fa-calendar-check me-2"></i> Mis Reservas</a></li>
                        <li class="nav-item"><a href="{{ route('user.favorites') }}" class="nav-link"><i class="fas fa-heart me-2"></i> Mis Favoritos</a></li>
                        <li class="nav-item"><a href="{{ route('user.history') }}" class="nav-link"><i class="fas fa-history me-2"></i> Historial</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Panel principal -->
        <div class="col-lg-9">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card shadow border-0 text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-heart fa-2x mb-2 text-danger"></i>
                            <h4 class="fw-bold" style="font-size:2.2rem;">{{ $favoritos->count() }}</h4>
                            <div class="text-muted">Favoritos</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-book-reader fa-2x mb-2 text-success"></i>
                            <h4 class="fw-bold" style="font-size:2.2rem;">{{ $prestamos_recientes->count() }}</h4>
                            <div class="text-muted">Préstamos recientes</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-shopping-cart fa-2x mb-2 text-warning"></i>
                            <h4 class="fw-bold" style="font-size:2.2rem;">{{ $stats['total_compras'] }}</h4>
                            <div class="text-muted">Compras totales</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-bell fa-2x mb-2 text-info"></i>
                            <h4 class="fw-bold" style="font-size:2.2rem;">{{ $notificaciones->count() }}</h4>
                            <div class="text-muted">Notificaciones</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
                            <i class="fas fa-book-reader me-2"></i> <span>Préstamos Recientes</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="prestamos-list">
                                @foreach($prestamos_recientes as $prestamo)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $prestamo->libro->titulo }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</span>
                                            <span class="badge bg-{{ $prestamo->estado == 'prestado' ? 'success' : 'secondary' }} ms-2">{{ ucfirst($prestamo->estado) }}</span>
                                            <div class="mt-1">
                                                <span class="badge bg-info"><i class="fas fa-map-marker-alt me-1"></i>{{ $prestamo->libro->ubicacion ?? 'Sin ubicación' }}</span>
                                            </div>
                                        </div>
                                        @if($prestamo->libro->archivo_pdf)
                                            <a href="{{ route('books.read', $prestamo->libro) }}" class="btn btn-outline-primary btn-sm" target="_blank"><i class="fas fa-eye"></i> Leer</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-gradient-warning text-white d-flex align-items-center">
                            <i class="fas fa-bookmark me-2"></i> <span>Reservas Activas</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="reservas-list">
                                @foreach($reservas_activas as $reserva)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $reserva->libro->titulo }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $reserva->fecha_reserva->format('d/m/Y') }}</span>
                                            <span class="badge bg-warning ms-2">Pendiente</span>
                                        </div>
                                        <a href="{{ route('books.show', $reserva->libro) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-gradient-success text-white d-flex align-items-center">
                            <i class="fas fa-heart me-2"></i> <span>Favoritos</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="favoritos-list">
                                @foreach($favoritos as $fav)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $fav->libro->titulo }}</span>
                                        <a href="{{ route('books.show', $fav->libro) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-gradient-info text-white d-flex align-items-center">
                            <i class="fas fa-bell me-2"></i> <span>Notificaciones</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="notificaciones-list">
                                @foreach($notificaciones as $notif)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $notif->titulo }}</span>
                                        <span class="badge bg-{{ $notif->tipo }} ms-2">{{ ucfirst($notif->tipo) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @if($actividad_reciente->count())
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header bg-gradient-secondary text-white d-flex align-items-center">
                            <i class="fas fa-history me-2"></i> <span>Actividad Reciente</span>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($actividad_reciente->take(10) as $actividad)
                                    <div class="timeline-item">
                                        <div class="timeline-icon bg-{{ $actividad['color'] }}">
                                            <i class="{{ $actividad['icono'] }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>{{ $actividad['titulo'] }}</h6>
                                            <p class="text-muted mb-0">{{ $actividad['descripcion'] }}</p>
                                            <small class="text-muted">{{ $actividad['fecha']->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header bg-gradient-warning text-white d-flex align-items-center">
                            <i class="fas fa-shopping-cart me-2"></i> <span>Compras Recientes</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="row g-0">
                                @forelse($compras_recientes as $compra)
                                    <div class="col-md-4 col-lg-3 p-3 d-flex align-items-stretch">
                                        <div class="card h-100 w-100 border-0 shadow-sm">
                                            <div class="text-center pt-3">
                                                <img src="{{ $compra->libro->imagen_portada ? asset('storage/' . $compra->libro->imagen_portada) : asset('images/portada_default.png') }}" alt="Portada" class="img-fluid rounded mb-2" style="max-height: 120px; max-width: 90px; object-fit: cover;">
                                            </div>
                                            <div class="card-body p-2">
                                                <h6 class="fw-bold mb-1" style="font-size:1rem;">{{ $compra->libro->titulo }}</h6>
                                                <div class="small text-muted mb-1">{{ $compra->libro->autor->nombre ?? '' }}</div>
                                                <span class="badge bg-primary">{{ ucfirst($compra->modalidad ?? 'online') }}</span>
                                                <span class="badge bg-success">S/ {{ number_format($compra->precio,2) }}</span>
                                            </div>
                                            <div class="card-footer bg-transparent border-0 text-center pb-3">
                                                <a href="{{ route('books.show', $compra->libro) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                                @if($compra->libro->archivo_pdf)
                                                    <a href="{{ route('books.read', $compra->libro) }}" class="btn btn-outline-success btn-sm ms-2"><i class="fas fa-book-open"></i> Leer</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 p-4 text-center text-muted">No tienes compras recientes.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-icon {
    position: absolute;
    left: -40px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -25px;
    top: 30px;
    width: 2px;
    height: calc(100% + 10px);
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}
</style>
@push('scripts')
<script>
// Refrescar panel de usuario en tiempo real
function refreshDashboard() {
    fetch("{{ route('user.dashboard') }}?ajax=1")
        .then(response => response.json())
        .then(data => {
            // Actualizar contadores
            document.querySelector('.card.bg-primary h4').textContent = data.favoritos;
            document.querySelector('.card.bg-success h4').textContent = data.prestamos_recientes;
            document.querySelector('.card.bg-warning h4').textContent = data.total_compras;
            document.querySelector('.card.bg-info h4').textContent = data.notificaciones;
            // Actualizar listados (ejemplo para favoritos, puedes expandir para otros)
            if(data.favoritos_list && Array.isArray(data.favoritos_list)) {
                const favList = document.getElementById('favoritos-list');
                favList.innerHTML = '';
                if(data.favoritos_list.length === 0) {
                    favList.innerHTML = '<p class="text-muted">No tienes libros favoritos.</p>';
                } else {
                    data.favoritos_list.forEach(fav => {
                        favList.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <span>${fav.titulo}</span>
                            <a href='/books/${fav.id}' class='btn btn-outline-primary btn-sm'><i class='fas fa-eye'></i> Ver</a>
                        </li>`;
                    });
                }
            }
            // Préstamos recientes
            if(data.prestamos_list && Array.isArray(data.prestamos_list)) {
                const presList = document.getElementById('prestamos-list');
                presList.innerHTML = '';
                if(data.prestamos_list.length === 0) {
                    presList.innerHTML = '<p class="text-muted">No tienes préstamos recientes.</p>';
                } else {
                    data.prestamos_list.forEach(pres => {
                        presList.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <div>
                                <strong>${pres.titulo}</strong>
                                <span class='badge bg-secondary ms-2'>${pres.fecha_prestamo}</span>
                                <span class='badge bg-${pres.estado == 'prestado' ? 'success' : 'secondary'} ms-2'>${pres.estado.charAt(0).toUpperCase() + pres.estado.slice(1)}</span>
                                <div class='mt-1'><span class='badge bg-info'><i class='fas fa-map-marker-alt me-1'></i>${pres.ubicacion ?? 'Sin ubicación'}</span></div>
                            </div>
                            ${pres.archivo_pdf ? `<a href='/books/${pres.id}/read' class='btn btn-outline-primary btn-sm' target='_blank'><i class='fas fa-eye'></i> Leer</a>` : ''}
                        </li>`;
                    });
                }
            }
            // Reservas activas
            if(data.reservas_list && Array.isArray(data.reservas_list)) {
                const resList = document.getElementById('reservas-list');
                resList.innerHTML = '';
                if(data.reservas_list.length === 0) {
                    resList.innerHTML = '<p class="text-muted">No tienes reservas activas.</p>';
                } else {
                    data.reservas_list.forEach(res => {
                        resList.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <div>
                                <strong>${res.titulo}</strong>
                                <span class='badge bg-secondary ms-2'>${res.fecha_reserva}</span>
                                <span class='badge bg-warning ms-2'>Pendiente</span>
                            </div>
                            <a href='/books/${res.id}' class='btn btn-outline-primary btn-sm'><i class='fas fa-eye'></i> Ver</a>
                        </li>`;
                    });
                }
            }
            // Notificaciones
            if(data.notificaciones_list && Array.isArray(data.notificaciones_list)) {
                const notifList = document.getElementById('notificaciones-list');
                notifList.innerHTML = '';
                if(data.notificaciones_list.length === 0) {
                    notifList.innerHTML = '<p class="text-muted">No tienes notificaciones nuevas.</p>';
                } else {
                    data.notificaciones_list.forEach(notif => {
                        notifList.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <span>${notif.titulo}</span>
                            <span class='badge bg-${notif.tipo} ms-2'>${notif.tipo.charAt(0).toUpperCase() + notif.tipo.slice(1)}</span>
                        </li>`;
                    });
                }
            }
        })
        .catch(() => alert('Error al actualizar el panel.'));
}
document.getElementById('refresh-dashboard').addEventListener('click', refreshDashboard);
setInterval(refreshDashboard, 60000);
</script>
@endpush
@endsection 