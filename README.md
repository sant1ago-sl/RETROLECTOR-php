# 📚 Retrolector - Sistema de Gestión de Biblioteca Digital

**Retrolector** es una plataforma moderna de gestión bibliotecaria desarrollada con Laravel 12, que combina funcionalidades tradicionales de biblioteca con características digitales avanzadas para ofrecer una experiencia de lectura completa y social.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite)

## ✨ Características Principales

### 📖 Gestión de Libros
- **Catálogo completo** con búsqueda avanzada y filtros múltiples
- **Sistema dual**: libros físicos y digitales (PDF)
- **Gestión de stock** y ubicación física
- **Sistema de precios** diferenciado (compra física/online, préstamo físico/online)
- **Vistas previas** de contenido con límites configurables

### 🔄 Préstamos y Reservas
- **Sistema de préstamos** con fechas de devolución y seguimiento
- **Reservas inteligentes** con notificaciones automáticas
- **Control de vencimientos** y multas automáticas
- **Historial completo** de préstamos por usuario

### 💰 Sistema de Compras
- **Compra de libros** físicos y digitales
- **Carrito de compras** integrado
- **Múltiples métodos de pago** (por implementar)
- **Gestión de inventario** automática

### 👥 Gestión de Usuarios
- **Sistema de roles**: Administradores y Clientes
- **Perfiles completos** con historial de actividad
- **Preferencias personalizables** (idioma, tema claro/oscuro)
- **Sistema de reputación** basado en préstamos

### 💬 Funcionalidades Sociales
- **Reseñas y calificaciones** de libros con moderación
- **Sistema de mensajería** interna entre usuarios
- **Clubes de lectura** comunitarios
- **Análisis de lectura** con estadísticas personales

### 🔔 Notificaciones y Comunicación
- **Sistema de notificaciones** en tiempo real
- **Mensajes automáticos** (bienvenida, recordatorios)
- **Alertas de disponibilidad** para libros reservados

### 📊 Panel de Administración
- **Dashboard completo** con métricas en tiempo real
- **Gestión de usuarios**, libros, categorías y autores
- **Moderación de contenido** (reseñas, mensajes)
- **Reportes y estadísticas** del sistema
- **Configuración del sistema** centralizada

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 12.x** - Framework PHP
- **PHP 8.2+** - Lenguaje de programación
- **MySQL** - Base de datos relacional
- **Eloquent ORM** - Mapeo objeto-relacional

### Frontend
- **Tailwind CSS 4.x** - Framework de utilidades CSS
- **Vite 6.x** - Bundler y herramienta de desarrollo
- **JavaScript ES6+** - Interactividad del lado del cliente
- **Alpine.js** - JavaScript reactivo (opcional)

### Características Técnicas
- **Arquitectura MVC** - Patrón de diseño bien estructurado
- **API RESTful** - Endpoints para integraciones futuras
- **Sistema de autenticación** nativo de Laravel
- **Sistema de colas** para procesamiento asíncrono
- **Sistema de caché** optimizado
- **Internacionalización** (español/inglés)

## 📁 Estructura del Proyecto

```
retrolector/
├── app/
│   ├── Models/                 # Modelos de datos
│   │   ├── Libro.php           # Entidad libro con relaciones
│   │   ├── Usuario.php         # Entidad usuario con autenticación
│   │   ├── Prestamo.php        # Gestión de préstamos
│   │   ├── Reserva.php         # Sistema de reservas
│   │   ├── Compra.php          # Gestión de compras
│   │   ├── Resena.php          # Reseñas y calificaciones
│   │   └── Mensaje.php         # Sistema de mensajería
│   ├── Http/Controllers/       # Controladores
│   ├── Providers/              # Proveedores de servicios
│   └── Console/                # Comandos Artisan
├── database/
│   ├── migrations/             # Migraciones de base de datos
│   ├── seeders/                # Datos de prueba
│   └── factories/              # Factories para testing
├── resources/
│   ├── views/                  # Vistas Blade
│   ├── lang/                   # Internacionalización
│   ├── css/                    # Estilos Tailwind CSS
│   └── js/                     # JavaScript
├── routes/
│   ├── web.php                 # Rutas web
│   └── api.php                 # Rutas API
├── config/                     # Configuraciones
└── public/                     # Assets públicos
```

## 🚀 Instalación y Configuración

### Prerrequisitos
- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- MySQL 5.7+ o MariaDB
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/sant1ago-sl/retrolector.git
   cd retrolector
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias JavaScript**
   ```bash
   npm install
   ```

4. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar base de datos**
   Editar el archivo `.env` con tus credenciales de base de datos:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=retrolector
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Ejecutar migraciones y seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Compilar assets**
   ```bash
   npm run build
   ```

8. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

### Desarrollo
Para desarrollo con hot reload:
```bash
npm run dev
```

## 👤 Usuarios por Defecto

Después de ejecutar los seeders, tendrás estos usuarios:

### Administrador
- **Email:** admin@retrolector.com
- **Contraseña:** password
- **Acceso:** Panel completo de administración

### Usuario Demo
- **Email:** usuario@ejemplo.com  
- **Contraseña:** password
- **Acceso:** Funcionalidades de cliente

## 📊 Base de Datos

### Diagrama Entidad-Relación Principal

```
Usuario (1) ──── (n) Prestamo
   │                   │
   │                   │
   │              (1) Libro
   │                   │
   │                   │
(n) Resena          (n) Reserva
   │                   │
   │                   │
   └─── (n) Mensaje ───┘
```

### Tablas Principales
- **usuarios** - Información de usuarios y autenticación
- **libros** - Catálogo completo de libros
- **prestamos** - Registro de préstamos activos e históricos
- **reservas** - Sistema de reservas de libros
- **compras** - Historial de compras
- **resenas** - Reseñas y calificaciones
- **mensajes** - Mensajería interna
- **notificaciones** - Sistema de alertas

## 🔧 Comandos Artisan Útiles

```bash
# Generar datos de prueba
php artisan db:seed

# Limpiar caché
php artisan optimize:clear

# Crear usuario administrador
php artisan make:user --admin

# Generar reportes
php artisan generate:reports

# Procesar colas de notificaciones
php artisan queue:work
```

## 🌐 API Endpoints

El sistema incluye una API RESTful para integraciones:

### Libros
- `GET /api/libros` - Listar libros con filtros
- `GET /api/libros/{id}` - Obtener libro específico
- `POST /api/libros` - Crear nuevo libro (admin)
- `PUT /api/libros/{id}` - Actualizar libro (admin)

### Usuarios  
- `GET /api/usuarios` - Listar usuarios (admin)
- `GET /api/usuarios/{id}` - Perfil de usuario
- `POST /api/auth/login` - Autenticación
- `POST /api/auth/register` - Registro

### Préstamos
- `GET /api/prestamos` - Mis préstamos (auth)
- `POST /api/prestamos` - Solicitar préstamo (auth)
- `PUT /api/prestamos/{id}/devolver` - Devolver libro (auth)

## 🎨 Personalización

### Temas
El sistema soporta temas claro y oscuro. Los usuarios pueden cambiar su preferencia en el perfil.

### Idiomas
- Español (predeterminado)
- Inglés

Para añadir nuevos idiomas:
1. Crear archivo en `resources/lang/{codigo}/`
2. Actualizar configuración en `config/app.php`

### Estilos
Los estilos usan Tailwind CSS con configuración personalizada en:
- `resources/css/app.css`
- `tailwind.config.js`

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Si encuentras problemas o tienes preguntas:

1. Revisa la documentación en [Wiki](../../wiki)
2. Abre un issue en GitHub
3. Contacta al equipo de desarrollo

---

**Desarrollado con ❤️ usando Laravel y Tailwind CSS**

*¿Te gusta Retrolector? ¡Dale una estrella ⭐ al proyecto!*