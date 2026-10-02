FROM php:8.2-apache

# Устанавливаем расширения MySQL и другие необходимые
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем конфиг Apache для DocumentRoot
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Создаём папку public и устанавливаем права
RUN mkdir -p /var/www/html/public && chmod -R 755 /var/www/html

# Рабочая директория — public (DocumentRoot)
WORKDIR /var/www/html/public

# Открываем порт
EXPOSE 80

CMD ["apache2-foreground"]
