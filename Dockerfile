FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y --no-install-recommends \
    libzip-dev unzip libicu-dev libonig-dev libxml2-dev \
    libjpeg-dev libpng-dev libfreetype6-dev libpq-dev curl gnupg \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd intl mbstring exif bcmath zip pcntl pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY package*.json vite.config.js ./
RUN npm install

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN npm run build

RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

EXPOSE 5000

# ✅ Migrate aqui, não no build
CMD php artisan migrate --force && php -S 0.0.0.0:5000 -t public
