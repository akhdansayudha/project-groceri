# 1. Gunakan Image PHP 8.2 dengan Apache
FROM php:8.2-apache

# 2. Install Library System (Tambahkan libpq-dev untuk Postgres)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# 3. Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Install Ekstensi PHP (Tambahkan pdo_pgsql dan pgsql)
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# 5. Konfigurasi Apache Document Root ke folder 'public'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 6. Aktifkan Mod Rewrite
RUN a2enmod rewrite

# 7. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Set folder kerja
WORKDIR /var/www/html

# 9. Copy kodingan
COPY . .

# 10. Install Vendor
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 11. Permission Storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Expose Port 80
EXPOSE 80

# 13. Jalankan Apache
CMD ["apache2-foreground"]
