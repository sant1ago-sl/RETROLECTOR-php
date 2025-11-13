@extends('layouts.app')

@section('title', 'Reseñas de Usuarios')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-star me-2"></i>Reseñas de Usuarios</h4>
                </div>
                <div class="card-body">
                    <!-- Tabla de reseñas -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="reviews-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Libro</th>
                                    <th>Comentario</th>
                                    <th>Calificación</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resenas as $resena)
                                    <tr>
                                        <td>{{ $resena->id }}</td>
                                        <td>
                                            <strong>{{ $resena->usuario->nombre }} {{ $resena->usuario->apellido }}</strong><br>
                                            <span class="text-muted small">{{ $resena->usuario->email }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $resena->libro->titulo }}</strong><br>
                                            <span class="text-muted small">{{ $resena->libro->autor->nombre }} {{ $resena->libro->autor->apellido }}</span>
                                        </td>
                                        <td>{{ Str::limit($resena->comentario, 100) }}</td>
                                        <td>{{ $resena->calificacion }}/5</td>
                                        <td>{{ $resena->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <form action="{{ route('admin.reviews.delete', $resena->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta reseña?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay reseñas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                            {{ $resenas->links() }}
                        </div>
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

function filterReviews() {
    const estado = document.getElementById('estado_filter').value.toLowerCase();
    const calificacion = document.getElementById('calificacion_filter').value;
    const usuario = document.getElementById('usuario_filter').value.toLowerCase();
    const libro = document.getElementById('libro_filter').value.toLowerCase();

    const rows = document.querySelectorAll('.review-row');
    
    rows.forEach(row => {
        const rowEstado = row.dataset.estado;
        const rowCalificacion = row.dataset.calificacion;
        const rowUsuario = row.dataset.usuario;
        const rowLibro = row.dataset.libro;

        const matchEstado = !estado || rowEstado === estado;
        const matchCalificacion = !calificacion || rowCalificacion === calificacion;
        const matchUsuario = !usuario || rowUsuario.includes(usuario);
        const matchLibro = !libro || rowLibro.includes(libro);

        if (matchEstado && matchCalificacion && matchUsuario && matchLibro) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
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

function deleteReview(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta reseña? Esta acción no se puede deshacer.')) {
        fetch(`/admin/reviews/${id}`, {
            method: 'DELETE',
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