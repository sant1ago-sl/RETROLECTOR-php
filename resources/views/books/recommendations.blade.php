@extends('layouts.app')

@section('title', 'Recomendaciones Personalizadas - Retrolector')

@section('content')
<div class="recommendations-container">
    <!-- Header de Recomendaciones -->
    <div class="recommendations-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="recommendations-title">
                        <i class="fas fa-magic me-3"></i>Recomendaciones Personalizadas
                    </h1>
                    <p class="recommendations-subtitle">Descubre libros perfectos para ti basados en tu historial de lectura</p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="recommendations-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $recomendaciones->count() }}</span>
                            <span class="stat-label">Recomendaciones</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $libros_populares_categoria->count() }}</span>
                            <span class="stat-label">Populares</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Sección de Recomendaciones Personalizadas -->
        <div class="recommendations-section">
            <div class="section-header">
                <h2><i class="fas fa-brain me-2"></i>Basado en tu Historial</h2>
                <p>Libros seleccionados especialmente para ti usando algoritmos de inteligencia artificial</p>
            </div>

            @if($recomendaciones->count() > 0)
                <div class="books-grid">
                    @foreach($recomendaciones as $libro)
                        @php $promedio = $libro->promedio_rating ?? 0; @endphp
                        <div class="book-card recommendation-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="recommendation-badge">
                                <i class="fas fa-star"></i>
                                <span>Recomendado</span>
                            </div>
                            
                            <div class="book-cover">
                                @if($libro->imagen_portada)
                                    <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="{{ $libro->titulo }}" class="book-image">
                                @else
                                    <div class="book-cover-placeholder">Sin Portada</div>
                                @endif
                                
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

                                <div class="book-badges">
                                    @if($promedio >= 4.5)
                                        <span class="badge bg-warning">Top Rated</span>
                                    @endif
                                    @if($libro->anio_publicacion && $libro->anio_publicacion >= date('Y') - 1)
                                        <span class="badge bg-success">Nuevo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="book-info">
                                <h5 class="book-title">{{ Str::limit($libro->titulo, 40) }}</h5>
                                <p class="book-author">{{ $libro->autor->nombre ?? 'Autor' }}</p>
                                
                                <div class="book-meta">
                                    <span class="book-category">{{ $libro->categoria->nombre ?? 'Categoría' }}</span>
                                    @if($libro->anio_publicacion)
                                        <span class="book-year">{{ $libro->anio_publicacion }}</span>
                                    @endif
                                </div>

                                <div class="book-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $promedio ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="rating-text">({{ number_format($promedio, 1) }})</span>
                                </div>

                                <div class="recommendation-reason">
                                    <i class="fas fa-lightbulb text-info"></i>
                                    <span>Similar a libros que has leído</span>
                                </div>

                                <div class="book-status">
                                    @if($libro->estado === 'disponible')
                                        <span class="status available">
                                            <i class="fas fa-check-circle"></i> Disponible
                                        </span>
                                    @else
                                        <span class="status unavailable">
                                            <i class="fas fa-times-circle"></i> No disponible
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-recommendations">
                    <div class="no-recommendations-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>No hay recomendaciones aún</h3>
                    <p>Comienza a leer libros para recibir recomendaciones personalizadas</p>
                    <a href="{{ route('books.catalog') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Explorar Catálogo
                    </a>
                </div>
            @endif
        </div>

        <!-- Sección de Libros Populares en Categorías Favoritas -->
        @if($libros_populares_categoria->count() > 0)
            <div class="popular-category-section">
                <div class="section-header">
                    <h2><i class="fas fa-fire me-2"></i>Populares en tus Categorías Favoritas</h2>
                    <p>Libros más prestados en las categorías que más te gustan</p>
                </div>

                <div class="books-grid">
                    @foreach($libros_populares_categoria as $libro)
                        @php $promedio = $libro->promedio_rating ?? 0; @endphp
                        <div class="book-card popular-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="popular-badge">
                                <i class="fas fa-fire"></i>
                                <span>Popular</span>
                            </div>
                            
                            <div class="book-cover">
                                <img src="{{ $libro->imagen_portada ?? 'https://via.placeholder.com/200x300' }}" 
                                     alt="{{ $libro->titulo }}" class="book-image">
                                
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

                                <div class="book-badges">
                                    @if($promedio >= 4.5)
                                        <span class="badge bg-warning">Top Rated</span>
                                    @endif
                                    @if($libro->anio_publicacion && $libro->anio_publicacion >= date('Y') - 1)
                                        <span class="badge bg-success">Nuevo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="book-info">
                                <h5 class="book-title">{{ Str::limit($libro->titulo, 40) }}</h5>
                                <p class="book-author">{{ $libro->autor->nombre ?? 'Autor' }}</p>
                                
                                <div class="book-meta">
                                    <span class="book-category">{{ $libro->categoria->nombre ?? 'Categoría' }}</span>
                                    @if($libro->anio_publicacion)
                                        <span class="book-year">{{ $libro->anio_publicacion }}</span>
                                    @endif
                                </div>

                                <div class="book-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $promedio ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="rating-text">({{ number_format($promedio, 1) }})</span>
                                </div>

                                <div class="popularity-info">
                                    <i class="fas fa-users text-success"></i>
                                    <span>{{ $libro->prestamos_count }} préstamos</span>
                                </div>

                                <div class="book-status">
                                    @if($libro->estado === 'disponible')
                                        <span class="status available">
                                            <i class="fas fa-check-circle"></i> Disponible
                                        </span>
                                    @else
                                        <span class="status unavailable">
                                            <i class="fas fa-times-circle"></i> No disponible
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Sección de Estadísticas de Lectura -->
        <div class="reading-stats-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-bar me-2"></i>Tu Perfil de Lectura</h2>
                <p>Estadísticas y análisis de tus preferencias de lectura</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ Auth::user()->prestamos()->count() }}</h3>
                        <p>Libros Leídos</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ Auth::user()->favoritos()->count() }}</h3>
                        <p>Favoritos</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ Auth::user()->resenas()->count() }}</h3>
                        <p>Reseñas Escritas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ Auth::user()->prestamos()->where('estado', 'prestado')->count() }}</h3>
                        <p>Préstamos Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Acciones Rápidas -->
        <div class="quick-actions-section">
            <div class="section-header">
                <h2><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h2>
                <p>Accede rápidamente a las funcionalidades más usadas</p>
            </div>

            <div class="actions-grid">
                <a href="{{ route('books.catalog') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Explorar Catálogo</h4>
                    <p>Descubre nuevos libros</p>
                </a>

                <a href="{{ route('user.loans') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Mis Préstamos</h4>
                    <p>Gestiona tus préstamos</p>
                </a>

                <a href="{{ route('user.favorites') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Mis Favoritos</h4>
                    <p>Libros guardados</p>
                </a>

                <a href="{{ route('user.history') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4>Historial</h4>
                    <p>Libros leídos</p>
                </a>
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
.recommendations-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.recommendations-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 3rem 0;
    margin-bottom: 3rem;
    color: white;
}

.recommendations-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.recommendations-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.recommendations-stats {
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

.recommendations-section,
.popular-category-section,
.reading-stats-section,
.quick-actions-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    color: var(--dark-color);
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.section-header p {
    color: #6c757d;
    font-size: 1.1rem;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
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

.recommendation-card {
    border: 2px solid #ffc107;
}

.popular-card {
    border: 2px solid #dc3545;
}

.recommendation-badge,
.popular-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(45deg, #ffc107, #ff8c00);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.popular-badge {
    background: linear-gradient(45deg, #dc3545, #c82333);
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

.book-cover-placeholder {
    width: 100%;
    height: 300px;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
    font-size: 1.5rem;
    font-weight: bold;
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

.recommendation-reason,
.popularity-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
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

.no-recommendations {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.no-recommendations-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-recommendations h3 {
    color: var(--dark-color);
    margin-bottom: 1rem;
}

.no-recommendations p {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}

.stat-content p {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.action-card {
    background: white;
    border: 2px solid #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    text-decoration: none;
    color: var(--dark-color);
    transition: all 0.3s ease;
}

.action-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    color: var(--dark-color);
}

.action-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

.action-card h4 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.action-card p {
    color: #6c757d;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .recommendations-title {
        font-size: 2rem;
    }
    
    .recommendations-stats {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .books-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
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
            window.location.href = `/books/${libroId}/request-loan`;
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
            window.location.href = `/books/${libroId}/reserve`;
        };
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

// Animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
    // Animar estadísticas
    const statNumbers = document.querySelectorAll('.stat-content h3');
    statNumbers.forEach(stat => {
        const finalNumber = parseInt(stat.textContent);
        let currentNumber = 0;
        const increment = finalNumber / 50;
        
        const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(currentNumber);
        }, 50);
    });
});
</script>
@endpush 