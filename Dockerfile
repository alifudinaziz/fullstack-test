FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install Alpine packages and PHP extensions
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    zlib-dev \
    libzip-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        gd \
        intl

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy app source
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install JS dependencies and build frontend
RUN npm install && npm run build

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

EXPOSE 9000

CMD ["php-fpm"]
