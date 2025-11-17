# Use official PHP with Apache
FROM php:8.2-apache

# Install system dependencies for Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Copy project files to Apache web root
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions so Apache can write logs and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Use Laravel public folder as web root
CMD ["./vendor/bin/heroku-php-apache2", "public/"]
