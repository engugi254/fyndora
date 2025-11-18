FROM php:8.2-apache

# ---------------------------
# Install system dependencies
# ---------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql

# ---------------------------
# Enable Apache rewrite
# ---------------------------
RUN a2enmod rewrite

# ---------------------------
# Set Laravel document root
# ---------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# ---------------------------
# Copy project files
# ---------------------------
COPY . /var/www/html

WORKDIR /var/www/html

# ---------------------------
# Install Composer
# ---------------------------
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# ---------------------------
# Install PHP dependencies
# ---------------------------
RUN composer install --no-dev --optimize-autoloader

# ---------------------------
# Install Node.js (required for Vite build)
# ---------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# ---------------------------
# Build frontend assets (Vite)
# ---------------------------
RUN npm install && npm run build

# ---------------------------
# Fix permissions
# ---------------------------
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# ---------------------------
# Laravel setup
# ---------------------------
RUN php artisan key:generate --force

# Migrate database but ignore errors (e.g. table already exists)
RUN php artisan migrate --force || true

# ---------------------------
# Start Apache
# ---------------------------
EXPOSE 80
CMD ["apache2-foreground"]
