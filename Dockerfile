# Step 1: Use official Apache base image
FROM php:8.0-apache

# Step 2: Install PHP 8, required packages, and PHP extensions
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*  # Clean up apt cache to reduce image size

# Step 3: Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Step 4: Enable Apache modules and PHP
RUN a2enmod rewrite

# Step 5: Configure Apache to use PHP-FPM
COPY ./httpd.conf /usr/local/apache2/conf/httpd.conf

# Step 6: Copy the application files to the container
COPY . /var/www/html/

# Step 7: Expose port 80 for Apache
EXPOSE 80

# Step 8: Start Apache in the foreground
CMD ["apachectl", "-D", "FOREGROUND"]