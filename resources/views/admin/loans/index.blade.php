@extends('layouts.app')

@section('title', 'Gestión de Préstamos')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-handshake me-2"></i>Gestión de Préstamos</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-sm" onclick="refreshLoans()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="{{ route('admin.loans.export') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="estado_filter" class="form-label">Estado</label>
                            <select id="estado_filter" class="form-select" onchange="filterLoans()">
                                <option value="">Todos</option>
                                <option value="prestado">Prestado</option>
                                <option value="devuelto">Devuelto</option>
                                <option value="vencido">Vencido</option>
                                <option value="perdido">Perdido</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="usuario_filter" class="form-label">Usuario</label>
                            <input type="text" id="usuario_filter" class="form-control" placeholder="Buscar por usuario..." onkeyup="filterLoans()">
                        </div>
                        <div class="col-md-3">
                            <label for="libro_filter" class="form-label">Libro</label>
                            <input type="text" id="libro_filter" class="form-control" placeholder="Buscar por libro..." onkeyup="filterLoans()">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_filter" class="form-label">Fecha</label>
                            <input type="date" id="fecha_filter" class="form-control" onchange="filterLoans()">
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $prestamos->where('estado', 'prestado')->count() }}</h5>
                                    <p class="card-text">Activos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $prestamos->where('estado', 'devuelto')->count() }}</h5>
                                    <p class="card-text">Devueltos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $prestamos->where('estado', 'vencido')->count() }}</h5>
                                    <p class="card-text">Vencidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $prestamos->where('estado', 'perdido')->count() }}</h5>
                                    <p class="card-text">Perdidos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de préstamos -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="loans-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Libro</th>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                    <th>Días Restantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prestamos as $prestamo)
                                    <tr class="loan-row" data-estado="{{ $prestamo->estado }}" data-usuario="{{ strtolower($prestamo->usuario->nombre . ' ' . $prestamo->usuario->apellido) }}" data-libro="{{ strtolower($prestamo->libro->titulo) }}" data-fecha="{{ $prestamo->fecha_prestamo->format('Y-m-d') }}">
                                        <td>{{ $prestamo->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($prestamo->usuario->nombre, 0, 1) }}{{ substr($prestamo->usuario->apellido, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $prestamo->usuario->nombre }} {{ $prestamo->usuario->apellido }}</strong>
                                                    <div class="text-muted small">{{ $prestamo->usuario->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @if($prestamo->libro->imagen_portada)
                                                        <img src="{{ $prestamo->libro->imagen_portada }}" alt="Portada" class="img-thumbnail" style="width: 40px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 50px;">
                                                            <i class="fas fa-book"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $prestamo->libro->titulo }}</strong>
                                                    <div class="text-muted small">{{ $prestamo->libro->autor->nombre }} {{ $prestamo->libro->autor->apellido }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $prestamo->fecha_prestamo->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="{{ $prestamo->fecha_devolucion_esperada < now() ? 'text-danger' : '' }}">
                                                {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($prestamo->estado)
                                                @case('prestado')
                                                    <span class="badge bg-primary">Prestado</span>
                                                    @break
                                                @case('devuelto')
                                                    <span class="badge bg-success">Devuelto</span>
                                                    @break
                                                @case('vencido')
                                                    <span class="badge bg-warning">Vencido</span>
                                                    @break
                                                @case('perdido')
                                                    <span class="badge bg-danger">Perdido</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $prestamo->estado }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($prestamo->estado === 'prestado')
                                                @php
                                                    $diasRestantes = now()->diffInDays($prestamo->fecha_devolucion_esperada, false);
                                                @endphp
                                                @if($diasRestantes > 0)
                                                    <span class="text-success">{{ $diasRestantes }} días</span>
                                                @elseif($diasRestantes == 0)
                                                    <span class="text-warning">Hoy</span>
                                                @else
                                                    <span class="text-danger">{{ abs($diasRestantes) }} días tarde</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewLoan({{ $prestamo->id }})" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($prestamo->estado === 'prestado')
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="returnLoan({{ $prestamo->id }})" title="Marcar como devuelto">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="extendLoan({{ $prestamo->id }})" title="Extender préstamo">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteLoan({{ $prestamo->id }})" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No hay préstamos registrados</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($prestamos->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $prestamos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del préstamo -->
<div class="modal fade" id="loanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="loanModalBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
function refreshLoans() {
    location.reload();
}

function filterLoans() {
    const estado = document.getElementById('estado_filter').value.toLowerCase();
    const usuario = document.getElementById('usuario_filter').value.toLowerCase();
    const libro = document.getElementById('libro_filter').value.toLowerCase();
    const fecha = document.getElementById('fecha_filter').value;

    const rows = document.querySelectorAll('.loan-row');
    
    rows.forEach(row => {
        const rowEstado = row.dataset.estado;
        const rowUsuario = row.dataset.usuario;
        const rowLibro = row.dataset.libro;
        const rowFecha = row.dataset.fecha;

        const matchEstado = !estado || rowEstado === estado;
        const matchUsuario = !usuario || rowUsuario.includes(usuario);
        const matchLibro = !libro || rowLibro.includes(libro);
        const matchFecha = !fecha || rowFecha === fecha;

        if (matchEstado && matchUsuario && matchLibro && matchFecha) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function viewLoan(id) {
    fetch(`/admin/loans/${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('loanModalBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('loanModal')).show();
        })
        .catch(error => console.error('Error:', error));
}

function returnLoan(id) {
    if (confirm('¿Confirmar que el libro ha sido devuelto?')) {
        fetch(`/admin/loans/${id}/return`, {
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

function extendLoan(id) {
    const dias = prompt('¿Cuántos días adicionales?', '7');
    if (dias && !isNaN(dias)) {
        fetch(`/admin/loans/${id}/extend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ dias: parseInt(dias) })
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

function deleteLoan(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este préstamo? Esta acción no se puede deshacer.')) {
        fetch(`/admin/loans/${id}`, {
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
</script>
@endsection 