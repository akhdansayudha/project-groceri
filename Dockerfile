# 1. Gunakan Image PHP 8.2 dengan Apache
FROM php:8.2-apache

# 2. Install Library System yang dibutuhkan Laravel (Zip, Git, Gambar, dll)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# 3. Bersihkan cache apt untuk memperkecil ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Install Ekstensi PHP yang wajib untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 5. Konfigurasi Apache Document Root ke folder 'public'
# (Ini PENTING supaya Azure langsung baca folder public, bukan root)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 6. Aktifkan Mod Rewrite (Supaya URL cantik Laravel jalan)
RUN a2enmod rewrite

# 7. Install Composer (Versi terbaru)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Set folder kerja
WORKDIR /var/www/html

# 9. Copy seluruh kodingan dari laptop ke dalam Image
COPY . .

# 10. Install Vendor (Jantungnya Laravel)
# --no-dev: tidak menginstall library testing (lebih ringan untuk production)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 11. Atur Hak Akses (Permission) Folder Storage
# Mengubah pemilik folder ke www-data (user Apache) agar bisa ditulis
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Buka Port 80
EXPOSE 80

# 13. Jalankan Apache
CMD ["apache2-foreground"]
