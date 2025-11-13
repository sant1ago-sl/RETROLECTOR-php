@extends('layouts.app')
@section('title', 'Reseñas Pendientes')
@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="fas fa-star me-2 text-warning"></i>Reseñas Pendientes de Moderación</h1>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>Usuario</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resenas as $resena)
                    <tr>
                        <td>{{ $resena->libro->titulo }}</td>
                        <td>{{ $resena->usuario->nombre }} {{ $resena->usuario->apellido }}</td>
                        <td>{{ $resena->calificacion }}/5</td>
                        <td>{{ $resena->comentario }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.resenas.aprobar', $resena) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Aprobar</button>
                            </form>
                            <form method="POST" action="{{ route('admin.resenas.rechazar', $resena) }}" class="d-inline ms-2">
                                @csrf
                                <button class="btn btn-danger btn-sm">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 