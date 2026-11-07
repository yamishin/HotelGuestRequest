FROM richarvey/nginx-php-fpm:3.1.6

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 THE CRITICAL FIX: Overwrite Nginx config to include try_files
RUN sed -i 's/try_files $uri $uri\/ =404;/try_files $uri $uri\/ \/index.php?$query_string;/g' /etc/nginx/sites-available/default.conf && \
    sed -i 's/root \/var\/www\/html;/root \/var\/www\/html\/public;/g' /etc/nginx/sites-available/default.conf

# 🔥 THE MISSING LINE: Make the deploy script executable
RUN chmod +x 00-laravel-deploy.sh

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