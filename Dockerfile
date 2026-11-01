FROM php:8.2-apache

# Dependências de sistema para o Moodle 4.5
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    postgresql-client \
    git \
    unzip \
    ghostscript \
    && rm -rf /var/lib/apt/lists/*

# Configuração e compilação das extensões PHP oficiais do Moodle
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        intl \
        zip \
        soap \
        opcache \
        mysqli \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        exif \
        bcmath

# Configurações de performance e upload recomendadas pelo Moodle
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable=1'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'max_execution_time=600'; \
    echo 'max_input_vars=5000'; \
    echo 'memory_limit=256M'; \
} > /usr/local/etc/php/conf.d/moodle.ini

# Módulos Apache essenciais
RUN a2enmod rewrite headers deflate

# Configuração do VirtualHost
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Diretório de trabalho
WORKDIR /var/www/html

# Copia código completo do Moodle-Base com o plugin local/monitoria
COPY . /var/www/html/
COPY docker/config.php /var/www/html/config.php

# Cria diretório de dados persistente do Moodle
RUN mkdir -p /var/www/moodledata && \
    chown -R www-data:www-data /var/www/html /var/www/moodledata && \
    chmod -R 777 /var/www/moodledata

# Script de entrada para execução no Render / Docker
COPY docker/entrypoint.sh /entrypoint.sh
RUN tr -d '\r' < /entrypoint.sh > /entrypoint_clean.sh && \
    mv /entrypoint_clean.sh /entrypoint.sh && \
    chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
