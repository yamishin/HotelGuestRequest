FROM richarvey/nginx-php-fpm:3.1.6

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer dependencies to create the vendor folder
RUN composer install --no-dev --optimize-autoloader

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

CMD ["/start.sh"]