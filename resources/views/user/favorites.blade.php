@extends('layouts.app')

@section('title', 'Mis Favoritos - Retrolector')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Volver al Dashboard
        </a>
    </div>
    <h2 class="fw-bold mb-4 text-center">Mis Favoritos</h2>
    <div class="row">
        @forelse($favoritos as $favorito)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-lg animate__animated animate__fadeInUp">
                    <div class="card-img-top text-center" style="height: 220px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        @if($favorito->libro->imagen_portada)
                            <img src="{{ $favorito->libro->imagen_portada }}" class="img-fluid book-cover-hover" alt="{{ $favorito->libro->titulo }}" style="max-height: 100%; object-fit: cover; transition: transform 0.3s;">
                        @else
                            <i class="fas fa-book fa-3x text-muted"></i>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $favorito->libro->titulo }}</h5>
                        <p class="card-text text-muted mb-1"><i class="fas fa-user me-1"></i> {{ $favorito->libro->autor->nombre }} {{ $favorito->libro->autor->apellido }}</p>
                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('books.show', $favorito->libro) }}" class="btn btn-primary btn-sm flex-fill animate__animated animate__fadeInUp">
                                <i class="fas fa-eye me-1"></i> Ver
                            </a>
                            <button class="btn btn-outline-danger btn-sm flex-fill animate__animated animate__fadeInUp" onclick="quitarFavorito({{ $favorito->id }})">
                                <i class="fas fa-heart-broken"></i> Quitar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                <h4>No tienes libros favoritos aún</h4>
                <p class="text-muted">Explora el catálogo y añade tus favoritos.</p>
                <a href="{{ route('books.catalog') }}" class="btn btn-primary">Explorar Catálogo</a>
            </div>
        @endforelse
    </div>
</div>

<style>
.book-card {
    background: var(--bg-card);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.book-cover {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.book-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.book-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.book-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.book-card:hover .book-overlay {
    opacity: 1;
}

.book-info {
    padding: 1.5rem;
}

.book-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.book-author {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.book-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<script>
function quitarFavorito(id) {
    Swal.fire('Favorito eliminado', 'El libro ha sido quitado de tus favoritos.', 'info');
}
</script>
@endsection

@section('scripts')
<script>
    function refreshFavorites() {
        fetch(window.location.href + '?ajax=1')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGrid = doc.querySelector('.row');
                if (newGrid) {
                    document.querySelector('.row').innerHTML = newGrid.innerHTML;
                }
            });
    }
    setInterval(refreshFavorites, 30000);
</script>
@endsection 