@extends('layouts.app')
@section('title', 'Leer Mensaje')
@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg animate__animated animate__fadeInUp border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-envelope-open-text fa-3x text-primary me-3"></i>
                        <div>
                            <h3 class="fw-bold mb-1">{{ $mensaje->asunto }}</h3>
                            <small class="text-muted">{{ $mensaje->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0">{{ $mensaje->contenido }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 