@extends('layouts.app')

@section('title', 'Leer ' . $libro->titulo . ' - Retrolector')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar de Información -->
        <div class="col-lg-3" id="sidebarPanel">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Información del Libro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($libro->imagen_portada)
                            <img src="{{ asset('storage/' . $libro->imagen_portada) }}" 
                                 alt="{{ $libro->titulo }}" 
                                 class="img-fluid rounded" style="max-width: 150px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded mx-auto" 
                                 style="width: 150px; height: 200px;">
                                <i class="fas fa-book fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <h6 class="mb-2">{{ $libro->titulo }}</h6>
                    <p class="text-muted small mb-3">{{ $libro->autor->nombre ?? 'Autor no especificado' }}</p>
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $libro->categoria->nombre ?? 'Sin categoría' }}</span>
                    </div>
                    @if(isset($tieneAcceso) && $tieneAcceso)
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Acceso Completo</strong><br>
                            <small>Puedes leer todo el libro</small>
                        </div>
                        @if(isset($prestamo))
                            <div class="mb-3">
                                <h6>Información del Préstamo</h6>
                                <p class="small mb-1">
                                    <strong>Fecha de préstamo:</strong><br>
                                    {{ $prestamo->fecha_prestamo->format('d/m/Y') }}
                                </p>
                                <p class="small mb-1">
                                    <strong>Fecha de devolución:</strong><br>
                                    {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                                </p>
                                <p class="small">
                                    <strong>Días restantes:</strong><br>
                                    <span class="badge bg-{{ $prestamo->fecha_devolucion_esperada->diffInDays(now()) <= 3 ? 'danger' : 'success' }}">
                                        {{ $prestamo->fecha_devolucion_esperada->diffInDays(now()) }} días
                                    </span>
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-eye me-2"></i>
                            <strong>Vista Previa</strong><br>
                            <small>Solo puedes ver los primeros capítulos</small>
                        </div>
                        <!-- Tarjetas de precios -->
                        <div class="mb-3">
                            <h6 class="mb-2">Opciones de Compra</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="card border-primary">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-primary fw-bold">Online</small>
                                            <div class="price-tag">
                                                <span class="currency">S/</span>
                                                <span class="amount">
                                                    @if($libro->precio_compra_online > 0)
                                                        {{ number_format($libro->precio_compra_online, 2) }}
                                                    @else
                                                        No disponible
                                                    @endif
                                                </span>
                                            </div>
                                            <small class="text-muted">Permanente</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card border-success">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-success fw-bold">Préstamo</small>
                                            <div class="price-tag">
                                                <span class="currency">S/</span>
                                                <span class="amount">
                                                    @if($libro->precio_prestamo_online > 0)
                                                        {{ number_format($libro->precio_prestamo_online, 2) }}
                                                    @else
                                                        No disponible
                                                    @endif
                                                </span>
                                            </div>
                                            <small class="text-muted">7 días</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="toggleSidebar()" id="toggleSidebarBtn">
                            <i class="fas fa-eye-slash me-2"></i>
                            Ocultar Panel
                        </button>
                        <a href="{{ route('books.show', $libro) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-info-circle me-2"></i>
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
            <!-- Controles de Lectura -->
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Controles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small">Tamaño de Fuente</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="changeFontSize(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="form-control-plaintext text-center" id="fontSizeDisplay">16px</span>
                            <button class="btn btn-sm btn-outline-secondary" onclick="changeFontSize(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tema</label>
                        <select class="form-select form-select-sm" id="themeSelect" onchange="changeTheme()">
                            <option value="light">Claro</option>
                            <option value="sepia">Sepia</option>
                            <option value="dark">Oscuro</option>
                        </select>
                    </div>
                    @if(isset($tieneAcceso) && $tieneAcceso)
                    <div class="mb-3">
                        <label class="form-label small">Marcadores</label>
                        <div class="d-grid gap-1">
                            <button class="btn btn-sm btn-outline-warning" onclick="addBookmark()">
                                <i class="fas fa-bookmark me-1"></i>Agregar
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="showBookmarks()">
                                <i class="fas fa-list me-1"></i>Ver Todos
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Área de Lectura -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-book-open me-2"></i>
                            {{ $libro->titulo }}
                        </h5>
                        <small class="text-muted">Página <span id="currentPage">1</span> de <span id="totalPages">1</span></small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="printBook()">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        $contenido = $libro->contenido ?? '';
                    @endphp
                    <div id="reader" class="reader-container">
                        @if(empty($contenido))
                            <div class="alert alert-info text-center my-5">
                                <i class="fas fa-info-circle me-2"></i>
                                Este libro aún no tiene contenido disponible.
                            </div>
                        @else
                            @if(isset($tieneAcceso) && $tieneAcceso)
                                {!! nl2br(e($contenido)) !!}
                            @else
                                {!! nl2br(e(Str::limit($contenido, $libro->preview_limit ?? 1000))) !!}
                                <div class="preview-limit mt-4">
                                    <h3><i class="fas fa-lock me-2"></i>Contenido Bloqueado</h3>
                                    <p>Has llegado al final de la vista previa gratuita. Para continuar leyendo este libro, elige una opción de compra o préstamo.</p>
                                    <div>
                                        @if($libro->precio_compra_online > 0)
                                        <a href="{{ route('books.purchase', $libro) }}?tipo=comprar&modalidad=online" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i>Comprar Online
                                        </a>
                                        @endif
                                        @if($libro->precio_prestamo_online > 0)
                                        <a href="{{ route('books.purchase', $libro) }}?tipo=prestar&modalidad=online" class="btn btn-success">
                                            <i class="fas fa-handshake me-2"></i>Prestar Online
                                        </a>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        @if($libro->precio_compra_fisica > 0)
                                        <a href="{{ route('books.purchase', $libro) }}?tipo=comprar&modalidad=fisico" class="btn btn-outline-light">
                                            <i class="fas fa-shipping-fast me-2"></i>Comprar Físico
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Marcadores -->
<div class="modal fade" id="bookmarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bookmark me-2"></i>
                    Mis Marcadores
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="bookmarksList">
                    <p class="text-muted text-center">No hay marcadores guardados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Compra -->
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Completar Lectura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                    <h4>¿Te gustó lo que leíste?</h4>
                    <p class="text-muted">Para continuar leyendo este libro, elige una opción:</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h6 class="mb-0">Compra Online</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="price-tag mb-3">
                                    <span class="currency">S/</span>
                                    <span class="amount">25.00</span>
                                </div>
                                <ul class="list-unstyled small text-muted mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Acceso permanente</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Sin límites de tiempo</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Descarga disponible</li>
                                </ul>
                                <a href="{{ route('books.purchase', $libro) }}?tipo=comprar&modalidad=online" class="btn btn-primary w-100">
                                    Comprar Ahora
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white text-center">
                                <i class="fas fa-handshake fa-2x mb-2"></i>
                                <h6 class="mb-0">Préstamo Online</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="price-tag mb-3">
                                    <span class="currency">S/</span>
                                    <span class="amount">3.00</span>
                                </div>
                                <ul class="list-unstyled small text-muted mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Acceso por 7 días</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Ideal para probar</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Precio económico</li>
                                </ul>
                                <a href="{{ route('books.purchase', $libro) }}?tipo=prestar&modalidad=online" class="btn btn-success w-100">
                                    Prestar Ahora
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        También puedes comprar la versión física por S/ 45.00 con envío incluido
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.reader-container {
    min-height: 70vh;
    background: var(--bg-color);
    color: var(--text-color);
    font-family: 'Georgia', serif;
    line-height: 1.8;
    padding: 2rem;
    font-size: 16px;
    transition: all 0.3s ease;
}

.reader-container.light {
    --bg-color: #ffffff;
    --text-color: #333333;
}

.reader-container.sepia {
    --bg-color: #f4ecd8;
    --text-color: #5c4b37;
}

.reader-container.dark {
    --bg-color: #2c3e50;
    --text-color: #ecf0f1;
}

.price-tag {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2c3e50;
}

.price-tag .currency {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.price-tag .amount {
    color: #27ae60;
}

.preview-limit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    margin: 2rem 0;
}

.preview-limit h3 {
    margin-bottom: 1rem;
}

.preview-limit p {
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.preview-limit .btn {
    margin: 0.5rem;
    min-width: 150px;
}

@media (max-width: 768px) {
    .reader-container {
        padding: 1rem;
        font-size: 14px;
    }
}
</style>

@push('scripts')
<script>
let currentFontSize = 16;
let currentTheme = 'light';
let currentPage = 1;
let totalPages = 1;
let bookContent = '';
let previewLimit = 2000; // Caracteres para vista previa
let tieneAcceso = {{ isset($tieneAcceso) && $tieneAcceso ? 'true' : 'false' }};

// Contenido de ejemplo del libro
const libroContenido = `{{ $libro->titulo }}

Capítulo 1: El Comienzo

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.

Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.

Capítulo 2: El Desarrollo

Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.

Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.

Capítulo 3: El Clímax

Vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.

Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.

Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.

Capítulo 4: La Resolución

Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.

Ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.

Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.

Capítulo 5: El Final

Sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.

Sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.

Nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?`;

function loadBookContent() {
    const reader = document.getElementById('reader');
    
    if (tieneAcceso) {
        // Contenido completo
        bookContent = libroContenido;
        reader.innerHTML = formatContent(bookContent);
        totalPages = Math.ceil(bookContent.length / 3000); // Aproximadamente 3000 caracteres por página
    } else {
        // Vista previa
        const previewContent = libroContenido.substring(0, previewLimit);
        const remainingContent = libroContenido.substring(previewLimit);
        
        reader.innerHTML = `
            ${formatContent(previewContent)}
            
            <div class="preview-limit">
                <h3><i class="fas fa-lock me-2"></i>Contenido Bloqueado</h3>
                <p>Has llegado al final de la vista previa gratuita. Para continuar leyendo este libro, elige una opción de compra o préstamo.</p>
                <div>
                    <button class="btn btn-primary" onclick="showPurchaseModal()">
                        <i class="fas fa-shopping-cart me-2"></i>Comprar Online (S/ 25.00)
                    </button>
                    <button class="btn btn-success" onclick="showPurchaseModal()">
                        <i class="fas fa-handshake me-2"></i>Prestar Online (S/ 3.00)
                    </button>
                </div>
                <div class="mt-3">
                    <a href="{{ route('books.purchase', $libro) }}?tipo=comprar&modalidad=fisico" class="btn btn-outline-light">
                        <i class="fas fa-shipping-fast me-2"></i>Comprar Físico (S/ 45.00)
                    </a>
                </div>
            </div>
        `;
        totalPages = Math.ceil(previewContent.length / 3000);
    }
    
    updatePageInfo();
}

function formatContent(content) {
    return content.split('\n\n').map(paragraph => {
        if (paragraph.trim().startsWith('Capítulo')) {
            return `<h2 class="chapter-title">${paragraph}</h2>`;
        } else if (paragraph.trim()) {
            return `<p>${paragraph}</p>`;
        }
        return '';
    }).join('');
}

function updatePageInfo() {
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent = totalPages;
}

function changeFontSize(delta) {
    currentFontSize = Math.max(12, Math.min(24, currentFontSize + delta));
    document.getElementById('fontSizeDisplay').textContent = currentFontSize + 'px';
    document.getElementById('reader').style.fontSize = currentFontSize + 'px';
}

function changeTheme() {
    const theme = document.getElementById('themeSelect').value;
    const reader = document.getElementById('reader');
    
    reader.className = `reader-container ${theme}`;
    currentTheme = theme;
}

function toggleFullscreen() {
    const reader = document.getElementById('reader');
    if (!document.fullscreenElement) {
        reader.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function printBook() {
    window.print();
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        updatePageInfo();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        updatePageInfo();
    }
}

function addBookmark() {
    if (!tieneAcceso) {
        showPurchaseModal();
        return;
    }
    
    // Lógica para agregar marcador
    alert('Marcador agregado en la página ' + currentPage);
}

function showBookmarks() {
    if (!tieneAcceso) {
        showPurchaseModal();
        return;
    }
    
    // Mostrar modal de marcadores
    const modal = new bootstrap.Modal(document.getElementById('bookmarksModal'));
    modal.show();
}

function showPurchaseModal() {
    const modal = new bootstrap.Modal(document.getElementById('purchaseModal'));
    modal.show();
}

function toggleSidebar() {
    var sidebar = document.getElementById('sidebarPanel');
    var btn = document.getElementById('toggleSidebarBtn');
    if (sidebar.style.display === 'none') {
        sidebar.style.display = '';
        btn.innerHTML = '<i class="fas fa-eye-slash me-2"></i> Ocultar Panel';
    } else {
        sidebar.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye me-2"></i> Mostrar Panel';
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    loadBookContent();
    changeTheme();
});
</script>
@endpush
@endsection 