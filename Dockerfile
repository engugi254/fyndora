# Use official PHP with Apache
FROM php:8.2-apache

# Install system dependencies for Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions so Laravel can write logs/files
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 to the web
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
