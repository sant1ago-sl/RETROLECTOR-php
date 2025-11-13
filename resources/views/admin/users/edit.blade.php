@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h2>
    <form method="POST" action="{{ route('admin.users.update', $usuario) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="apellido" class="form-label">Apellido *</label>
                <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required>
                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">
                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $usuario->direccion) }}">
            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                    <option value="activo" {{ old('estado', $usuario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $usuario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection 