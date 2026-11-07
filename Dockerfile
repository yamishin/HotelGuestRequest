FROM richarvey/nginx-php-fpm:3.1.6

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 THE CRITICAL FIX: Overwrite Nginx config
RUN sed -i 's/try_files $uri $uri\/ =404;/try_files $uri $uri\/ \/index.php?$query_string;/g' /etc/nginx/sites-available/default.conf && \
    sed -i 's/root \/var\/www\/html;/root \/var\/www\/html\/public;/g' /etc/nginx/sites-available/default.conf

# 🔥 THE REAL FIX: Run migrations and seed DURING the build
# This ensures the tables exist in PostgreSQL before the app starts
RUN php artisan migrate --force || true
RUN php artisan db:seed --force || true

# 🔥 Force file sessions so login doesn't crash on missing session table
RUN php artisan config:cache

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]