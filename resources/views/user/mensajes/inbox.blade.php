@extends('layouts.app')

@section('title', 'Mensajes')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-comments me-3"></i>Mensajes
                    </h1>
                    <p class="lead text-muted">Comunícate con otros usuarios y administradores</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevoMensajeModal">
                        <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                    </button>
                    <button class="btn btn-outline-success" onclick="marcarTodosComoLeidos()">
                        <i class="fas fa-check-double me-2"></i>Marcar todos como leídos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="bg-gradient-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-comments fa-2x text-white"></i>
                    </div>
                    <h4 class="text-primary fw-bold" id="totalConversaciones">{{ count($conversacionesConUsuarios) }}</h4>
                    <p class="text-muted mb-0">Conversaciones</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body">
                    <div class="bg-gradient-warning rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-envelope fa-2x text-white"></i>
                    </div>
                    <h4 class="text-warning fw-bold" id="mensajesNoLeidos">{{ collect($conversacionesConUsuarios)->sum('no_leidos') }}</h4>
                    <p class="text-muted mb-0">No leídos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="bg-gradient-success rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <h4 class="text-success fw-bold" id="usuariosActivos">{{ count($conversacionesConUsuarios) }}</h4>
                    <p class="text-muted mb-0">Contactos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <div class="bg-gradient-info rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                    <h4 class="text-info fw-bold" id="hoy">{{ collect($conversacionesConUsuarios)->filter(function($conv) { return $conv['ultimo_mensaje']->created_at->isToday(); })->count() }}</h4>
                    <p class="text-muted mb-0">Hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Conversaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Conversaciones
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="input-group input-group-sm me-3" style="width: 300px;">
                            <span class="input-group-text bg-transparent border-0 text-white">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control bg-transparent text-white border-0" 
                                   id="buscarConversaciones" placeholder="Buscar conversaciones...">
                        </div>
                        <span class="badge bg-light text-dark" id="contadorConversaciones">{{ count($conversacionesConUsuarios) }} conversaciones</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($conversacionesConUsuarios) > 0)
                        <div class="conversaciones-container">
                            @foreach($conversacionesConUsuarios as $index => $conversacion)
                                <div class="conversacion-item animate__animated animate__fadeInUp" 
                                     style="animation-delay: {{ $index * 0.1 }}s;"
                                     data-usuario="{{ strtolower($conversacion['usuario']->nombre . ' ' . $conversacion['usuario']->apellido) }}"
                                     data-email="{{ strtolower($conversacion['usuario']->email) }}">
                                    <div class="conversacion-content {{ $conversacion['no_leidos'] > 0 ? 'no-leida' : 'leida' }}" 
                                         onclick="abrirConversacion({{ $conversacion['usuario']->id }})">
                                        <div class="conversacion-avatar">
                                            @if($conversacion['usuario']->avatar)
                                                <img src="{{ asset('storage/' . $conversacion['usuario']->avatar) }}" 
                                                     alt="{{ $conversacion['usuario']->nombre }}" 
                                                     class="avatar-img">
                                            @else
                                                <div class="avatar-placeholder">
                                                    {{ strtoupper(substr($conversacion['usuario']->nombre, 0, 1) . substr($conversacion['usuario']->apellido, 0, 1)) }}
                                                </div>
                                            @endif
                                            @if($conversacion['no_leidos'] > 0)
                                                <span class="badge-notificacion">{{ $conversacion['no_leidos'] }}</span>
                                            @endif
                                        </div>
                                        <div class="conversacion-body">
                                            <div class="conversacion-header">
                                                <h6 class="conversacion-nombre mb-1">
                                                    {{ $conversacion['usuario']->nombre }} {{ $conversacion['usuario']->apellido }}
                                                    @if($conversacion['usuario']->tipo === 'admin')
                                                        <span class="badge bg-danger ms-2">Admin</span>
                                                    @endif
                                                </h6>
                                                <div class="conversacion-meta">
                                                    <span class="conversacion-fecha">{{ $conversacion['ultimo_mensaje']->created_at->diffForHumans() }}</span>
                                                    @if($conversacion['no_leidos'] > 0)
                                                        <span class="badge bg-warning ms-2">{{ $conversacion['no_leidos'] }} nuevo(s)</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="conversacion-preview mb-0">
                                                @if($conversacion['ultimo_mensaje']->remitente_id === Auth::id())
                                                    <i class="fas fa-reply text-muted me-1"></i>
                                                @endif
                                                {{ Str::limit($conversacion['ultimo_mensaje']->contenido, 80) }}
                                            </p>
                                        </div>
                                        <div class="conversacion-acciones">
                                            <button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); abrirConversacion({{ $conversacion['usuario']->id }})">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                            @if($conversacion['no_leidos'] > 0)
                                                <button class="btn btn-outline-success btn-sm" onclick="event.stopPropagation(); marcarConversacionComoLeida({{ $conversacion['usuario']->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state animate__animated animate__fadeIn">
                                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted mb-3">No tienes conversaciones</h4>
                                <p class="text-muted mb-4">Comienza una nueva conversación para conectarte con otros usuarios</p>
                                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#nuevoMensajeModal">
                                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Mensaje -->
<div class="modal fade" id="nuevoMensajeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoMensajeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="destinatario_id" class="form-label">Destinatario</label>
                        <select class="form-select" id="destinatario_id" name="destinatario_id" required>
                            <option value="">Seleccionar destinatario...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contenido" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="5" 
                                  placeholder="Escribe tu mensaje aquí..." required maxlength="1000"></textarea>
                        <div class="form-text">
                            <span id="caracteresRestantes">1000</span> caracteres restantes
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de mensaje</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="texto">Texto</option>
                            <option value="imagen">Imagen</option>
                            <option value="archivo">Archivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarMensaje()">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos para conversaciones */
.conversaciones-container {
    max-height: 600px;
    overflow-y: auto;
}

.conversacion-item {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.conversacion-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(5px);
}

.conversacion-content {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.conversacion-content.no-leida {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));
    border-left: 4px solid #667eea;
}

.conversacion-content.leida {
    opacity: 0.8;
}

.conversacion-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    border: 3px solid #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.badge-notificacion {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    animation: pulse 2s infinite;
}

.conversacion-body {
    flex-grow: 1;
}

.conversacion-nombre {
    font-weight: 600;
    color: #333;
    margin: 0;
}

.conversacion-preview {
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.conversacion-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.conversacion-fecha {
    font-size: 0.875rem;
    color: #888;
}

.conversacion-acciones {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Estado vacío */
.empty-state {
    padding: 3rem;
}

/* Animaciones */
@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.conversacion-item {
    animation: slideInFromRight 0.5s ease-out;
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Scrollbar personalizado */
.conversaciones-container::-webkit-scrollbar {
    width: 8px;
}

.conversaciones-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.conversaciones-container::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 10px;
}

.conversaciones-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #5a6fd8, #6a4190);
}

/* Responsive */
@media (max-width: 768px) {
    .conversacion-content {
        flex-direction: column;
        text-align: center;
    }
    
    .conversacion-acciones {
        justify-content: center;
        margin-top: 1rem;
    }
}

/* Estilos para el modal */
.modal-content {
    border-radius: 15px;
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

/* Input de búsqueda */
#buscarConversaciones {
    color: white !important;
}

#buscarConversaciones::placeholder {
    color: rgba(255, 255, 255, 0.7) !important;
}

#buscarConversaciones:focus {
    box-shadow: none;
    border-color: rgba(255, 255, 255, 0.5);
}
</style>
@endpush

@push('scripts')
<script>
let conversaciones = @json($conversacionesConUsuarios);

function abrirConversacion(usuarioId) {
    window.location.href = `/mensajes/${usuarioId}`;
}

function marcarConversacionComoLeida(usuarioId) {
    fetch(`/mensajes/conversacion/${usuarioId}/marcar-leida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar UI
            const item = document.querySelector(`[onclick*="${usuarioId}"]`).closest('.conversacion-item');
            item.querySelector('.conversacion-content').classList.remove('no-leida');
            item.querySelector('.conversacion-content').classList.add('leida');
            
            // Remover badge de notificación
            const badge = item.querySelector('.badge-notificacion');
            if (badge) {
                badge.remove();
            }
            
            // Actualizar contadores
            const noLeidos = parseInt(document.getElementById('mensajesNoLeidos').textContent) - 1;
            document.getElementById('mensajesNoLeidos').textContent = noLeidos;
            
            mostrarNotificacion('Conversación marcada como leída', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al marcar como leída', 'error');
    });
}

function marcarTodosComoLeidos() {
    if (confirm('¿Estás seguro de que quieres marcar todas las conversaciones como leídas?')) {
        fetch('/mensajes/marcar-todos-leidos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar UI
                document.querySelectorAll('.conversacion-content').forEach(content => {
                    content.classList.remove('no-leida');
                    content.classList.add('leida');
                });
                
                // Remover todos los badges
                document.querySelectorAll('.badge-notificacion').forEach(badge => {
                    badge.remove();
                });
                
                // Actualizar contadores
                document.getElementById('mensajesNoLeidos').textContent = '0';
                
                mostrarNotificacion('Todas las conversaciones marcadas como leídas', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error al marcar como leídas', 'error');
        });
    }
}

function enviarMensaje() {
    const form = document.getElementById('nuevoMensajeForm');
    const formData = new FormData(form);
    
    fetch('/mensajes', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            destinatario_id: formData.get('destinatario_id'),
            contenido: formData.get('contenido'),
            tipo: formData.get('tipo')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoMensajeModal'));
            modal.hide();
            
            // Limpiar formulario
            form.reset();
            document.getElementById('caracteresRestantes').textContent = '1000';
            
            // Recargar página para mostrar nueva conversación
            location.reload();
            
            mostrarNotificacion('Mensaje enviado correctamente', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al enviar mensaje', 'error');
    });
}

// Búsqueda de conversaciones
document.getElementById('buscarConversaciones').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.conversacion-item');
    let contador = 0;
    
    items.forEach(item => {
        const usuario = item.dataset.usuario;
        const email = item.dataset.email;
        
        if (usuario.includes(query) || email.includes(query)) {
            item.style.display = 'block';
            item.classList.add('animate__fadeIn');
            contador++;
        } else {
            item.style.display = 'none';
        }
    });
    
    document.getElementById('contadorConversaciones').textContent = `${contador} conversaciones`;
});

// Contador de caracteres
document.getElementById('contenido').addEventListener('input', function(e) {
    const maxLength = 1000;
    const currentLength = e.target.value.length;
    const remaining = maxLength - currentLength;
    
    document.getElementById('caracteresRestantes').textContent = remaining;
    
    if (remaining < 50) {
        document.getElementById('caracteresRestantes').style.color = '#dc3545';
    } else if (remaining < 100) {
        document.getElementById('caracteresRestantes').style.color = '#ffc107';
    } else {
        document.getElementById('caracteresRestantes').style.color = '#6c757d';
    }
});

// Cargar usuarios para el modal
document.getElementById('nuevoMensajeModal').addEventListener('show.bs.modal', function() {
    const select = document.getElementById('destinatario_id');
    select.innerHTML = '<option value="">Cargando usuarios...</option>';
    
    fetch('/mensajes/buscar-usuarios?q=')
        .then(response => response.json())
        .then(usuarios => {
            select.innerHTML = '<option value="">Seleccionar destinatario...</option>';
            usuarios.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id;
                option.textContent = `${usuario.nombre} ${usuario.apellido} (${usuario.email})`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            select.innerHTML = '<option value="">Error al cargar usuarios</option>';
        });
});

function mostrarNotificacion(mensaje, tipo) {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${tipo} animate__animated animate__fadeInRight`;
    toast.innerHTML = `
        <i class="fas fa-${tipo === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
        ${mensaje}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('animate__fadeInRight');
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Auto-refresh cada 30 segundos
setInterval(() => {
    fetch('/mensajes/no-leidos')
        .then(response => response.json())
        .then(data => {
            if (data.count > parseInt(document.getElementById('mensajesNoLeidos').textContent)) {
                location.reload();
            }
        });
}, 30000);

// Animación de entrada
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.conversacion-item');
    items.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
});

// Agregar estilos para notificaciones
const style = document.createElement('style');
style.textContent = `
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        color: white;
        z-index: 9999;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .toast-success {
        background: linear-gradient(45deg, #28a745, #20c997);
    }
    
    .toast-error {
        background: linear-gradient(45deg, #dc3545, #fd7e14);
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection 