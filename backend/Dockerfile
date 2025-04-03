FROM php:8.2-fpm as build-stage
# Install system packages for PHP extensions and pdo_mysql

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install Laravel and dependencies
RUN composer install

# Now that Laravel is installed, require Breeze and install it
RUN composer require laravel/breeze
RUN php artisan breeze:install vue --no-interaction

# Remove the default nginx index page
RUN rm -rf /var/www/html

#COPY --from=build-stage /app/public/ /var/www/public/

# Ensure .env file exists
COPY .env /var/www/.env
RUN if [ ! -f .env ]; then cp .env.example .env; fi

RUN php artisan key:generate
RUN php artisan migrate:refresh

# Change current user to www
USER www-data
# Expose port 8000 and start php-fpm server
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]