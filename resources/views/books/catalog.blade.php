@extends('layouts.app')

@section('title', 'Catálogo de Libros - Retrolector')

@section('content')
<div class="catalog-container">
    {{-- Encabezado eliminado para un diseño más limpio --}}
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar de Filtros -->
            <div class="col-lg-3">
                <div class="filters-sidebar">
                    <div class="filters-header">
                        <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="clearFilters()">
                            <i class="fas fa-times"></i>Limpiar
                        </button>
                                </div>

                    <form id="filtersForm" method="GET" action="{{ route('books.catalog') }}">
                        <!-- Búsqueda -->
                        <div class="filter-section">
                            <label class="filter-label">Búsqueda</label>
                            <div class="search-box">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       class="form-control" placeholder="Buscar libros, autores...">
                                <i class="fas fa-search search-icon"></i>
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div class="filter-section">
                            <label class="filter-label">Categorías</label>
                            <select name="categoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" 
                                            {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        <!-- Autores -->
                        <div class="filter-section">
                            <label class="filter-label">Autores</label>
                            <select name="autor" class="form-select">
                                <option value="">Todos los autores</option>
                                    @foreach($autores as $autor)
                                    <option value="{{ $autor->id }}" 
                                            {{ request('autor') == $autor->id ? 'selected' : '' }}>
                                        {{ $autor->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        <!-- Año de publicación -->
                        <div class="filter-section">
                            <label class="filter-label">Año de publicación</label>
                            <select name="anio" class="form-select">
                                <option value="">Todos los años</option>
                                @foreach($anios as $anio)
                                    <option value="{{ $anio }}" 
                                            {{ request('anio') == $anio ? 'selected' : '' }}>
                                        {{ $anio }}
                                    </option>
                                @endforeach
                                </select>
                            </div>

                        <!-- Rating -->
                        <div class="filter-section">
                            <label class="filter-label">Rating mínimo</label>
                            <div class="rating-filter">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                           id="rating_{{ $i }}" 
                                           {{ request('rating') == $i ? 'checked' : '' }}>
                                    <label for="rating_{{ $i }}" class="rating-star">
                                        <i class="fas fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Idioma -->
                        <div class="filter-section">
                            <label class="filter-label">Idioma</label>
                            <select name="idioma" class="form-select">
                                <option value="">Todos los idiomas</option>
                                @foreach($idiomas as $idioma)
                                    <option value="{{ $idioma }}" 
                                            {{ request('idioma') == $idioma ? 'selected' : '' }}>
                                        {{ ucfirst($idioma) }}
                                    </option>
                                @endforeach
                            </select>
                            </div>

                        <!-- Ordenamiento -->
                        <div class="filter-section">
                            <label class="filter-label">Ordenar por</label>
                            <select name="orden" class="form-select mb-2">
                                <option value="titulo" {{ request('orden') == 'titulo' ? 'selected' : '' }}>Título</option>
                                <option value="rating" {{ request('orden') == 'rating' ? 'selected' : '' }}>Rating</option>
                                <option value="anio_publicacion" {{ request('orden') == 'anio_publicacion' ? 'selected' : '' }}>Fecha</option>
                                <option value="precio" {{ request('orden') == 'precio' ? 'selected' : '' }}>Precio</option>
                                <option value="popularidad" {{ request('orden') == 'popularidad' ? 'selected' : '' }}>Popularidad</option>
                            </select>
                            <select name="direccion" class="form-select">
                                <option value="asc" {{ request('direccion') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direccion') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            </select>
                            </div>

                        <!-- Elementos por página -->
                        <div class="filter-section">
                            <label class="filter-label">Elementos por página</label>
                            <select name="per_page" class="form-select">
                                <option value="12" {{ request('per_page') == '12' ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == '24' ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page') == '48' ? 'selected' : '' }}>48</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Aplicar Filtros
                        </button>
                    </form>
    </div>

                <!-- Libros Populares -->
                <div class="popular-books-sidebar">
                    <h5><i class="fas fa-fire me-2"></i>Libros Populares</h5>
                    @foreach($libros_populares as $libro)
                        @php
                            $isUrl = $libro->imagen_portada && (str_starts_with($libro->imagen_portada, 'http://') || str_starts_with($libro->imagen_portada, 'https://'));
                        @endphp
                        <div class="popular-book-item">
                            @if($libro->imagen_portada)
                                <img src="{{ $isUrl ? $libro->imagen_portada : asset('storage/' . $libro->imagen_portada) }}"
                                     alt="{{ $libro->titulo }}" class="popular-book-cover">
                            @else
                                <img src="https://via.placeholder.com/60x80" alt="Sin portada" class="popular-book-cover">
                            @endif
                            <div class="popular-book-info">
                                <h6>{{ Str::limit($libro->titulo, 30) }}</h6>
                                <p>{{ $libro->autor->nombre ?? 'Autor' }}</p>
                                <div class="popular-book-stats">
                                    <span class="rating">
                                        @php $promedio = $libro->promedio_rating ?? 0; @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $promedio ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </span>
                                    <span class="prestamos">{{ $libro->prestamos_count }} préstamos</span>
                </div>
            </div>
        </div>
                    @endforeach
                </div>

                <!-- Libros Recientes -->
                <div class="recent-books-sidebar">
                    <h5><i class="fas fa-clock me-2"></i>Libros Recientes</h5>
                    @foreach($libros_recientes as $libro)
                        @php
                            $isUrl = $libro->imagen_portada && (str_starts_with($libro->imagen_portada, 'http://') || str_starts_with($libro->imagen_portada, 'https://'));
                        @endphp
                        <div class="recent-book-item">
                            @if($libro->imagen_portada)
                                <img src="{{ $isUrl ? $libro->imagen_portada : asset('storage/' . $libro->imagen_portada) }}"
                                     alt="{{ $libro->titulo }}" class="recent-book-cover">
                            @else
                                <img src="https://via.placeholder.com/60x80" alt="Sin portada" class="recent-book-cover">
                            @endif
                            <div class="recent-book-info">
                                <h6>{{ Str::limit($libro->titulo, 30) }}</h6>
                                <p>{{ $libro->autor->nombre ?? 'Autor' }}</p>
                                <small class="text-muted">{{ $libro->anio_publicacion ? $libro->anio_publicacion : 'N/A' }}</small>
            </div>
        </div>
                    @endforeach
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-lg-9">
                <!-- Barra de herramientas -->
                <div class="catalog-toolbar">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="results-info">
                                <span class="results-count">{{ $libros->total() }} resultados</span>
                                @if(request()->hasAny(['search', 'categoria', 'autor', 'anio', 'rating', 'idioma']))
                                    <span class="filters-applied">con filtros aplicados</span>
                                @endif
                </div>
            </div>
                        <div class="col-md-6">
                            <div class="toolbar-actions">
                                <!-- Vista -->
                                <div class="view-toggle">
                                    <button class="btn btn-sm {{ $vista == 'grid' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                            onclick="setView('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                                    <button class="btn btn-sm {{ $vista == 'list' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                            onclick="setView('list')">
                                <i class="fas fa-list"></i>
                            </button>
                                </div>

                                <!-- Búsqueda rápida -->
                                <div class="quick-search">
                                    <input type="text" id="quickSearch" class="form-control form-control-sm" 
                                           placeholder="Búsqueda rápida...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados de búsqueda -->
                <div class="search-results" id="searchResults">
                    @if($libros->count() > 0)
                        <div class="books-container {{ $vista == 'grid' ? 'books-grid' : 'books-list' }}">
                    @foreach($libros as $libro)
                        @php
                            $isNuevo = \Carbon\Carbon::parse($libro->created_at)->gt(now()->subDays(7));
                            $esMio = auth()->check() && $libro->creado_por == auth()->id();
                        @endphp
                        <div class="book-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="book-cover">
                                @php
                                    $isUrl = $libro->imagen_portada && (str_starts_with($libro->imagen_portada, 'http://') || str_starts_with($libro->imagen_portada, 'https://'));
                                @endphp
                                @if($libro->imagen_portada)
                                    <img src="{{ $isUrl ? $libro->imagen_portada : asset('storage/' . $libro->imagen_portada) }}"
                                         alt="{{ $libro->titulo }}"
                                         class="book-image"
                                         onerror="this.onerror=null;this.outerHTML='<div class=\'book-cover-placeholder\'>
                                         <svg width=\'60\' height=\'80\' viewBox=\'0 0 60 80\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><rect width=\'60\' height=\'80\' rx=\'8\' fill=\'#e3e6ed\'/><rect x=\'10\' y=\'20\' width=\'40\' height=\'8\' rx=\'2\' fill=\'#b0b8c9\'/><rect x=\'10\' y=\'35\' width=\'40\' height=\'6\' rx=\'2\' fill=\'#b0b8c9\'/><rect x=\'10\' y=\'48\' width=\'25\' height=\'5\' rx=\'2\' fill=\'#b0b8c9\'/></svg>\n<small class=\'text-muted\'>Sin Portada</small></div>';">
                                @else
                                    <div class="book-cover-placeholder">
                                        <svg width="60" height="80" viewBox="0 0 60 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="60" height="80" rx="8" fill="#e3e6ed"/>
                                            <rect x="10" y="20" width="40" height="8" rx="2" fill="#b0b8c9"/>
                                            <rect x="10" y="35" width="40" height="6" rx="2" fill="#b0b8c9"/>
                                            <rect x="10" y="48" width="25" height="5" rx="2" fill="#b0b8c9"/>
                                        </svg>
                                        <small class="text-muted">Sin Portada</small>
                                    </div>
                                @endif
                                
                                <!-- Overlay de acciones -->
                                <div class="book-overlay">
                                    <div class="book-actions">
                                        <a href="{{ route('books.show', $libro->id) }}" 
                                           class="btn btn-primary btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @auth
                                            <button class="btn btn-outline-light btn-sm toggle-favorite" 
                                                    data-libro-id="{{ $libro->id }}"
                                                    title="{{ $libro->is_favorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                                <i class="fas fa-heart {{ $libro->is_favorite ? 'text-danger' : '' }}"></i>
                                            </button>
                                            
                                            @if($libro->estado === 'disponible')
                                                <button class="btn btn-success btn-sm request-loan" 
                                                        data-libro-id="{{ $libro->id }}"
                                                        title="Solicitar préstamo">
                                                    <i class="fas fa-book"></i>
                                                </button>
                                    @else
                                                        <button class="btn btn-warning btn-sm reserve-book" 
                                                                data-libro-id="{{ $libro->id }}"
                                                                title="Reservar libro">
                                                            <i class="fas fa-bookmark"></i>
                                                        </button>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    
                                    <!-- Badges -->
                                        <div class="book-badges">
                                            @if($libro->promedio_rating >= 4.5)
                                                <span class="badge bg-warning">Top Rated</span>
                                    @endif
                                            @if($isNuevo)
                                                <span class="badge bg-success">Nuevo</span>
                                            @endif
                                    </div>
                                </div>
                                
                                    <div class="book-info">
                                        <h5 class="book-title">{{ $libro->titulo }}</h5>
                                        <div class="book-author mb-1">
                                            <i class="fas fa-user me-1"></i>{{ $libro->autor->nombre ?? 'Autor' }}
                                        </div>
                                        <div class="book-meta mb-2">
                                            <span class="badge bg-secondary"><i class="fas fa-map-marker-alt me-1"></i>{{ $libro->ubicacion ?? 'Sin ubicación' }}</span>
                                        </div>
                                        <div class="book-prices mb-2">
                                            <div class="row g-1">
                                                {{-- Mostrar precios solo si existen y son mayores a cero --}}
                                                @if(isset($libro->precio_compra_fisica) && $libro->precio_compra_fisica > 0)
                                                    <div class="col-6">
                                                        <div class="price-card bg-light border rounded p-1 mb-1">
                                                            <small class="text-muted">Compra Física</small><br>
                                                            <span class="fw-bold">S/ {{ number_format($libro->precio_compra_fisica, 2) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(isset($libro->precio_compra_online) && $libro->precio_compra_online > 0)
                                                    <div class="col-6">
                                                        <div class="price-card bg-light border rounded p-1 mb-1">
                                                            <small class="text-muted">Compra Online</small><br>
                                                            <span class="fw-bold">S/ {{ number_format($libro->precio_compra_online, 2) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(isset($libro->precio_prestamo_fisico) && $libro->precio_prestamo_fisico > 0)
                                                    <div class="col-6">
                                                        <div class="price-card bg-light border rounded p-1 mb-1">
                                                            <small class="text-muted">Préstamo Físico</small><br>
                                                            <span class="fw-bold">S/ {{ number_format($libro->precio_prestamo_fisico, 2) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(isset($libro->precio_prestamo_online) && $libro->precio_prestamo_online > 0)
                                                    <div class="col-6">
                                                        <div class="price-card bg-light border rounded p-1 mb-1">
                                                            <small class="text-muted">Préstamo Online</small><br>
                                                            <span class="fw-bold">S/ {{ number_format($libro->precio_prestamo_online, 2) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="book-actions mt-2">
                                            <a href="{{ route('books.show', $libro->id) }}" class="btn btn-outline-primary btn-sm me-1">
                                                <i class="fas fa-eye"></i> Ver detalles
                                            </a>
                                            <a href="{{ route('books.read', $libro->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-book-open"></i> Vista previa
                                            </a>
                                            {{-- Eliminar la tarjeta especial para el admin creador del libro --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginación -->
                        <div class="pagination-container">
                            {{ $libros->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="no-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>No se encontraron resultados</h3>
                            <p>Intenta ajustar tus filtros de búsqueda o explorar nuestras categorías.</p>
                            <div class="no-results-actions">
                                <button class="btn btn-primary" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                                </button>
                                <a href="{{ route('books.catalog') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-2"></i>Ver Todo el Catálogo
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
                </div>

<!-- Modal de confirmación de préstamo -->
<div class="modal fade" id="loanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres solicitar el préstamo de este libro?</p>
                <div class="loan-details">
                    <h6 id="loanBookTitle"></h6>
                    <p id="loanBookAuthor"></p>
                    <p><strong>Fecha de devolución:</strong> <span id="loanReturnDate"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmLoan">Confirmar Préstamo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de reserva -->
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres reservar este libro?</p>
                <div class="reserve-details">
                    <h6 id="reserveBookTitle"></h6>
                    <p id="reserveBookAuthor"></p>
                    <p>Te notificaremos cuando el libro esté disponible.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="confirmReserve">Confirmar Reserva</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.catalog-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.catalog-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.catalog-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.catalog-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.catalog-stats {
    display: flex;
    gap: 2rem;
    justify-content: flex-end;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 900;
    background: linear-gradient(45deg, #f093fb, #f5576c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.filters-sidebar {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.filters-header h5 {
    margin: 0;
    color: var(--dark-color);
    font-weight: 700;
}

.filter-section {
    margin-bottom: 1.5rem;
}

.filter-label {
    display: block;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.rating-filter {
    display: flex;
    gap: 0.5rem;
}

.rating-star {
    cursor: pointer;
    color: #dee2e6;
    transition: color 0.3s ease;
}

.rating-star:hover,
.rating-star.active {
    color: #ffc107;
}

.price-range input {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.popular-books-sidebar,
.recent-books-sidebar {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.popular-books-sidebar h5,
.recent-books-sidebar h5 {
    color: var(--dark-color);
    font-weight: 700;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f8f9fa;
}

.popular-book-item,
.recent-book-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.popular-book-item:last-child,
.recent-book-item:last-child {
    border-bottom: none;
}

.popular-book-cover,
.recent-book-cover {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.popular-book-info h6,
.recent-book-info h6 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--dark-color);
}

.popular-book-info p,
.recent-book-info p {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.popular-book-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
}

.rating .fas {
    font-size: 0.7rem;
}

.catalog-toolbar {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.results-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.results-count {
    font-weight: 700;
    color: var(--dark-color);
}

.filters-applied {
    font-size: 0.9rem;
    color: #6c757d;
}

.toolbar-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.quick-search {
    width: 250px;
}

.books-container {
    margin-bottom: 2rem;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.books-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

    .book-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    position: relative;
    }

    .book-card:hover {
        transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.book-cover {
    position: relative;
    overflow: hidden;
}

.book-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
    }

.book-card:hover .book-image {
    transform: scale(1.05);
}

.book-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.book-card:hover .book-overlay {
        opacity: 1;
    }

.book-actions {
    display: flex;
    gap: 0.5rem;
    }

.book-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    }

.book-badges .badge {
        font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    }

.book-info {
    padding: 1.5rem;
}

.book-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.book-author {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
}

.book-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.book-category,
.book-year {
    font-size: 0.8rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    }

.book-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.book-rating .fas {
    font-size: 0.8rem;
}

.rating-text {
    font-size: 0.8rem;
    color: #6c757d;
}

.book-price {
    margin-bottom: 0.75rem;
    }

.price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--success-color);
}

.book-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status {
    font-size: 0.8rem;
    font-weight: 600;
    }

.status.available {
    color: var(--success-color);
}

.status.unavailable {
    color: var(--danger-color);
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.no-results-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.no-results h3 {
    color: var(--dark-color);
    margin-bottom: 1rem;
    }

.no-results p {
    color: #6c757d;
    margin-bottom: 2rem;
}

.no-results-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    }

/* Responsive */
    @media (max-width: 768px) {
    .catalog-title {
        font-size: 2rem;
    }
    
    .catalog-stats {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .books-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .toolbar-actions {
            flex-direction: column;
        align-items: stretch;
        }
        
    .quick-search {
            width: 100%;
        }
    }
</style>
{{-- Mejorar el CSS para que use variables de Bootstrap y se adapte al tema --}}
<style>
:root, [data-bs-theme="light"] {
    --bs-card-bg: #fff;
    --bs-body-color: #222;
    --bs-secondary-color: #666;
    --bs-light: #f1f1f1;
}
[data-bs-theme="dark"] {
    --bs-card-bg: #23272b;
    --bs-body-color: #f1f1f1;
    --bs-secondary-color: #bbb;
    --bs-light: #23272b;
}
.catalog-container, .books-container, .book-card, .filters-sidebar, .popular-books-sidebar, .recent-books-sidebar {
    background: var(--bs-card-bg) !important;
    color: var(--bs-body-color) !important;
}
</style>
@endpush

@push('scripts')
<script>
// Búsqueda en tiempo real
let searchTimeout;
document.getElementById('quickSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    
    searchTimeout = setTimeout(() => {
        if (query.length >= 2) {
            fetch(`{{ route('books.search-api') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                });
        } else {
            hideSearchResults();
        }
    }, 300);
});

function displaySearchResults(results) {
    const container = document.getElementById('searchResults');
    if (results.length === 0) {
        container.innerHTML = `
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No se encontraron resultados</h3>
                <p>Intenta con otros términos de búsqueda.</p>
            </div>
        `;
        return;
        }
    
    let html = '<div class="books-container books-grid">';
    results.forEach(book => {
        html += `
            <div class="book-card" data-aos="fade-up">
                <div class="book-cover">
                    <img src="https://via.placeholder.com/200x300" alt="${book.titulo}" class="book-image">
                    <div class="book-overlay">
                        <div class="book-actions">
                            <a href="${book.url}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="book-info">
                    <h5 class="book-title">${book.titulo}</h5>
                    <p class="book-author">${book.autor}</p>
                    <div class="book-meta">
                        <span class="book-category">${book.categoria}</span>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function hideSearchResults() {
    // Recargar la página para mostrar todos los resultados
    window.location.reload();
    }

// Cambiar vista
function setView(view) {
    const container = document.querySelector('.books-container');
    container.className = `books-container books-${view}`;
    
    // Guardar preferencia
    localStorage.setItem('catalogView', view);
    
    // Actualizar botones
    document.querySelectorAll('.view-toggle .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
}

// Limpiar filtros
function clearFilters() {
    window.location.href = '{{ route('books.catalog') }}';
    }

// Toggle favorito
document.addEventListener('click', function(e) {
    if (e.target.closest('.toggle-favorite')) {
        const button = e.target.closest('.toggle-favorite');
        const libroId = button.dataset.libroId;
        
        fetch(`/user/favorites/${libroId}/toggle`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        const icon = button.querySelector('i');
        if (data.action === 'agregado') {
            icon.classList.add('text-danger');
            button.title = 'Quitar de favoritos';
        } else {
            icon.classList.remove('text-danger');
            button.title = 'Agregar a favoritos';
        }
        showNotification(data.message, 'success');
    }
});
    }
});

// Solicitar préstamo
document.addEventListener('click', function(e) {
    if (e.target.closest('.request-loan')) {
        const button = e.target.closest('.request-loan');
        const libroId = button.dataset.libroId;
        
        // Mostrar modal de confirmación
        const modal = new bootstrap.Modal(document.getElementById('loanModal'));
        modal.show();
        
        // Configurar confirmación
        document.getElementById('confirmLoan').onclick = function() {
            window.location.href = `{{ url('books') }}/${libroId}/request-loan`;
        };
    }
});

// Reservar libro
document.addEventListener('click', function(e) {
    if (e.target.closest('.reserve-book')) {
        const button = e.target.closest('.reserve-book');
        const libroId = button.dataset.libroId;
        
        // Mostrar modal de confirmación
        const modal = new bootstrap.Modal(document.getElementById('reserveModal'));
        modal.show();
        
        // Configurar confirmación
        document.getElementById('confirmReserve').onclick = function() {
            window.location.href = `{{ url('books') }}/${libroId}/reserve`;
        };
    }
});

// Rating filter
document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.previousElementSibling.value;
        
        // Actualizar estrellas visuales
        document.querySelectorAll('.rating-star').forEach(s => {
            s.classList.remove('active');
        });
        
        for (let i = 1; i <= rating; i++) {
            document.querySelector(`#rating_${i} + .rating-star`).classList.add('active');
        }
        
        // Enviar formulario
        document.getElementById('filtersForm').submit();
            });
        });

// Auto-submit en cambios de filtros
document.querySelectorAll('select[name="categoria"], select[name="autor"], select[name="anio"], select[name="idioma"]').forEach(select => {
    select.addEventListener('change', function() {
        document.getElementById('filtersForm').submit();
    });
});

// Cargar vista guardada
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('catalogView');
    if (savedView) {
        setView(savedView);
    }
});

// Mostrar notificación
function showNotification(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-dismiss
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush