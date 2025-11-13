@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="container-fluid py-4">
    <!-- Formulario de crear categoría - siempre visible -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nueva Categoría</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required maxlength="255" placeholder="Ej: Ficción">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="1" maxlength="500" placeholder="Descripción de la categoría"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" name="color" id="color" class="form-control form-control-color" value="#007bff">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="icono" class="form-label">Icono</label>
                                    <input type="text" name="icono" id="icono" class="form-control" placeholder="fas fa-tag" maxlength="50">
                                    <div class="form-text small">fas fa-tag</div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de categorías -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-tags me-2"></i>Lista de Categorías</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-sm" onclick="refreshCategories()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $categorias->count() }}</h5>
                                    <p class="card-text">Total Categorías</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $categorias->where('estado', 'activa')->count() }}</h5>
                                    <p class="card-text">Activas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $categorias->where('estado', 'inactiva')->count() }}</h5>
                                    <p class="card-text">Inactivas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $categorias->sum('libros_count') }}</h5>
                                    <p class="card-text">Total Libros</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de categorías -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="categories-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Color</th>
                                    <th>Libros</th>
                                    <th>Estado</th>
                                    <th>Creada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="{{ $categoria->icono ?? 'fas fa-tag' }}" style="color: {{ $categoria->color }};"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $categoria->nombre }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ Str::limit($categoria->descripcion, 50) ?: 'Sin descripción' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="color-preview me-2" style="background-color: {{ $categoria->color }}; width: 20px; height: 20px; border-radius: 4px; border: 1px solid #ddd;"></div>
                                                <span class="text-muted">{{ $categoria->color }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $categoria->libros_count }} libros</span>
                                        </td>
                                        <td>
                                            @if($categoria->estado === 'activa')
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-warning">Inactiva</span>
                                            @endif
                                        </td>
                                        <td>{{ $categoria->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCategory({{ $categoria->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($categoria->libros_count == 0)
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $categoria->id }})" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="No se puede eliminar (tiene libros)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3"></i>
                                                <p>No hay categorías registradas</p>
                                                <p class="small">Usa el formulario de arriba para crear tu primera categoría</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($categorias->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $categorias->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar categoría -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_color" class="form-label">Color</label>
                        <input type="color" name="color" id="edit_color" class="form-control form-control-color">
                    </div>
                    <div class="mb-3">
                        <label for="edit_icono" class="form-label">Icono (FontAwesome)</label>
                        <input type="text" name="icono" id="edit_icono" class="form-control" placeholder="fas fa-tag" maxlength="50">
                        <div class="form-text">Ejemplo: fas fa-tag, fas fa-book, fas fa-heart</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Actualizar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
function refreshCategories() {
    location.reload();
}

function editCategory(id) {
    // Aquí podrías cargar los datos de la categoría vía AJAX
    // Por ahora, redirigimos a la página de edición
    window.location.href = `/admin/categories/${id}/edit`;
}

function deleteCategory(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta categoría? Esta acción no se puede deshacer.')) {
        fetch(`/admin/categories/${id}`, {
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

// Previsualización del color en tiempo real
document.getElementById('color').addEventListener('input', function(e) {
    const color = e.target.value;
    // Puedes agregar lógica de previsualización aquí
});

document.getElementById('edit_color').addEventListener('input', function(e) {
    const color = e.target.value;
    // Puedes agregar lógica de previsualización aquí
});
</script>
@endsection 