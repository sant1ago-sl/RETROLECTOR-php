@extends('layouts.app')

@section('title', $libro->titulo . ' - Retrolector')

@section('content')
<div class="book-detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.catalog') }}">Catálogo</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.by-category', $libro->categoria->id) }}">{{ $libro->categoria->nombre }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($libro->titulo, 50) }}</li>
        </ol>
    </nav>
                        </div>
                    </div>

    <div class="container">
        <div class="row">
            <!-- Información principal del libro -->
            <div class="col-lg-8">
                <div class="book-main-info">
                    <div class="row">
                        <!-- Portada del libro -->
                        <div class="col-md-4">
                            <div class="book-cover-container">
                                @php
                                    $isUrl = $libro->imagen_portada && (str_starts_with($libro->imagen_portada, 'http://') || str_starts_with($libro->imagen_portada, 'https://'));
                                @endphp
                                @if($libro->imagen_portada)
                                    <img src="{{ $isUrl ? $libro->imagen_portada : asset('storage/' . $libro->imagen_portada) }}"
                                         alt="{{ $libro->titulo }}"
                                         class="img-fluid rounded shadow"
                                         onerror="this.onerror=null;this.outerHTML='<div class=\'book-cover-placeholder\'>
                                         <svg width=\'120\' height=\'160\' viewBox=\'0 0 60 80\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><rect width=\'60\' height=\'80\' rx=\'8\' fill=\'#e3e6ed\'/><rect x=\'10\' y=\'20\' width=\'40\' height=\'8\' rx=\'2\' fill=\'#b0b8c9\'/><rect x=\'10\' y=\'35\' width=\'40\' height=\'6\' rx=\'2\' fill=\'#b0b8c9\'/><rect x=\'10\' y=\'48\' width=\'25\' height=\'5\' rx=\'2\' fill=\'#b0b8c9\'/></svg>\n<small class=\'text-muted\'>Sin Portada</small></div>';">
                                @else
                                    <div class="book-cover-placeholder">
                                        <svg width="120" height="160" viewBox="0 0 60 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="60" height="80" rx="8" fill="#e3e6ed"/>
                                            <rect x="10" y="20" width="40" height="8" rx="2" fill="#b0b8c9"/>
                                            <rect x="10" y="35" width="40" height="6" rx="2" fill="#b0b8c9"/>
                                            <rect x="10" y="48" width="25" height="5" rx="2" fill="#b0b8c9"/>
                                        </svg>
                                        <small class="text-muted">Sin Portada</small>
                                    </div>
                                @endif
                                
                                <!-- Badges -->
                                <div class="book-badges">
                                    @php $promedio = $stats_libro['promedio_rating'] ?? 0; @endphp
                                    @if($promedio >= 4.5)
                                        <span class="badge bg-warning">Top Rated</span>
                                    @endif
                                    @if($libro->anio_publicacion && $libro->anio_publicacion >= date('Y') - 1)
                                        <span class="badge bg-success">Nuevo</span>
                @endif
                                    @if($libro->estado === 'disponible')
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">No disponible</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del libro -->
                        <div class="col-md-8">
            <div class="book-info">
                                <h1 class="book-title">{{ $libro->titulo }}</h1>
                
                                <div class="book-author">
                        <i class="fas fa-user me-2"></i>
                                    <a href="{{ route('books.by-author', $libro->autor->id) }}" class="author-link">
                                        {{ $libro->autor->nombre ?? 'Autor desconocido' }}
                                    </a>
                                </div>

                                <div class="book-rating-section">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $promedio ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="rating-text">({{ number_format($promedio, 1) }})</span>
                                    </div>
                                    <span class="rating-count">Basado en {{ $stats_libro['total_resenas'] }} reseñas</span>
                                </div>

                                <div class="book-price-section mb-3">
    <div class="row g-2">
        <div class="col-md-3 col-6">
            <div class="price-card bg-light border rounded p-2 text-center">
                <small class="text-muted">Compra Física</small><br>
                <span class="fw-bold">
                    @if($libro->precio_compra_fisica && $libro->precio_compra_fisica > 0)
                        S/ {{ number_format($libro->precio_compra_fisica, 2) }}
                    @else
                        No disponible
                    @endif
                </span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="price-card bg-light border rounded p-2 text-center">
                <small class="text-muted">Compra Online</small><br>
                <span class="fw-bold">
                    @if($libro->precio_compra_online && $libro->precio_compra_online > 0)
                        S/ {{ number_format($libro->precio_compra_online, 2) }}
                    @else
                        No disponible
                    @endif
                </span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="price-card bg-light border rounded p-2 text-center">
                <small class="text-muted">Préstamo Físico</small><br>
                <span class="fw-bold">
                    @if($libro->precio_prestamo_fisico && $libro->precio_prestamo_fisico > 0)
                        S/ {{ number_format($libro->precio_prestamo_fisico, 2) }}
                    @else
                        No disponible
                    @endif
                </span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="price-card bg-light border rounded p-2 text-center">
                <small class="text-muted">Préstamo Online</small><br>
                <span class="fw-bold">
                    @if($libro->precio_prestamo_online && $libro->precio_prestamo_online > 0)
                        S/ {{ number_format($libro->precio_prestamo_online, 2) }}
                    @else
                        No disponible
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
<div class="book-meta mb-3">
    <span class="badge bg-secondary"><i class="fas fa-map-marker-alt me-1"></i>Ubicación: {{ $libro->ubicacion ?? 'Sin ubicación' }}</span>
</div>
                                <div class="book-meta">
                                    <div class="meta-item">
                        <i class="fas fa-tag me-2"></i>
                                        <a href="{{ route('books.by-category', $libro->categoria->id) }}" class="category-link">
                                            {{ $libro->categoria->nombre ?? 'Sin categoría' }}
                        </a>
                                    </div>
                                    
                    @if($libro->anio_publicacion)
                                        <div class="meta-item">
                            <i class="fas fa-calendar me-2"></i>
                                            <span>{{ $libro->anio_publicacion }}</span>
                                        </div>
                    @endif
                                    
                                    @if($libro->idioma)
                                        <div class="meta-item">
                                            <i class="fas fa-language me-2"></i>
                                            <span>{{ ucfirst($libro->idioma) }}</span>
                                        </div>
                    @endif
                                    
                    @if($libro->isbn)
                                        <div class="meta-item">
                            <i class="fas fa-barcode me-2"></i>
                                            <span>{{ $libro->isbn }}</span>
                                        </div>
                    @endif
                                    
                    @if($libro->paginas)
                                        <div class="meta-item">
                            <i class="fas fa-file-alt me-2"></i>
                                            <span>{{ $libro->paginas }} páginas</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Acciones del libro -->
                                <div class="book-actions">
                                    @auth
                                        <!-- Botón de favoritos -->
                                        <button class="btn btn-outline-primary toggle-favorite" 
                                                data-libro-id="{{ $libro->id }}"
                                                title="{{ $es_favorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                            <i class="fas fa-heart {{ $es_favorito ? 'text-danger' : '' }}"></i>
                                            {{ $es_favorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}
                                        </button>

                                        <!-- Botones de acción según estado -->
                                        @if($prestamo_activo)
                                            <div class="loan-info">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Préstamo activo</strong><br>
                                                    Fecha de devolución: {{ $prestamo_activo->fecha_devolucion->format('d/m/Y') }}
                                                    @if($prestamo_activo->fecha_devolucion->isPast())
                                                        <span class="text-danger">(Vencido)</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('books.read', $libro->id) }}" class="btn btn-success">
                                                    <i class="fas fa-book-reader me-2"></i>Leer Libro
                                                </a>
                                            </div>
                                        @elseif($reserva_activa)
                                            <div class="reservation-info">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <strong>Libro reservado</strong><br>
                                                    Fecha de reserva: {{ $reserva_activa->fecha_reserva->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        @endif

                                        @if(auth()->user()->isAdmin())
                                            <button class="btn btn-danger" disabled title="Solo los clientes pueden solicitar libros">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Solo los clientes pueden solicitar libros
                                            </button>
                                        @else
                                            @if($libro->estado === 'disponible')
                                                <a href="{{ route('books.purchase', $libro->id) }}" class="btn btn-success">
                                                    <i class="fas fa-shopping-cart me-2"></i>Comprar Libro
                                                </a>
                                            @endif
                                        @endif
                                        <!-- Botón de vista previa para todos -->
                                        <a href="{{ route('books.read', $libro->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye me-2"></i>Vista Previa
                                        </a>
                                    @else
                                        <div class="auth-required">
                                            <p class="text-muted">Inicia sesión para interactuar con este libro</p>
                                            <a href="{{ route('login') }}" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                            </a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Descripción del libro -->
                @if($libro->descripcion)
                    <div class="book-description">
                        <h3>Descripción</h3>
                        <div class="description-content">
                            {!! nl2br(e($libro->descripcion)) !!}
                        </div>
                    </div>
                @endif

                <!-- Estadísticas del libro -->
                <div class="book-stats">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ $stats_libro['total_prestamos'] }}</span>
                                    <span class="stat-label">Préstamos</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ $stats_libro['total_reservas'] }}</span>
                                    <span class="stat-label">Reservas</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ $stats_libro['total_favoritos'] }}</span>
                                    <span class="stat-label">Favoritos</span>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-eye"></i>
                    </div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ number_format($libro->vistas) }}</span>
                                    <span class="stat-label">Vistas</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>

            <!-- Reseñas -->
                <div class="book-reviews">
                    <div class="reviews-header d-flex justify-content-between align-items-center">
                        <h3>Reseñas ({{ $stats_libro['total_resenas'] }})</h3>
                    </div>
                    @if($resenas->count() > 0)
                        <div class="reviews-list mb-4">
                            @foreach($resenas as $resena)
                                <div class="review-item card mb-3 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ $resena->usuario->avatar ?? 'https://via.placeholder.com/40x40' }}" alt="{{ $resena->usuario->nombre }}" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <strong>{{ $resena->usuario->nombre }}</strong>
                                            @if(method_exists($resena->usuario, 'isAdmin') && $resena->usuario->isAdmin())
                                                <span class="badge bg-danger ms-2">Admin</span>
                                            @endif
                                            <div class="review-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $resena->calificacion ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="ms-auto text-muted small">{{ $resena->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="review-content ps-5">
                                        <p class="mb-0">{{ $resena->comentario }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="reviews-pagination mb-4">
                            {{ $resenas->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted mb-4">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p>No hay reseñas para este libro. ¡Sé el primero en escribir una!</p>
                        </div>
                    @endif
                    @auth
                    <div class="card p-4 shadow-sm mb-4">
                        <h5 class="mb-3"><i class="fas fa-pen me-2"></i>Escribe tu reseña</h5>
                        <form action="{{ route('user.create-review', $libro->id) }}" method="POST">
                            @csrf
                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-2 mb-0">Calificación:</label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating_{{ $i }}" required>
                                        <label for="rating_{{ $i }}" class="rating-star">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="comentario" rows="3" placeholder="Comparte tu opinión sobre este libro..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Reseña
                                </button>
                            </div>
                        </form>
                    </div>
                    @endauth
                    @guest
                    <div class="alert alert-info text-center mt-4">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <a href="{{ route('login') }}" class="fw-bold">Inicia sesión</a> para dejar tu reseña.
                    </div>
                    @endguest
                </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Libros relacionados -->
                <div class="related-books">
                    <h4>Libros Relacionados</h4>
                    @foreach($libros_relacionados as $libro_relacionado)
                        <div class="related-book-item">
                            @php
                                $isUrl = $libro_relacionado->imagen_portada && (str_starts_with($libro_relacionado->imagen_portada, 'http://') || str_starts_with($libro_relacionado->imagen_portada, 'https://'));
                            @endphp
                            @if($libro_relacionado->imagen_portada)
                                <img src="{{ $isUrl ? $libro_relacionado->imagen_portada : asset('storage/' . $libro_relacionado->imagen_portada) }}"
                                     alt="{{ $libro_relacionado->titulo }}"
                                     class="related-book-cover"
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
                            <div class="related-book-info">
                                <h6><a href="{{ route('books.show', $libro_relacionado->id) }}">{{ $libro_relacionado->titulo }}</a></h6>
                                <p>{{ $libro_relacionado->autor->nombre ?? 'Autor' }}</p>
                                <div class="related-book-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $libro_relacionado->promedio_rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span>({{ number_format($libro_relacionado->promedio_rating, 1) }})</span>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                <!-- Información adicional -->
                <div class="additional-info">
                    <h4>Información Adicional</h4>
                    
                    @if($libro->ubicacion)
                        <div class="info-item">
                            <strong>Ubicación en Biblioteca:</strong> {{ $libro->ubicacion }}
                        </div>
                    @endif
                    
                    @if($libro->editorial)
                        <div class="info-item">
                            <strong>Editorial:</strong> {{ $libro->editorial }}
                </div>
            @endif

                    @if($libro->edicion)
                        <div class="info-item">
                            <strong>Edición:</strong> {{ $libro->edicion }}
                </div>
                            @endif
                    
                    @if($libro->formato)
                        <div class="info-item">
                            <strong>Formato:</strong> {{ $libro->formato }}
                        </div>
                            @endif
                    
                    @if($libro->peso)
                        <div class="info-item">
                            <strong>Peso:</strong> {{ $libro->peso }}g
                        </div>
                    @endif
                    
                    @if($libro->dimensiones)
                        <div class="info-item">
                            <strong>Dimensiones:</strong> {{ $libro->dimensiones }}
                    </div>
                    @endif
                    
                    <div class="info-item">
                        <strong>Fecha de registro:</strong> {{ $libro->created_at->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Compartir -->
                <div class="share-section">
                    <h4>Compartir</h4>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                           target="_blank" class="btn btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($libro->titulo) }}" 
                           target="_blank" class="btn btn-twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                           target="_blank" class="btn btn-linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="mailto:?subject={{ urlencode($libro->titulo) }}&body={{ urlencode('Mira este libro: ' . request()->url()) }}" 
                           class="btn btn-email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
                </div>
            </div>

<!-- Modal de reseña -->
@auth
    @if(!$tiene_resena)
        <div class="modal fade" id="reviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Escribir Reseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('user.create-review', $libro->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Calificación</label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating_{{ $i }}" required>
                                        <label for="rating_{{ $i }}" class="rating-star">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comentario" class="form-label">Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="4" 
                                          placeholder="Comparte tu opinión sobre este libro..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
                    @endif
@endauth

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
                    <h6>{{ $libro->titulo }}</h6>
                    <p>{{ $libro->autor->nombre ?? 'Autor' }}</p>
                    <p><strong>Fecha de devolución:</strong> <span id="loanReturnDate"></span></p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Recordatorio:</strong> Los préstamos tienen una duración de 15 días y pueden renovarse hasta 2 veces.
                    </div>
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
                    <h6>{{ $libro->titulo }}</h6>
                    <p>{{ $libro->autor->nombre ?? 'Autor' }}</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Información:</strong> Te notificaremos cuando el libro esté disponible. Las reservas tienen prioridad sobre nuevos préstamos.
                    </div>
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
.book-detail-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.breadcrumb-section {
    background: white;
    padding: 1rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.breadcrumb-item a {
    color: var(--primary-color);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: var(--dark-color);
}

.book-main-info {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

    .book-cover-container {
        position: relative;
    text-align: center;
}

.book-cover-image {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

.book-cover-image:hover {
        transform: scale(1.05);
    }

.book-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.book-badges .badge {
    font-size: 0.7rem;
    padding: 0.5rem 0.75rem;
}

.book-info {
    padding-left: 1rem;
}

.book-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark-color);
    margin-bottom: 1rem;
    line-height: 1.2;
}

.book-author {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.author-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.author-link:hover {
    text-decoration: underline;
}

.book-rating-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.rating-stars .fas {
    font-size: 1.2rem;
}

.rating-text {
    font-weight: 600;
    color: var(--dark-color);
    margin-left: 0.5rem;
}

.rating-count {
    font-size: 0.9rem;
    color: #6c757d;
}

.book-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #6c757d;
}

.category-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.category-link:hover {
    text-decoration: underline;
}

.book-price-section {
    margin-bottom: 2rem;
}

.price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--success-color);
}

.original-price {
    font-size: 1.2rem;
    color: #6c757d;
    text-decoration: line-through;
    margin-left: 1rem;
}

.discount {
    background: var(--danger-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.book-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.book-actions .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 25px;
}

.loan-info,
.reservation-info {
    width: 100%;
    margin-top: 1rem;
}

.auth-required {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.book-description {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.book-description h3 {
    color: var(--dark-color);
    margin-bottom: 1rem;
    font-weight: 700;
}

.description-content {
    line-height: 1.8;
    color: #6c757d;
}

.book-stats {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-align: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.stat-info {
    flex: 1;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dark-color);
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.book-reviews {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.reviews-header h3 {
    color: var(--dark-color);
    margin: 0;
    font-weight: 700;
}

.review-item {
    padding: 1.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.reviewer-details h6 {
    margin: 0;
    color: var(--dark-color);
    font-weight: 600;
}

.review-rating {
    display: flex;
    gap: 0.25rem;
    margin-top: 0.25rem;
}

.review-rating .fas {
    font-size: 0.8rem;
}

.review-date {
    font-size: 0.8rem;
    color: #6c757d;
}

.review-content p {
    margin: 0;
    line-height: 1.6;
    color: #6c757d;
}

.no-reviews {
    text-align: center;
    padding: 3rem 2rem;
    color: #6c757d;
}

.no-reviews i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.reviews-pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

/* Sidebar */
.related-books,
.additional-info,
.share-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.related-books h4,
.additional-info h4,
.share-section h4 {
    color: var(--dark-color);
    margin-bottom: 1rem;
    font-weight: 700;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f8f9fa;
}

.related-book-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.related-book-item:last-child {
    border-bottom: none;
}

.related-book-cover {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.related-book-info h6 {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.related-book-info h6 a {
    color: var(--dark-color);
    text-decoration: none;
}

.related-book-info h6 a:hover {
    color: var(--primary-color);
}

.related-book-info p {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.related-book-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
}

.related-book-rating .fas {
    font-size: 0.7rem;
}

.info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: var(--dark-color);
}

.share-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.share-buttons .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.btn-facebook {
    background: #1877f2;
    color: white;
}

.btn-twitter {
    background: #1da1f2;
    color: white;
}

.btn-linkedin {
    background: #0077b5;
    color: white;
}

.btn-email {
    background: #6c757d;
    color: white;
}

/* Rating input */
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        gap: 0.5rem;
    }

    .rating-input input {
        display: none;
    }

    .rating-input label {
        cursor: pointer;
        font-size: 1.5rem;
    color: #dee2e6;
    transition: color 0.3s ease;
    }

    .rating-input input:checked ~ label,
    .rating-input label:hover,
    .rating-input label:hover ~ label {
        color: #ffc107;
    }

/* Responsive */
@media (max-width: 768px) {
    .book-title {
        font-size: 1.5rem;
    }
    
    .book-meta {
        grid-template-columns: 1fr;
    }
    
    .book-actions {
        flex-direction: column;
    }
    
    .book-actions .btn {
        width: 100%;
    }
    
    .reviews-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    }
.book-cover-placeholder {
    width: 100%;
    min-height: 200px;
    background: #f3f3f3;
    color: #bbb;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 1.1rem;
    font-style: italic;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.related-book-cover, .book-image {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}
.modal-backdrop.show {
    opacity: 0.5 !important;
    background: #222 !important;
}
[data-bs-theme="dark"] .modal-content {
    background: #23272b;
    color: #f1f1f1;
}
[data-bs-theme="dark"] .modal-backdrop.show {
    background: #000 !important;
    opacity: 0.7 !important;
}
.rating-input { display: flex; flex-direction: row-reverse; gap: 0.2rem; }
.rating-input input[type="radio"] { display: none; }
.rating-input label { cursor: pointer; font-size: 1.5rem; color: #ccc; transition: color 0.2s; }
.rating-input input[type="radio"]:checked ~ label, .rating-input label:hover, .rating-input label:hover ~ label { color: #ffc107; }
.review-item { border: 1px solid #e3e6f0; border-radius: 8px; background: #fff; }
.review-rating { font-size: 1.1rem; }
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
        const text = button.querySelector('span') || button;
        if (data.action === 'agregado') {
            icon.classList.add('text-danger');
            button.title = 'Quitar de favoritos';
            if (text.textContent) text.textContent = 'Quitar de favoritos';
        } else {
            icon.classList.remove('text-danger');
            button.title = 'Agregar a favoritos';
            if (text.textContent) text.textContent = 'Agregar a favoritos';
        }
        showNotification(data.message, 'success');
    }
});
    }
});

// Reservar libro
document.addEventListener('click', function(e) {
    if (e.target.closest('.reserve-book')) {
        const button = e.target.closest('.reserve-book');
        const libroId = button.dataset.libroId;
        
        // Mostrar modal
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

// Lazy loading de imágenes
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
});
document.addEventListener('hidden.bs.modal', function (event) {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    // Eliminar backdrop si queda
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});
</script>
@endpush