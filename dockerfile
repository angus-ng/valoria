# Use the official PHP image for Laravel
FROM php:8.4-fpm

# Install dependencies needed for Laravel (and MySQL)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    git \
    curl \
    libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm for Vue.js
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Install Composer (Laravel dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory for Laravel
WORKDIR /var/www/html

# Copy Laravel files into the container
COPY . /var/www/html

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set the working directory for Vue.js
WORKDIR /var/www/html/frontend  # Assuming your Vue app is in the 'frontend' folder

# Install Vue.js dependencies
RUN npm install

# Expose ports for both Laravel (80) and Vue.js (5173)
EXPOSE 80 5173

# Command to run both Laravel and Vue.js
CMD php artisan serve --host=0.0.0.0 --port=80 & npm run dev --prefix /var/www/html
