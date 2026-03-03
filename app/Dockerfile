# Базовый образ PHP с поддержкой FPM
FROM php:8.4-fpm

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Устанавливаем необходимые пакеты для работы Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    locales \
    zip \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add Composer to the PATH
ENV PATH="$PATH:/usr/local/bin"

# Создаем пользователя и группу для работы с приложением
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Копируем файлы проекта в рабочую директорию
COPY . /var/www
COPY --chown=www:www . /var/www

# Меняем пользователя на www
USER root

# Открываем порт для php-fpm
EXPOSE 8000

# Запускаем php-fpm сервер
CMD ["php-fpm"]
