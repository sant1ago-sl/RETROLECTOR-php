@extends('layouts.app')

@section('title', 'Mi Historial - Retrolector')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <h2 class="fw-bold mb-4 text-center">Mi Historial</h2>
    <div class="row justify-content-center">
        <div class="col-md-10">
            @forelse($historial as $item)
                <div class="card mb-3 shadow animate__animated animate__fadeInUp border-{{ $item->tipo }}">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-{{ $item->tipo === 'prestamo' ? 'book-reader text-info' : ($item->tipo === 'compra' ? 'shopping-cart text-success' : 'clock text-warning') }} fa-2x me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">{{ $item->titulo }}</h5>
                            <p class="card-text mb-0">{{ $item->descripcion }}</p>
                            <small class="text-muted">{{ $item->fecha->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h4>No hay historial aún</h4>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal de Información de Envío -->
<div class="modal fade" id="envioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shipping-fast me-2"></i>
                    Información de Envío
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="envioModalBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<script>
function verEnvio(compraId) {
    // Aquí se cargaría la información de envío desde el servidor
    const modalBody = document.getElementById('envioModalBody');
    modalBody.innerHTML = `
        <div class="text-center">
            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
            <h6>En proceso de envío</h6>
            <p class="text-muted">Tu libro está siendo preparado para el envío. Recibirás una notificación cuando esté en camino.</p>
            <div class="alert alert-info">
                <small>
                    <strong>Tiempo estimado de entrega:</strong><br>
                    • Lima: 2-4 días hábiles<br>
                    • Provincias: 3-7 días hábiles<br>
                    <strong>Estado actual:</strong> En preparación<br>
                    <strong>Envío:</strong> Gratuito a todo Perú
                </small>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('envioModal'));
    modal.show();
}

function renovarPrestamo(prestamoId) {
    if (confirm('¿Deseas renovar este préstamo por 14 días más?')) {
        // Aquí se implementaría la lógica de renovación
        alert('Función de renovación en desarrollo. Por favor, contacta al administrador.');
    }
}

// Activar la primera pestaña por defecto
document.addEventListener('DOMContentLoaded', function() {
    const firstTab = document.querySelector('#historialTabs .nav-link');
    const firstTabContent = document.querySelector('#historialTabsContent .tab-pane');
    
    if (firstTab && firstTabContent) {
        firstTab.classList.add('active');
        firstTabContent.classList.add('show', 'active');
    }
});
</script>

<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.nav-tabs .nav-link {
    border: none;
    color: var(--text-color);
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
    background: none;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
}
</style>
@endsection 

@section('scripts')
<script>
    function refreshHistorial() {
        fetch(window.location.href + '?ajax=1')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.querySelector('.row.justify-content-center');
                if (newList) {
                    document.querySelector('.row.justify-content-center').innerHTML = newList.innerHTML;
                }
            });
    }
    setInterval(refreshHistorial, 30000);
</script>
@endsection 