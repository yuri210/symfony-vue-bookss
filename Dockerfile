FROM php:8.2-cli

# Paquetes/Extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia el código (ok en dev; usa volúmenes al correr)
COPY . .

EXPOSE 8000

# Servidor embebido de PHP (dev)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
