@extends('layouts.app')

@section('title', 'Retrolector - Biblioteca Digital Moderna')

@section('content')
<!-- Hero Section Librería -->
<div class="hero-section-library position-relative py-5">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-text animate__animated animate__fadeInLeft">
                <h1 class="fw-bold mb-4">Descubre el mundo de la <span class="library-highlight">lectura</span> digital</h1>
                <p class="mb-4">Retrolector es tu biblioteca digital moderna. Accede a miles de libros, gestiona tus préstamos y disfruta de una experiencia de lectura única con tecnología de vanguardia.</p>
                <div class="hero-buttons mb-4">
                    <a href="{{ route('register') }}" class="btn btn-library-primary btn-lg me-2 animate__animated animate__fadeInUp animate__delay-1s"> <i class="fas fa-rocket"></i> Comenzar Ahora</a>
                    <a href="{{ route('books.catalog') }}" class="btn btn-outline-library btn-lg animate__animated animate__fadeInUp animate__delay-2s"> <i class="fas fa-search"></i> Explorar Catálogo</a>
                </div>
            </div>
            <div class="col-lg-6 hero-image text-center animate__animated animate__fadeInRight">
                <img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=800&q=80" alt="Estantería de libros" class="img-fluid rounded-4 shadow-lg" style="max-width: 90%; object-fit: cover;">
            </div>
        </div>
    </div>
</div>



<!-- Cards de características -->
<div class="container py-5">
    <div class="row text-center mb-5">
        <div class="col">
            <h2 class="fw-bold mb-3">¿Por qué Retrolector?</h2>
            <p class="text-muted">Todo lo que necesitas para una experiencia de lectura moderna y social.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-card h-100 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <i class="fas fa-book fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Catálogo Extenso</h5>
                    <p class="card-text">Miles de libros de todos los géneros, siempre disponibles para ti.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-3 text-success"></i>
                    <h5 class="card-title">Clubes de Lectura</h5>
                    <p class="card-text">Únete a comunidades, comparte opiniones y haz nuevos amigos lectores.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-2x mb-3 text-info"></i>
                    <h5 class="card-title">Estadísticas y Analíticas</h5>
                    <p class="card-text">Sigue tu progreso, descubre hábitos y mejora tu experiencia lectora.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bloque interactivo de recomendación -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card interactive-card text-center shadow-lg animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h4 class="mb-3">¿No sabes qué leer?</h4>
                    <p class="mb-4">Haz clic en el botón y recibe una recomendación aleatoria de nuestro catálogo.</p>
                    <button id="btn-recomendar" class="btn btn-accent btn-lg mb-3"><i class="fas fa-magic me-2"></i>Recomiéndame un libro</button>
                    <div id="recomendacion-libro" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carrusel de testimonios -->
<div class="container py-5">
    <div class="row text-center mb-4">
        <div class="col">
            <h2 class="fw-bold mb-3">Lo que dicen nuestros lectores</h2>
        </div>
    </div>
    <div id="testimoniosCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="testimonial testimonial-theme p-4 rounded shadow-sm mx-auto" style="max-width:600px;">
                    <p class="mb-2">“Retrolector me ayudó a descubrir libros increíbles y a conectarme con otros lectores apasionados.”</p>
                    <div class="fw-bold">Ana Martínez</div>
                    <div class="text-muted small">Estudiante y lectora</div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="testimonial testimonial-theme p-4 rounded shadow-sm mx-auto" style="max-width:600px;">
                    <p class="mb-2">“La función de estadísticas me motiva a leer más cada mes. ¡Me encanta!”</p>
                    <div class="fw-bold">Carlos Gómez</div>
                    <div class="text-muted small">Ingeniero y lector</div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="testimonial testimonial-theme p-4 rounded shadow-sm mx-auto" style="max-width:600px;">
                    <p class="mb-2">“Los clubes de lectura virtuales son geniales para compartir opiniones y conocer gente.”</p>
                    <div class="fw-bold">Lucía Torres</div>
                    <div class="text-muted small">Profesora y lectora</div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimoniosCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimoniosCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</div>

<!-- Libro Virtual Interactivo con efecto flip -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="book-flip-container animate__animated animate__fadeInUp">
                <div class="book-flip" id="bookFlip">
                    <div class="book-page left-page" id="leftPageContent"></div>
                    <div class="book-spine"></div>
                    <div class="book-page right-page" id="rightPageContent"></div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button class="book-arrow-flip" id="prevFlip" aria-label="Página anterior"><i class="fas fa-chevron-left"></i></button>
                    <button class="book-arrow-flip" id="nextFlip" aria-label="Página siguiente"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="text-center mt-2 text-muted small">
                    Haz clic en las flechas para pasar las páginas del libro
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Sección CTA adaptativa -->
<div class="py-5 text-center cta-section-library" id="cta-section">
    <h2 class="fw-bold mb-3">¿Listo para comenzar tu aventura literaria?</h2>
    <p class="mb-4">Únete a miles de lectores que ya disfrutan de Retrolector</p>
    <a href="{{ route('register') }}" class="btn btn-library-primary btn-lg"><i class="fas fa-user-plus me-2"></i>Crear Cuenta Gratis</a>
</div>

@push('scripts')
<script>
// Estadísticas animadas
const counters = document.querySelectorAll('.stat-number');
counters.forEach(counter => {
    const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const increment = Math.ceil(target / 40);
        if(count < target) {
            counter.innerText = count + increment;
            setTimeout(updateCount, 20);
        } else {
            counter.innerText = target;
        }
    };
    updateCount();
});

// Actualización en tiempo real de estadísticas
function updateStatsRealtime() {
    fetch("{{ route('stats.realtime') }}")
        .then(response => response.json())
        .then(stats => {
            document.querySelector('.stat-number[data-target="{{ $stats['total_books'] ?? 0 }}"]').textContent = stats.total_books;
            document.querySelector('.stat-number[data-target="{{ $stats['active_users'] ?? 0 }}"]').textContent = stats.active_users;
            document.querySelector('.stat-number[data-target="{{ $stats['total_loans'] ?? 0 }}"]').textContent = stats.total_loans;
            document.querySelector('.stat-number[data-target="{{ $stats['reading_clubs'] ?? 0 }}"]').textContent = stats.reading_clubs;
        });
}
setInterval(updateStatsRealtime, 30000);
document.addEventListener('DOMContentLoaded', updateStatsRealtime);

// Recomendación aleatoria (simulada)
document.getElementById('btn-recomendar').addEventListener('click', function() {
    const libros = [
        'Cien años de soledad',
        'Rayuela',
        'El Aleph',
        'La casa de los espíritus',
        'Veinte poemas de amor',
        'La ciudad y los perros',
        'El laberinto de la soledad',
        'La muerte de Artemio Cruz'
    ];
    const autores = [
        'Gabriel García Márquez',
        'Julio Cortázar',
        'Jorge Luis Borges',
        'Isabel Allende',
        'Pablo Neruda',
        'Mario Vargas Llosa',
        'Octavio Paz',
        'Carlos Fuentes'
    ];
    const idx = Math.floor(Math.random() * libros.length);
    document.getElementById('recomendacion-libro').style.display = 'block';
    document.getElementById('recomendacion-libro').innerHTML = `<div class='alert alert-info animate__animated animate__fadeIn'><b>${libros[idx]}</b> de <i>${autores[idx]}</i></div>`;
});

// Curiosidades para el libro flip
const curiosidades = [
    'El libro más robado de las bibliotecas públicas es el Guinness World Records.',
    'La palabra “libro” proviene del latín “liber”, que significa corteza de árbol.',
    'El libro más caro jamás vendido es el Codex Leicester de Leonardo da Vinci, comprado por Bill Gates.',
    'En Islandia, existe la tradición de regalar libros en Nochebuena y pasar la noche leyendo.',
    'El primer libro impreso con tipos móviles fue la Biblia de Gutenberg en 1455.',
    'El libro más pequeño del mundo mide 0,74 x 0,75 mm y solo puede leerse con lupa.',
    'La Biblioteca Nacional de China es la más grande de Asia, con más de 37 millones de volúmenes.',
    'El Día Mundial del Libro se celebra el 23 de abril, fecha de la muerte de Cervantes y Shakespeare.',
    'El libro más traducido del mundo (después de la Biblia) es “El Principito”.',
    'En Japón, existen cafés donde puedes leer libros mientras tomas té o café en silencio.'
];
let flipIndex = 0;
function renderBookFlip() {
    // Dos curiosidades por "doble página"
    const left = curiosidades[flipIndex] || '';
    const right = curiosidades[flipIndex+1] || '';
    document.getElementById('leftPageContent').innerHTML = `<div class='curiosity-content'>${left}</div>`;
    document.getElementById('rightPageContent').innerHTML = `<div class='curiosity-content'>${right}</div>`;
}
function flipBook(direction) {
    const book = document.getElementById('bookFlip');
    book.classList.remove('flip-left', 'flip-right');
    void book.offsetWidth;
    book.classList.add(direction === 'left' ? 'flip-left' : 'flip-right');
    setTimeout(() => {
        renderBookFlip();
        book.classList.remove('flip-left', 'flip-right');
    }, 400);
}
document.getElementById('prevFlip').addEventListener('click', function() {
    if(flipIndex > 0) {
        flipIndex -= 2;
        flipBook('left');
    }
});
document.getElementById('nextFlip').addEventListener('click', function() {
    if(flipIndex < curiosidades.length - 2) {
        flipIndex += 2;
        flipBook('right');
    }
});
renderBookFlip();

// Adaptar CTA a tema
function adaptCTASection() {
    const cta = document.getElementById('cta-section');
    const theme = document.documentElement.getAttribute('data-bs-theme');
    if(theme === 'dark') {
        cta.style.background = 'linear-gradient(90deg, #232526 0%, #414345 100%)';
        cta.style.color = '#fff';
    } else {
        cta.style.background = 'linear-gradient(90deg, #f093fb 0%, #f5576c 100%)';
        cta.style.color = '#fff';
    }
}
adaptCTASection();
const observer = new MutationObserver(adaptCTASection);
observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
</script>
@endpush

<style>
.hero-section {
    color: #fff;
    background-size: cover;
    background-position: center;
    border-radius: 0 0 40px 40px;
}
.hero-section .highlight {
    color: #f9d423;
    background: linear-gradient(90deg, #f9d423 0%, #ff4e50 100%);
    padding: 0 8px;
    border-radius: 8px;
}
.hero-buttons .btn {
    font-size: 1.2rem;
    padding: 0.75rem 2rem;
    border-radius: 30px;
    margin-right: 10px;
    margin-bottom: 10px;
    box-shadow: 0 4px 16px rgba(52,152,219,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}
.hero-buttons .btn:hover {
    transform: translateY(-3px) scale(1.04);
    box-shadow: 0 8px 24px rgba(52,152,219,0.18);
}
.stat-card {
    border-radius: 18px;
    box-shadow: 0 4px 16px rgba(52,152,219,0.08);
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 8px 24px rgba(52,152,219,0.18);
}
.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
.feature-card {
    border-radius: 18px;
    box-shadow: 0 4px 16px rgba(52,152,219,0.08);
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}
.feature-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 8px 24px rgba(52,152,219,0.18);
}
.interactive-card {
    border-radius: 18px;
    background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
    box-shadow: 0 4px 16px rgba(52,152,219,0.08);
    transition: background 0.3s;
}
[data-bs-theme="dark"] .interactive-card {
    background: linear-gradient(135deg, #232526 0%, #414345 100%);
    color: #fff;
}
.cta-section {
    border-radius: 30px;
    margin: 2rem 0;
    transition: background 0.4s, color 0.4s;
}
.carousel .testimonial {
    background: rgba(255,255,255,0.95);
    color: #222;
}
[data-bs-theme="dark"] .carousel .testimonial {
    background: rgba(44,62,80,0.95);
    color: #fff;
}
.book-flip-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 2rem;
}
.book-flip {
    display: flex;
    align-items: stretch;
    justify-content: center;
    width: 420px;
    max-width: 100%;
    min-height: 180px;
    background: #f8fafc;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(52,152,219,0.10);
    position: relative;
    transition: box-shadow 0.3s;
    perspective: 1200px;
}
[data-bs-theme="dark"] .book-flip {
    background: #232526;
    box-shadow: 0 8px 32px rgba(44,62,80,0.18);
}
.book-page {
    flex: 1;
    background: #fff;
    min-width: 180px;
    padding: 2rem 1.2rem;
    border-radius: 12px 0 0 12px;
    box-shadow: 2px 0 8px rgba(52,152,219,0.04);
    font-size: 1.08rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s, color 0.3s;
    position: relative;
    z-index: 1;
}
.book-page.right-page {
    border-radius: 0 12px 12px 0;
    box-shadow: -2px 0 8px rgba(52,152,219,0.04);
}
[data-bs-theme="dark"] .book-page {
    background: #353b48;
    color: #fff;
}
.book-spine {
    width: 12px;
    background: linear-gradient(120deg, #e0eafc 60%, #bfc9d1 100%);
    border-radius: 8px;
    margin: 0 2px;
    box-shadow: 0 0 8px rgba(52,152,219,0.08);
    position: relative;
    z-index: 2;
}
[data-bs-theme="dark"] .book-spine {
    background: linear-gradient(120deg, #232526 60%, #414345 100%);
}
.book-arrow-flip {
    background: #fff;
    border: 1px solid #e0eafc;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    font-size: 1.5rem;
    color: #3498db;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(52,152,219,0.08);
    transition: background 0.2s, color 0.2s, transform 0.2s;
}
.book-arrow-flip:hover {
    background: #3498db;
    color: #fff;
    transform: scale(1.12);
}
[data-bs-theme="dark"] .book-arrow-flip {
    background: #353b48;
    border: 1px solid #414345;
    color: #fff;
}
.curiosity-content {
    text-align: center;
    font-size: 1.08rem;
    font-style: italic;
    color: #34495e;
}
[data-bs-theme="dark"] .curiosity-content {
    color: #bfc9d1;
}
/* Efecto flip */
.book-flip.flip-left .left-page {
    animation: flipLeft 0.4s;
}
.book-flip.flip-right .right-page {
    animation: flipRight 0.4s;
}
@keyframes flipLeft {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(-90deg); opacity: 0.5; }
}
@keyframes flipRight {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(90deg); opacity: 0.5; }
}
@media (max-width: 600px) {
    .book-flip {
        width: 98vw;
        min-width: 0;
    }
    .book-page {
        padding: 1rem 0.5rem;
        font-size: 0.98rem;
    }
}
.testimonial-theme {
    background: rgba(255,255,255,0.95);
    color: #222;
    transition: background 0.3s, color 0.3s;
}
[data-bs-theme="dark"] .testimonial-theme {
    background: rgba(44,62,80,0.95) !important;
    color: #fff !important;
}
.hero-section-library {
    background: linear-gradient(120deg, #f8f6f1 60%, #e7dac7 100%);
    border-radius: 0 0 40px 40px;
    box-shadow: 0 8px 32px rgba(52, 52, 52, 0.04);
    color: #3e2c18;
    border: 1.5rem solid #f3f1e7;
    margin-bottom: 2rem;
}
.library-highlight {
    font-family: 'Merriweather', serif;
    font-size: 1.1em;
    color: #b97a56;
    border-bottom: 4px solid #b97a56;
    padding: 0 4px 2px 4px;
    background: none;
    border-radius: 0;
    font-style: italic;
    box-shadow: none;
}
.btn-library-primary {
    background: linear-gradient(90deg, #b97a56 0%, #a67c52 100%);
    color: #fff;
    border: none;
    border-radius: 30px;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(185,122,86,0.08);
    transition: background 0.2s, box-shadow 0.2s;
}
.btn-library-primary:hover {
    background: linear-gradient(90deg, #a67c52 0%, #b97a56 100%);
    color: #fff;
    box-shadow: 0 8px 24px rgba(185,122,86,0.18);
}
.btn-outline-library {
    background: none;
    color: #b97a56;
    border: 2px solid #b97a56;
    border-radius: 30px;
    font-weight: 600;
    transition: background 0.2s, color 0.2s;
}
.btn-outline-library:hover {
    background: #b97a56;
    color: #fff;
}
[data-bs-theme="dark"] .hero-section-library {
    background: linear-gradient(120deg, #232526 60%, #414345 100%);
    color: #f3e9d2;
    border-color: #232526;
}
[data-bs-theme="dark"] .library-highlight {
    color: #f3e9d2;
    border-bottom: 4px solid #f3e9d2;
}
[data-bs-theme="dark"] .btn-library-primary {
    background: linear-gradient(90deg, #8d6742 0%, #b97a56 100%);
    color: #fff;
}
[data-bs-theme="dark"] .btn-outline-library {
    color: #f3e9d2;
    border-color: #f3e9d2;
}
[data-bs-theme="dark"] .btn-outline-library:hover {
    background: #f3e9d2;
    color: #232526;
}
.cta-section-library {
    background: #f8f6f1;
    border-radius: 2rem;
    margin: 2rem 0;
    box-shadow: 0 4px 24px rgba(52, 52, 52, 0.04);
    color: #3e2c18;
    border: 1.5rem solid #f3f1e7;
    transition: background 0.4s, color 0.4s;
}
[data-bs-theme="dark"] .cta-section-library {
    background: #232526;
    color: #f3e9d2;
    border-color: #232526;
}
</style>
@endsection

