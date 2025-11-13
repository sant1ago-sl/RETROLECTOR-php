@extends('layouts.app')

@section('title', 'Mis Libros Virtuales')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <h2 class="fw-bold mb-4 text-center">Tus Libros</h2>
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills justify-content-center gap-2" id="booksTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="compra-fisica-tab" data-bs-toggle="pill" data-bs-target="#compra-fisica" type="button" role="tab">Compra Física</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="compra-online-tab" data-bs-toggle="pill" data-bs-target="#compra-online" type="button" role="tab">Compra Online</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prestamo-fisico-tab" data-bs-toggle="pill" data-bs-target="#prestamo-fisico" type="button" role="tab">Préstamo Físico</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prestamo-online-tab" data-bs-toggle="pill" data-bs-target="#prestamo-online" type="button" role="tab">Préstamo Online</button>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content" id="booksTabsContent">
        <div class="tab-pane fade show active" id="compra-fisica" role="tabpanel">
            <h4 class="mb-3">Compra Física</h4>
            <div class="row">
                @forelse($compraFisica as $libro)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-lg border-info">
                            <div class="card-img-top text-center" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                @if($libro->imagen_portada)
                                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" class="img-fluid" alt="{{ $libro->titulo }}" style="max-height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $libro->titulo }}</h5>
                                <p class="card-text text-muted mb-1"><i class="fas fa-user me-1"></i> {{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                <div class="alert alert-success mt-2">Gracias por tu compra, tu libro está llegando a tu dirección.</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h4>No tienes compras físicas aún</h4>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="tab-pane fade" id="compra-online" role="tabpanel">
            <h4 class="mb-3">Compra Online</h4>
            <div class="row">
                @forelse($compraOnline as $libro)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-lg border-info">
                            <div class="card-img-top text-center" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                @if($libro->imagen_portada)
                                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" class="img-fluid" alt="{{ $libro->titulo }}" style="max-height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $libro->titulo }}</h5>
                                <p class="card-text text-muted mb-1"><i class="fas fa-user me-1"></i> {{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                <div class="alert alert-info mt-2">Gracias por tu compra, puedes leer tu libro online o descargar el PDF.</div>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="{{ route('books.read', $libro) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-book-open"></i> Leer
                                    </a>
                                    @if($libro->archivo_pdf)
                                    <a href="{{ asset('storage/' . $libro->archivo_pdf) }}" class="btn btn-outline-success btn-sm flex-fill" download>
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-tablet-alt fa-3x text-muted mb-3"></i>
                        <h4>No tienes compras online aún</h4>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="tab-pane fade" id="prestamo-fisico" role="tabpanel">
            <h4 class="mb-3">Préstamo Físico</h4>
            <div class="row">
                @forelse($prestamoFisico as $libro)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-lg border-info">
                            <div class="card-img-top text-center" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                @if($libro->imagen_portada)
                                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" class="img-fluid" alt="{{ $libro->titulo }}" style="max-height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $libro->titulo }}</h5>
                                <p class="card-text text-muted mb-1"><i class="fas fa-user me-1"></i> {{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                <div class="alert alert-warning mt-2">Puedes recoger tu libro en la biblioteca.</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                        <h4>No tienes préstamos físicos aún</h4>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="tab-pane fade" id="prestamo-online" role="tabpanel">
            <h4 class="mb-3">Préstamo Online</h4>
            <div class="row">
                @forelse($prestamoOnline as $libro)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-lg border-info">
                            <div class="card-img-top text-center" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                @if($libro->imagen_portada)
                                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" class="img-fluid" alt="{{ $libro->titulo }}" style="max-height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $libro->titulo }}</h5>
                                <p class="card-text text-muted mb-1"><i class="fas fa-user me-1"></i> {{ $libro->autor->nombre }} {{ $libro->autor->apellido }}</p>
                                <div class="alert alert-info mt-2">Puedes leer tu libro online durante el periodo de préstamo.</div>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="{{ route('books.read', $libro) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-book-open"></i> Leer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-laptop fa-3x text-muted mb-3"></i>
                        <h4>No tienes préstamos online aún</h4>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 