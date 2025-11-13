@extends('layouts.app')

@section('title', 'Notificaciones del Sistema')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Panel de envío de notificaciones -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Enviar Notificación</h5>
                </div>
                <div class="card-body">
                    <!-- Tabs para individual y masiva -->
                    <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
                                <i class="fas fa-user me-1"></i>Individual
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab">
                                <i class="fas fa-users me-1"></i>Masiva
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="notificationTabsContent">
                        <!-- Notificación Individual -->
                        <div class="tab-pane fade show active" id="individual" role="tabpanel">
                            <form action="{{ route('admin.notifications.send') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="usuario_id" class="form-label">Usuario</label>
                                    <select name="usuario_id" id="usuario_id" class="form-select" required>
                                        <option value="">Seleccionar usuario...</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="mensaje" class="form-label">Mensaje</label>
                                    <textarea name="mensaje" id="mensaje" class="form-control" rows="3" required maxlength="1000"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select name="tipo" id="tipo" class="form-select" required>
                                        <option value="info">Información</option>
                                        <option value="success">Éxito</option>
                                        <option value="warning">Advertencia</option>
                                        <option value="error">Error</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar
                                </button>
                            </form>
                        </div>

                        <!-- Notificación Masiva -->
                        <div class="tab-pane fade" id="bulk" role="tabpanel">
                            <form action="{{ route('admin.notifications.send-bulk') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="destinatarios" class="form-label">Destinatarios</label>
                                    <select name="destinatarios" id="destinatarios" class="form-select" required>
                                        <option value="todos">Todos los usuarios</option>
                                        <option value="activos">Solo usuarios activos</option>
                                        <option value="inactivos">Solo usuarios inactivos</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="titulo_bulk" class="form-label">Título</label>
                                    <input type="text" name="titulo" id="titulo_bulk" class="form-control" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="mensaje_bulk" class="form-label">Mensaje</label>
                                    <textarea name="mensaje" id="mensaje_bulk" class="form-control" rows="3" required maxlength="1000"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo_bulk" class="form-label">Tipo</label>
                                    <select name="tipo" id="tipo_bulk" class="form-select" required>
                                        <option value="info">Información</option>
                                        <option value="success">Éxito</option>
                                        <option value="warning">Advertencia</option>
                                        <option value="error">Error</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-broadcast-tower me-2"></i>Enviar Masiva
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Limpiar notificaciones antiguas -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-gradient-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-broom me-2"></i>Mantenimiento</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.cleanup') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="dias" class="form-label">Eliminar notificaciones de más de:</label>
                            <select name="dias" id="dias" class="form-select">
                                <option value="7">7 días</option>
                                <option value="30" selected>30 días</option>
                                <option value="90">90 días</option>
                                <option value="180">180 días</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('¿Estás seguro? Esta acción no se puede deshacer.')">
                            <i class="fas fa-trash me-2"></i>Limpiar Antiguas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historial de notificaciones -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Historial de Notificaciones</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-sm" onclick="refreshNotifications()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <span class="badge bg-light text-dark" id="unread-count">0</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="notifications-container">
                        @if($notificaciones->count())
                            <ul class="list-group list-group-flush">
                                @foreach($notificaciones as $notificacion)
                                    <li class="list-group-item d-flex justify-content-between align-items-start notification-item" data-id="{{ $notificacion->id }}">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-{{ $notificacion->tipo }} me-2">{{ ucfirst($notificacion->tipo) }}</span>
                                                <strong>{{ $notificacion->titulo }}</strong>
                                                @if(!$notificacion->leida)
                                                    <span class="badge bg-warning ms-2">No leída</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small mb-1">{{ $notificacion->mensaje }}</div>
                                            <div class="text-muted small">
                                                <i class="fas fa-user me-1"></i>{{ $notificacion->usuario->nombre ?? 'N/A' }} (ID: {{ $notificacion->usuario_id }})
                                                <span class="ms-3"><i class="fas fa-clock me-1"></i>{{ $notificacion->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if(!$notificacion->leida)
                                                <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $notificacion->id }})" title="Marcar como leída">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notificacion->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-3">
                                {{ $notificaciones->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-info-circle me-2"></i>No hay notificaciones en el sistema.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para funcionalidad AJAX -->
<script>
let refreshInterval;

// Inicializar actualizaciones automáticas
document.addEventListener('DOMContentLoaded', function() {
    refreshNotifications();
    // Actualizar cada 30 segundos
    refreshInterval = setInterval(refreshNotifications, 30000);
});

// Refrescar notificaciones
function refreshNotifications() {
    fetch('{{ route("admin.notifications.get-unread") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('unread-count').textContent = data.count;
        })
        .catch(error => console.error('Error:', error));
}

// Marcar como leída
function markAsRead(id) {
    fetch(`/user/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            const item = document.querySelector(`[data-id="${id}"]`);
            const badge = item.querySelector('.badge.bg-warning');
            if (badge) badge.remove();
            refreshNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Eliminar notificación
function deleteNotification(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                const item = document.querySelector(`[data-id="${id}"]`);
                item.remove();
                refreshNotifications();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', function() {
    if (refreshInterval) clearInterval(refreshInterval);
});
</script>
@endsection 