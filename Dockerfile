FROM php:8.3-fpm

# --- Dépendances système nécessaires aux extensions PHP ---
# $PHPIZE_DEPS (gcc, make, autoconf, pkg-config...) : requis pour compiler
# les extensions via docker-php-ext-install, même celles sans lib externe.
RUN apt-get update && apt-get install -y \
        $PHPIZE_DEPS \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# --- Extensions PHP requises par le projet ---
RUN docker-php-ext-install pdo pdo_mysql mbstring intl opcache

# --- Installation de Composer (copié depuis l'image officielle) ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN if [ -f composer.json ]; then composer install --no-interaction --no-progress || true; fi

CMD ["php-fpm"]
