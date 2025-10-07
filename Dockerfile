# Use PHP 8.2 with extensions support
FROM php:8.2-cli

# Install required extensions (pdo_mysql for MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Expose port for Render
EXPOSE 10000

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
