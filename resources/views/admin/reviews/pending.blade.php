@extends('layouts.app')

@section('title', 'Reseñas Pendientes de Moderación')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-warning text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-star me-2"></i>Reseñas Pendientes de Moderación</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-sm" onclick="refreshReviews()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-list me-1"></i>Todas las Reseñas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $resenas->count() }}</h5>
                                    <p class="card-text">Pendientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ \App\Models\Resena::where('estado', 'aprobada')->count() }}</h5>
                                    <p class="card-text">Aprobadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ \App\Models\Resena::where('estado', 'rechazada')->count() }}</h5>
                                    <p class="card-text">Rechazadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ \App\Models\Resena::count() }}</h5>
                                    <p class="card-text">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($resenas->count() > 0)
                        <!-- Tabla de reseñas pendientes -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="reviews-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Libro</th>
                                        <th>Calificación</th>
                                        <th>Comentario</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resenas as $resena)
                                        <tr data-review-id="{{ $resena->id }}">
                                            <td>{{ $resena->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($resena->usuario->nombre, 0, 1) }}{{ substr($resena->usuario->apellido, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $resena->usuario->nombre }} {{ $resena->usuario->apellido }}</strong>
                                                        <div class="text-muted small">{{ $resena->usuario->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        @if($resena->libro->imagen_portada)
                                                            <img src="{{ $resena->libro->imagen_portada }}" alt="Portada" class="img-thumbnail" style="width: 40px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 50px;">
                                                                <i class="fas fa-book"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $resena->libro->titulo }}</strong>
                                                        <div class="text-muted small">{{ $resena->libro->autor->nombre }} {{ $resena->libro->autor->apellido }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $resena->calificacion)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="ms-2 badge bg-secondary">{{ $resena->calificacion }}/5</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="review-comment">
                                                    <p class="mb-1">{{ Str::limit($resena->comentario, 100) }}</p>
                                                    @if(strlen($resena->comentario) > 100)
                                                        <button class="btn btn-sm btn-outline-primary" onclick="showFullComment({{ $resena->id }})">
                                                            Ver completo
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $resena->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="approveReview({{ $resena->id }})" title="Aprobar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectReview({{ $resena->id }})" title="Rechazar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewReview({{ $resena->id }})" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if($resenas->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $resenas->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <h5>¡Excelente trabajo!</h5>
                                <p>No hay reseñas pendientes de moderación.</p>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list me-1"></i>Ver Todas las Reseñas
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver comentario completo -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comentario Completo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commentModalBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
function refreshReviews() {
    location.reload();
}

function approveReview(id) {
    if (confirm('¿Confirmar que quieres aprobar esta reseña?')) {
        fetch(`/admin/reviews/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function rejectReview(id) {
    if (confirm('¿Confirmar que quieres rechazar esta reseña?')) {
        fetch(`/admin/reviews/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function viewReview(id) {
    // Aquí podrías cargar los detalles de la reseña vía AJAX
    // Por ahora, redirigimos a la página de detalles
    window.location.href = `/admin/reviews/${id}`;
}

function showFullComment(id) {
    // Buscar el comentario en la tabla
    const row = document.querySelector(`tr[data-review-id="${id}"]`);
    const comment = row.querySelector('.review-comment p').textContent;
    
    document.getElementById('commentModalBody').innerHTML = `
        <div class="mb-3">
            <strong>Comentario:</strong>
            <p class="mt-2">${comment}</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('commentModal')).show();
}
</script>
@endsection 