# Use the official PHP image
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies and required extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \  
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip  

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the existing application
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]

