@extends('layouts.app')
@section('title', 'Mis Compras')
@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <h2 class="fw-bold mb-4 text-center">Mis Compras</h2>
    <div class="row">
        @forelse($compras as $compra)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-lg animate__animated animate__fadeInUp border-success">
                    <div class="card-img-top text-center" style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        @if($compra->libro->imagen_portada)
                            <img src="{{ $compra->libro->imagen_portada }}" class="img-fluid" alt="{{ $compra->libro->titulo }}" style="max-height: 100%; object-fit: cover;">
                        @else
                            <i class="fas fa-book fa-3x text-muted"></i>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $compra->libro->titulo }}</h5>
                        <p class="card-text text-muted mb-1"><i class="fas fa-calendar me-1"></i> {{ $compra->created_at->format('d/m/Y') }}</p>
                        <div class="mt-auto d-flex gap-2">
                            @if($compra->tipo === 'digital')
                                <a href="{{ $compra->libro->archivo_pdf }}" class="btn btn-outline-primary btn-sm flex-fill animate__animated animate__fadeInUp" download>
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            @elseif($compra->tipo === 'fisico')
                                <button class="btn btn-outline-info btn-sm flex-fill animate__animated animate__fadeInUp" onclick="verEstadoEnvio({{ $compra->id }})">
                                    <i class="fas fa-truck"></i> Ver Envío
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>No tienes compras registradas</h4>
            </div>
        @endforelse
    </div>
</div>
<script>
function verEstadoEnvio(id) {
    Swal.fire('Envío en proceso', 'Tu pedido está siendo procesado. Pronto recibirás información de envío.', 'info');
}
</script>
@endsection 