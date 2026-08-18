# ---- Stage 1: Build frontend assets (Vite) ----
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# ---- Stage 2: PHP application ----
FROM php:8.2-cli AS app

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy built frontend assets from stage 1
COPY --from=node-build /app/public/build ./public/build

# Install PHP dependencies (production, no dev deps)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions for storage & cache
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && chmod -R 775 storage bootstrap/cache

# Cache Laravel config/routes/views at build time (safe defaults; can be skipped if it causes issues)
# NOTE: if this fails at build due to missing runtime env vars, remove these two lines
# and instead run them in the start command below.

EXPOSE 10000

# Render injects $PORT at runtime; php artisan serve binds to it.
# We also run migrations automatically on each deploy (--force needed for production).
CMD php artisan config:clear \
    && php artisan migrate --force \
    && php artisan storage:link || true \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
