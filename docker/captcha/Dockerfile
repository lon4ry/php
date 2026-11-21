# Образ CAPTCHA для хостинга (Render): код копируется внутрь, контекст сборки — корень репозитория
FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd \
    && rm -rf /var/lib/apt/lists/*

COPY 02-captcha/ /var/www/html/
