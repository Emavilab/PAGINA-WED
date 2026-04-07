FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite headers

# Mantiene la compatibilidad con rutas existentes del proyecto:
# http://localhost:8080/PAGINA%20WED/
RUN mkdir -p "/var/www/html/PAGINA WED" \
    && chown -R www-data:www-data /var/www/html

WORKDIR "/var/www/html/PAGINA WED"
