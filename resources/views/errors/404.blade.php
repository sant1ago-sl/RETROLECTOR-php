@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body py-5">
                    <h1 class="display-1 fw-bold text-primary mb-3">404</h1>
                    <h2 class="mb-3">Página no encontrada</h2>
                    <p class="lead mb-4">La página que buscas no existe, fue movida o nunca estuvo aquí.<br>Por favor, verifica la URL o vuelve al inicio.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Ir al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 