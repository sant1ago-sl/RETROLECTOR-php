<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ $theme ?? 'claro' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Retrolector - Biblioteca Digital')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--accent-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn {
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            padding: 0.5rem 1.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent-color), #2980b9);
        }

        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #229954);
        }

        .btn-warning {
            background: linear-gradient(45deg, var(--warning-color), #e67e22);
        }

        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #c0392b);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border-bottom: none;
            font-weight: 600;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, var(--accent-color), #2980b9) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, var(--success-color), #229954) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, var(--warning-color), #e67e22) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, var(--danger-color), #c0392b) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #3498db, #2980b9) !important;
        }

        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .dropdown-item {
            border-radius: 10px;
            margin: 2px 8px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(45deg, var(--accent-color), #2980b9);
            color: white;
            transform: translateX(5px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        }

        footer h5, footer h6 {
            color: #ecf0f1;
            font-weight: 600;
        }

        footer .social-links a {
            display: inline-block;
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            transition: all 0.3s ease;
        }

        footer .social-links a:hover {
            background: var(--accent-color);
            transform: translateY(-3px);
        }

        footer ul li a {
            transition: all 0.3s ease;
        }

        footer ul li a:hover {
            color: var(--accent-color) !important;
            padding-left: 5px;
        }

        footer .contact-info p {
            transition: all 0.3s ease;
        }

        footer .contact-info p:hover {
            color: var(--accent-color) !important;
        }

        /* Dark theme adjustments */
        [data-bs-theme="dark"] {
            --primary-color: #ecf0f1;
            --secondary-color: #bdc3c7;
            --dark-color: #2c3e50;
        }

        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        [data-bs-theme="dark"] .navbar {
            background: rgba(44, 62, 80, 0.95) !important;
        }

        [data-bs-theme="dark"] .main-content {
            background: rgba(44, 62, 80, 0.95);
        }

        [data-bs-theme="dark"] .card {
            background: rgba(52, 73, 94, 0.9);
            color: #ecf0f1;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background: rgba(52, 73, 94, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #ecf0f1;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background: var(--accent-color);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin: 10px;
                padding: 20px;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm animate__animated animate__fadeInDown" style="backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="fas fa-book-reader fa-lg text-accent"></i>
                <span class="fw-bold">Retrolector</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1" href="{{ route('home') }}">
                            <i class="fas fa-home"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1" href="{{ route('books.catalog') }}">
                            <i class="fas fa-books"></i>Catálogo
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                    @endauth
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="{{ route('admin.compras.index') }}">
                                <i class="fas fa-shopping-cart"></i>Compras
                            </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <!-- Botón de cambio de tema animado -->
                    <li class="nav-item">
                        <button id="theme-toggle" class="btn btn-theme-toggle d-flex align-items-center gap-2" aria-label="Cambiar tema">
                            <span class="theme-icon-light"><i class="fas fa-sun"></i></span>
                            <span class="theme-icon-dark"><i class="fas fa-moon"></i></span>
                        </button>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i>Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary d-flex align-items-center gap-1" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i>Registrarse
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="{{ route('admin.login') }}" title="Acceso Administrativo">
                                <i class="fas fa-user-shield"></i><span class="d-none d-md-inline">Admin</span>
                            </a>
                        </li>
                    @else
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Notificaciones</span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="refreshNotificationDropdown()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </h6>
                                <div id="notifications-dropdown">
                                    <div class="dropdown-item text-center text-muted">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Cargando...
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ auth()->user()->isAdmin() ? route('admin.notifications.index') : route('user.notifications') }}">
                                    <i class="fas fa-list me-2"></i>Ver todas
                                </a>
                            </div>
                        </li>

                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->nombre }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                    <li><h6 class="dropdown-header">Administración</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cogs me-2"></i>Panel Admin
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li><h6 class="dropdown-header">Mi Cuenta</h6></li>
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                                        <i class="fas fa-user me-2"></i>Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.loans') }}">
                                        <i class="fas fa-book me-2"></i>Mis Préstamos
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.reservations') }}">
                                        <i class="fas fa-calendar-check me-2"></i>Mis Reservas
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.favorites') }}">
                                        <i class="fas fa-heart me-2"></i>Mis Favoritos
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" style="margin-top: 80px;">
        @yield('content')
                </div>

    <!-- Footer -->
    <footer class="footer-modern bg-gradient-footer text-light py-5 mt-5 animate__animated animate__fadeInUp mt-auto">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-book-open fa-lg text-accent"></i>Retrolector
                    </h5>
                    <p class="text-footer-muted">
                        Tu biblioteca digital moderna. Accede a miles de libros, gestiona préstamos y descubre nuevas lecturas.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" class="social-icon animate__animated animate__pulse animate__infinite"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon animate__animated animate__pulse animate__infinite animate__delay-1s"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon animate__animated animate__pulse animate__infinite animate__delay-2s"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon animate__animated animate__pulse animate__infinite animate__delay-3s"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="footer-link"><i class="fas fa-home me-1"></i>Inicio</a></li>
                        <li><a href="{{ route('books.catalog') }}" class="footer-link"><i class="fas fa-search me-1"></i>Catálogo</a></li>
                        <li><a href="{{ route('books.recommendations') }}" class="footer-link"><i class="fas fa-star me-1"></i>Recomendaciones</a></li>
                        <li><a href="{{ route('books.reading-clubs') }}" class="footer-link"><i class="fas fa-users me-1"></i>Clubes de Lectura</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="mb-3">Servicios</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link"><i class="fas fa-book me-1"></i>Préstamos</a></li>
                        <li><a href="#" class="footer-link"><i class="fas fa-calendar-check me-1"></i>Reservas</a></li>
                        <li><a href="#" class="footer-link"><i class="fas fa-heart me-1"></i>Favoritos</a></li>
                        <li><a href="#" class="footer-link"><i class="fas fa-chart-line me-1"></i>Analíticas</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h6 class="mb-3">Contacto</h6>
                    <div class="contact-info">
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Av. Biblioteca 123, Ciudad</p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i>+51 987 654 321</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i>info@retrolector.com</p>
                        <p class="mb-2"><i class="fas fa-clock me-2"></i>Lun - Vie: 8:00 - 20:00</p>
                </div>
            </div>
            </div>
            <hr class="my-4 bg-secondary">
                <div class="row align-items-center">
                    <div class="col-md-6">
                    <p class="mb-0 text-footer-muted">&copy; {{ date('Y') }} Retrolector. Todos los derechos reservados. santiago</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link me-3">Política de Privacidad</a>
                    <a href="#" class="footer-link me-3">Términos de Uso</a>
                    <a href="#" class="footer-link">Ayuda</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
            }, 5000);
            });
        });

        // Confirm logout
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForm = document.querySelector('form[action*="logout"]');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    if (!confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    e.preventDefault();
                    }
                        });
                    }
            });

        // Loading states for buttons
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span class="loading"></span> Procesando...';
                        submitBtn.disabled = true;
                        
                        // Re-enable after 10 seconds as fallback
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 10000);
                    }
                });
            });
        });
    </script>

    @stack('scripts')
    <style>
        .navbar, .footer-modern {
            transition: background 0.4s, color 0.4s;
        }
        .navbar {
            background: rgba(255,255,255,0.95) !important;
        }
        .footer-modern {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        }
        .footer-link {
            color: #bfc9d1;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s, padding-left 0.3s;
        }
        .footer-link:hover {
            color: #fff;
            padding-left: 8px;
        }
        .text-footer-muted {
            color: #bfc9d1;
        }
        .social-icon {
            display: inline-block;
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            text-align: center;
            line-height: 38px;
            color: #fff;
            font-size: 1.2rem;
            margin-right: 8px;
            transition: background 0.3s, transform 0.3s;
        }
        .social-icon:hover {
            background: #3498db;
            color: #fff;
            transform: translateY(-4px) scale(1.1);
        }
        .btn-theme-toggle {
            background: linear-gradient(45deg, #f5f6fa, #d6e4ff);
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
            position: relative;
            transition: background 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        .btn-theme-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px #3498db44;
        }
        .theme-icon-light, .theme-icon-dark {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            transition: opacity 0.3s, transform 0.3s;
        }
        [data-bs-theme="dark"] .navbar {
            background: rgba(44,62,80,0.98) !important;
        }
        [data-bs-theme="dark"] .footer-modern {
            background: linear-gradient(135deg, #23272f 0%, #181c22 100%) !important;
        }
        [data-bs-theme="dark"] .footer-link, [data-bs-theme="dark"] .text-footer-muted {
            color: #8b9bb4;
        }
        [data-bs-theme="dark"] .footer-link:hover {
            color: #fff;
        }
        [data-bs-theme="dark"] .social-icon {
            background: rgba(44,62,80,0.5);
            color: #fff;
        }
        [data-bs-theme="dark"] .social-icon:hover {
            background: #2980b9;
        }
        /* Animación de icono de tema */
        [data-bs-theme="light"] .theme-icon-light {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        [data-bs-theme="light"] .theme-icon-dark {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.5);
        }
        [data-bs-theme="dark"] .theme-icon-light {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.5);
        }
        [data-bs-theme="dark"] .theme-icon-dark {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
    <script>
        // Botón de cambio de tema animado
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if(themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    fetch(`{{ url('theme/change') }}/${newTheme}`);
                });
            }
        });
    </script>
    <!-- Scripts para notificaciones en tiempo real -->
    <script>
        let notificationRefreshInterval;

        // Inicializar notificaciones
        document.addEventListener('DOMContentLoaded', function() {
            refreshNotificationDropdown();
            // Actualizar cada 30 segundos
            notificationRefreshInterval = setInterval(refreshNotificationDropdown, 30000);
        });

        // Refrescar dropdown de notificaciones
        function refreshNotificationDropdown() {
            const dropdown = document.getElementById('notifications-dropdown');
            const badge = document.getElementById('notification-badge');
            
            @if(auth()->check())
                fetch('{{ auth()->user()->isAdmin() ? route("admin.notifications.get-unread") : route("user.notifications.unread") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Actualizar contador
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }

                        // Actualizar contenido del dropdown
                        if (data.notificaciones && data.notificaciones.length > 0) {
                            let html = '';
                            data.notificaciones.forEach(notif => {
                                html += `
                                    <div class="dropdown-item d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-${notif.tipo} me-2">${notif.tipo}</span>
                                                <strong class="small">${notif.titulo}</strong>
                                            </div>
                                            <div class="text-muted small">${notif.mensaje}</div>
                                            <div class="text-muted small">${new Date(notif.created_at).toLocaleString()}</div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-success ms-2" onclick="markNotificationAsRead(${notif.id})" title="Marcar como leída">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                `;
                            });
                            dropdown.innerHTML = html;
                        } else {
                            dropdown.innerHTML = '<div class="dropdown-item text-center text-muted">No hay notificaciones nuevas</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error cargando notificaciones:', error);
                        dropdown.innerHTML = '<div class="dropdown-item text-center text-danger">Error al cargar notificaciones</div>';
                    });
            @else
                // No hacer nada si no hay usuario autenticado
                return;
            @endif
        }

        // Marcar notificación como leída
        function markNotificationAsRead(id) {
            fetch(`/user/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    refreshNotificationDropdown();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Limpiar intervalo al salir
        window.addEventListener('beforeunload', function() {
            if (notificationRefreshInterval) clearInterval(notificationRefreshInterval);
        });
    </script>
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-lg shadow-lg rounded-circle position-fixed" style="bottom: 40px; right: 40px; z-index: 1050; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; font-size: 2rem; animation: bounceIn 1s;">
                <i class="fas fa-plus"></i>
            </a>
            <style>
                @keyframes bounceIn {
                    0% { transform: scale(0.5); opacity: 0; }
                    60% { transform: scale(1.2); opacity: 1; }
                    100% { transform: scale(1); }
                }
                .btn[style*="position: fixed"]:hover {
                    background: linear-gradient(45deg, #f39c12, #e67e22);
                    color: #fff;
                    transform: scale(1.1);
                }
            </style>
        @endif
    @endauth
    <!-- Toasts de mensajes -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(function (toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        });
    </script>
</body>
</html> 