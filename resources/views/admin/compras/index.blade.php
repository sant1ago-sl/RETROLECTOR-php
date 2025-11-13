@extends('layouts.app')

@section('title', 'Gestión de Compras')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Gestión de Compras</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="row mb-4 g-2">
                        <div class="col-md-3">
                            <input type="text" name="usuario" class="form-control" placeholder="Usuario (nombre, email)" value="{{ request('usuario') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="libro" class="form-control" placeholder="Libro" value="{{ request('libro') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="modalidad" class="form-select">
                                <option value="">Modalidad</option>
                                <option value="fisico" {{ request('modalidad')=='fisico'?'selected':'' }}>Físico</option>
                                <option value="virtual" {{ request('modalidad')=='virtual'?'selected':'' }}>Online</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="estado" class="form-select">
                                <option value="">Estado</option>
                                <option value="completada" {{ request('estado')=='completada'?'selected':'' }}>Completada</option>
                                <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
                                <option value="cancelada" {{ request('estado')=='cancelada'?'selected':'' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <!-- Tabla de compras -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Libro</th>
                                    <th>Modalidad</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compras as $compra)
                                    <tr>
                                        <td>{{ $compra->id }}</td>
                                        <td>
                                            <strong>{{ $compra->usuario->nombre ?? '-' }} {{ $compra->usuario->apellido ?? '' }}</strong><br>
                                            <span class="text-muted small">{{ $compra->usuario->email ?? '' }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $compra->libro->titulo ?? '-' }}</strong><br>
                                            <span class="text-muted small">{{ $compra->libro->autor->nombre ?? '' }}</span>
                                        </td>
                                        <td>
                                            @if($compra->tipo=='fisico')
                                                <span class="badge bg-primary">Físico</span>
                                            @else
                                                <span class="badge bg-success">Online</span>
                                            @endif
                                        </td>
                                        <td>S/{{ number_format($compra->precio,2) }}</td>
                                        <td>
                                            @switch($compra->estado)
                                                @case('completada')
                                                    <span class="badge bg-success">Completada</span>
                                                    @break
                                                @case('pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                    @break
                                                @case('cancelada')
                                                    <span class="badge bg-danger">Cancelada</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $compra->estado }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $compra->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detalleCompra{{ $compra->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <!-- Modal Detalle -->
                                            <div class="modal fade" id="detalleCompra{{ $compra->id }}" tabindex="-1" aria-labelledby="detalleCompraLabel{{ $compra->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="detalleCompraLabel{{ $compra->id }}">Detalle de Compra #{{ $compra->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <strong>Usuario:</strong> {{ $compra->usuario->nombre ?? '-' }} {{ $compra->usuario->apellido ?? '' }}<br>
                                                                    <strong>Email:</strong> {{ $compra->usuario->email ?? '' }}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Libro:</strong> {{ $compra->libro->titulo ?? '-' }}<br>
                                                                    <strong>Autor:</strong> {{ $compra->libro->autor->nombre ?? '' }}
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4">
                                                                    <strong>Modalidad:</strong> {{ $compra->tipo }}
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Precio:</strong> S/{{ number_format($compra->precio,2) }}
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Estado:</strong> {{ $compra->estado }}
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-12">
                                                                    <strong>Fecha de compra:</strong> {{ $compra->created_at->format('d/m/Y H:i') }}
                                                                </div>
                                                            </div>
                                                            @if($compra->datos_envio)
                                                                <div class="row mt-3">
                                                                    <div class="col-12">
                                                                        <strong>Datos de Envío / Pago:</strong>
                                                                        <pre class="bg-light p-2 rounded">{{ json_encode(json_decode($compra->datos_envio), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No hay compras registradas</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $compras->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 