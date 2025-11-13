@extends('layouts.app')

@section('title', 'Clubes de Lectura - Retrolector')

@section('content')
<div class="reading-clubs-container">
    <!-- Header de Clubes de Lectura -->
    <div class="clubs-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="clubs-title">
                        <i class="fas fa-users me-3"></i>Clubes de Lectura
                    </h1>
                    <p class="clubs-subtitle">Únete a comunidades de lectores apasionados y comparte tus experiencias</p>
                </div>
                <div class="col-lg-4 text-end">
                    @auth
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createClubModal">
                            <i class="fas fa-plus me-2"></i>Crear Club
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filtros y Búsqueda -->
        <div class="clubs-filters">
            <div class="row">
                <div class="col-lg-4">
                    <div class="search-box">
                        <input type="text" id="searchClubs" class="form-control" placeholder="Buscar clubes...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="col-lg-3">
                    <select id="categoryFilter" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                        <option value="full">Completos</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button class="btn btn-outline-primary w-100" id="clearFilters">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Clubes -->
        <div class="clubs-stats">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_clubs'] ?? 0 }}</h3>
                            <p>Clubes Totales</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_members'] ?? 0 }}</h3>
                            <p>Miembros</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['total_discussions'] ?? 0 }}</h3>
                            <p>Discusiones</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $stats['books_read'] ?? 0 }}</h3>
                            <p>Libros Leídos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clubes Destacados -->
        <div class="featured-clubs">
            <div class="section-header">
                <h2><i class="fas fa-star me-2"></i>Clubes Destacados</h2>
                <p>Los clubes más populares y activos de la comunidad</p>
            </div>
            
            <div class="clubs-grid">
                @foreach($featured_clubs as $club)
                    <div class="club-card featured" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="featured-badge">
                            <i class="fas fa-star"></i>
                            <span>Destacado</span>
                        </div>
                        
                        <div class="club-header">
                            <div class="club-avatar">
                                <img src="{{ $club->imagen ?? 'https://via.placeholder.com/80x80' }}" alt="{{ $club->nombre }}">
                            </div>
                            <div class="club-info">
                                <h3>{{ $club->nombre }}</h3>
                                <p class="club-description">{{ Str::limit($club->descripcion, 100) }}</p>
                                <div class="club-meta">
                                    <span class="club-category">{{ $club->categoria->nombre ?? 'General' }}</span>
                                    <span class="club-members">{{ $club->miembros_count }} miembros</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="club-stats">
                            <div class="stat-item">
                                <i class="fas fa-book"></i>
                                <span>{{ $club->libros_leidos_count }} libros</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comments"></i>
                                <span>{{ $club->discusiones_count }} discusiones</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $club->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <div class="club-actions">
                            @if($club->is_member)
                                <a href="{{ route('clubs.show', $club->id) }}" class="btn btn-primary">
                                    <i class="fas fa-door-open me-2"></i>Entrar
                                </a>
                            @elseif($club->is_pending)
                                <button class="btn btn-warning" disabled>
                                    <i class="fas fa-clock me-2"></i>Pendiente
                                </button>
                            @else
                                <button class="btn btn-success join-club" data-club-id="{{ $club->id }}">
                                    <i class="fas fa-user-plus me-2"></i>Unirse
                                </button>
                            @endif
                            
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#clubDetailsModal" data-club="{{ json_encode($club) }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Todos los Clubes -->
        <div class="all-clubs">
            <div class="section-header">
                <h2><i class="fas fa-list me-2"></i>Todos los Clubes</h2>
                <p>Explora todos los clubes disponibles</p>
            </div>
            
            <div class="clubs-grid" id="allClubsGrid">
                @foreach($clubs as $club)
                    <div class="club-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="club-header">
                            <div class="club-avatar">
                                <img src="{{ $club->imagen ?? 'https://via.placeholder.com/80x80' }}" alt="{{ $club->nombre }}">
                            </div>
                            <div class="club-info">
                                <h3>{{ $club->nombre }}</h3>
                                <p class="club-description">{{ Str::limit($club->descripcion, 100) }}</p>
                                <div class="club-meta">
                                    <span class="club-category">{{ $club->categoria->nombre ?? 'General' }}</span>
                                    <span class="club-members">{{ $club->miembros_count }} miembros</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="club-stats">
                            <div class="stat-item">
                                <i class="fas fa-book"></i>
                                <span>{{ $club->libros_leidos_count }} libros</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comments"></i>
                                <span>{{ $club->discusiones_count }} discusiones</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $club->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <div class="club-actions">
                            @if($club->is_member)
                                <a href="{{ route('clubs.show', $club->id) }}" class="btn btn-primary">
                                    <i class="fas fa-door-open me-2"></i>Entrar
                                </a>
                            @elseif($club->is_pending)
                                <button class="btn btn-warning" disabled>
                                    <i class="fas fa-clock me-2"></i>Pendiente
                                </button>
                            @else
                                <button class="btn btn-success join-club" data-club-id="{{ $club->id }}">
                                    <i class="fas fa-user-plus me-2"></i>Unirse
                                </button>
                            @endif
                            
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#clubDetailsModal" data-club="{{ json_encode($club) }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Paginación -->
            <div class="pagination-wrapper">
                {{ $clubs->links() }}
            </div>
        </div>

        <!-- Mis Clubes -->
        @auth
            @if($my_clubs->count() > 0)
                <div class="my-clubs">
                    <div class="section-header">
                        <h2><i class="fas fa-heart me-2"></i>Mis Clubes</h2>
                        <p>Los clubes a los que perteneces</p>
                    </div>
                    
                    <div class="clubs-grid">
                        @foreach($my_clubs as $club)
                            <div class="club-card my-club" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="my-club-badge">
                                    <i class="fas fa-heart"></i>
                                    <span>Mi Club</span>
                                </div>
                                
                                <div class="club-header">
                                    <div class="club-avatar">
                                        <img src="{{ $club->imagen ?? 'https://via.placeholder.com/80x80' }}" alt="{{ $club->nombre }}">
                                    </div>
                                    <div class="club-info">
                                        <h3>{{ $club->nombre }}</h3>
                                        <p class="club-description">{{ Str::limit($club->descripcion, 100) }}</p>
                                        <div class="club-meta">
                                            <span class="club-category">{{ $club->categoria->nombre ?? 'General' }}</span>
                                            <span class="club-members">{{ $club->miembros_count }} miembros</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="club-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-book"></i>
                                        <span>{{ $club->libros_leidos_count }} libros</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-comments"></i>
                                        <span>{{ $club->discusiones_count }} discusiones</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $club->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                <div class="club-actions">
                                    <a href="{{ route('clubs.show', $club->id) }}" class="btn btn-primary">
                                        <i class="fas fa-door-open me-2"></i>Entrar
                                    </a>
                                    
                                    @if($club->is_admin)
                                        <a href="{{ route('clubs.manage', $club->id) }}" class="btn btn-warning">
                                            <i class="fas fa-cog me-2"></i>Gestionar
                                        </a>
                                    @endif
                                    
                                    <button class="btn btn-outline-danger leave-club" data-club-id="{{ $club->id }}">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth
    </div>
</div>

<!-- Modal de Crear Club -->
<div class="modal fade" id="createClubModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Club de Lectura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createClubForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clubName" class="form-label">Nombre del Club</label>
                                <input type="text" class="form-control" id="clubName" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clubCategory" class="form-label">Categoría</label>
                                <select class="form-select" id="clubCategory" name="categoria_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clubDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="clubDescription" name="descripcion" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="maxMembers" class="form-label">Máximo de Miembros</label>
                                <input type="number" class="form-control" id="maxMembers" name="max_miembros" min="5" max="100" value="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="isPrivate" class="form-label">Visibilidad</label>
                                <select class="form-select" id="isPrivate" name="es_privado">
                                    <option value="0">Público</option>
                                    <option value="1">Privado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clubRules" class="form-label">Reglas del Club</label>
                        <textarea class="form-control" id="clubRules" name="reglas" rows="3" placeholder="Establece las reglas básicas del club..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Club
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Detalles del Club -->
<div class="modal fade" id="clubDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Club</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="clubDetailsContent">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.reading-clubs-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.clubs-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 3rem 0;
    margin-bottom: 3rem;
    color: white;
}

.clubs-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.clubs-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.clubs-filters {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.search-box {
    position: relative;
}

.search-box input {
    padding-right: 40px;
    border-radius: 10px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.clubs-stats {
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.stat-content p {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

.featured-clubs,
.all-clubs,
.my-clubs {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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

.clubs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.club-card {
    background: white;
    border: 2px solid #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
}

.club-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.club-card.featured {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff9c4, #fff59d);
}

.club-card.my-club {
    border-color: #dc3545;
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
}

.featured-badge,
.my-club-badge {
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

.my-club-badge {
    background: linear-gradient(45deg, #dc3545, #c82333);
}

.club-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.club-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.club-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.club-info {
    flex: 1;
}

.club-info h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.club-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.club-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.club-category,
.club-members {
    font-size: 0.8rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.club-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding: 1rem 0;
    border-top: 1px solid #f8f9fa;
    border-bottom: 1px solid #f8f9fa;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.stat-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.stat-item span {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 600;
}

.club-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.club-actions .btn {
    flex: 1;
    border-radius: 10px;
    font-weight: 600;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .clubs-title {
        font-size: 2rem;
    }
    
    .clubs-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .club-header {
        flex-direction: column;
        text-align: center;
    }
    
    .club-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .club-actions {
        flex-direction: column;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Búsqueda y filtros
document.getElementById('searchClubs').addEventListener('input', filterClubs);
document.getElementById('categoryFilter').addEventListener('change', filterClubs);
document.getElementById('statusFilter').addEventListener('change', filterClubs);
document.getElementById('clearFilters').addEventListener('click', clearFilters);

function filterClubs() {
    const searchTerm = document.getElementById('searchClubs').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const clubs = document.querySelectorAll('.club-card');
    
    clubs.forEach(club => {
        const clubName = club.querySelector('h3').textContent.toLowerCase();
        const clubDescription = club.querySelector('.club-description').textContent.toLowerCase();
        const clubCategory = club.querySelector('.club-category').textContent;
        
        let show = true;
        
        // Filtro de búsqueda
        if (searchTerm && !clubName.includes(searchTerm) && !clubDescription.includes(searchTerm)) {
            show = false;
        }
        
        // Filtro de categoría
        if (categoryFilter && clubCategory !== categoryFilter) {
            show = false;
        }
        
        // Filtro de estado (implementar lógica según necesidades)
        if (statusFilter) {
            // Aquí se implementaría la lógica de filtrado por estado
        }
        
        club.style.display = show ? 'block' : 'none';
    });
}

function clearFilters() {
    document.getElementById('searchClubs').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterClubs();
}

// Unirse a club
document.addEventListener('click', function(e) {
    if (e.target.closest('.join-club')) {
        const button = e.target.closest('.join-club');
        const clubId = button.dataset.clubId;
        
        joinClub(clubId, button);
    }
});

function joinClub(clubId, button) {
    fetch(`{{ route('clubs.join') }}/${clubId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-clock me-2"></i>Pendiente';
            button.disabled = true;
            button.classList.remove('btn-success');
            button.classList.add('btn-warning');
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

// Salir de club
document.addEventListener('click', function(e) {
    if (e.target.closest('.leave-club')) {
        const button = e.target.closest('.leave-club');
        const clubId = button.dataset.clubId;
        
        if (confirm('¿Estás seguro de que quieres salir de este club?')) {
            leaveClub(clubId, button);
        }
    }
});

function leaveClub(clubId, button) {
    fetch(`{{ route('clubs.leave') }}/${clubId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.closest('.club-card').remove();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

// Crear club
document.getElementById('createClubForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("clubs.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('createClubModal')).hide();
            this.reset();
            // Recargar página para mostrar el nuevo club
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    });
});

// Detalles del club
document.getElementById('clubDetailsModal').addEventListener('show.bs.modal', function(e) {
    const button = e.relatedTarget;
    const clubData = JSON.parse(button.dataset.club);
    
    const content = `
        <div class="club-details">
            <div class="row">
                <div class="col-md-4">
                    <img src="${clubData.imagen || 'https://via.placeholder.com/200x200'}" 
                         alt="${clubData.nombre}" class="img-fluid rounded">
                </div>
                <div class="col-md-8">
                    <h4>${clubData.nombre}</h4>
                    <p class="text-muted">${clubData.descripcion}</p>
                    
                    <div class="club-details-stats">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat">
                                    <strong>${clubData.miembros_count}</strong>
                                    <small>Miembros</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat">
                                    <strong>${clubData.libros_leidos_count}</strong>
                                    <small>Libros</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat">
                                    <strong>${clubData.discusiones_count}</strong>
                                    <small>Discusiones</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="club-details-info">
                        <p><strong>Categoría:</strong> ${clubData.categoria?.nombre || 'General'}</p>
                        <p><strong>Creado:</strong> ${new Date(clubData.created_at).toLocaleDateString()}</p>
                        <p><strong>Estado:</strong> ${clubData.es_privado ? 'Privado' : 'Público'}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('clubDetailsContent').innerHTML = content;
});

// Mostrar notificación
function showNotification(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
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