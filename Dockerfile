FROM --platform=${BUILDPLATFORM:-linux/amd64} php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    default-mysql-client \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set ServerName to suppress the warning message
RUN echo "ServerName db" >> /etc/apache2/apache2.conf

# Configure Apache for /op-ranking-page path
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|' /etc/apache2/sites-available/000-default.conf

# Create op-ranking-page directory
RUN mkdir -p /var/www/html/op-ranking-page

# Copy application files to the /op-ranking-page directory
COPY . /var/www/html/op-ranking-page/

# Create an .htaccess file to handle the base URL
RUN echo 'DirectoryIndex index.php' > /var/www/html/op-ranking-page/.htaccess

# Copy database migration script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Update the database configuration to use Docker environment
RUN sed -i 's/CONST DB_HOST = "db"/CONST DB_HOST = "db"/g' /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i 's/CONST DB_USERNAME = "root"/CONST DB_USERNAME = "root"/g' /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i 's/CONST DB_PASSWORD = ""/CONST DB_PASSWORD = "password"/g' /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i 's/CONST DB_DBNAME = "op_ranking"/CONST DB_DBNAME = "op_ranking"/g' /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php

# Create a simple landing page in the root directory
RUN echo '<html><head><meta http-equiv="refresh" content="0;URL=/op-ranking-page/"></head><body>Redirecting...</body></html>' > /var/www/html/index.html

# Set the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]