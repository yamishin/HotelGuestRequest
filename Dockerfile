FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html
WORKDIR /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Nginx Routing fix
RUN sed -i 's/try_files $uri $uri\/ =404;/try_files $uri $uri\/ \/index.php?$query_string;/g' /etc/nginx/sites-available/default.conf && \
    sed -i 's/root \/var\/www\/html;/root \/var\/www\/html\/public;/g' /etc/nginx/sites-available/default.conf

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# 🔥 THE FIX: Override the default start command to run migrations FIRST, then start the server
CMD php artisan migrate:fresh --force && php artisan db:seed --force && /start.sh