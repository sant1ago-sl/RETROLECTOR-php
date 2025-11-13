FROM php:8.2-fpm

# Argumentos para la construcción
ARG user=laravel
ARG uid=1000

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar extensiones PHP adicionales
RUN pecl install redis && docker-php-ext-enable redis

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario del sistema para ejecutar comandos Composer y Artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de configuración
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copiar archivos de la aplicación
COPY . /var/www

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs
RUN npm install
RUN npm run build

# Configurar permisos
RUN chown -R $user:$user /var/www
RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap/cache

# Crear enlace simbólico para storage
RUN php artisan storage:link

# Configurar supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puerto
EXPOSE 9000

# Comando por defecto
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 