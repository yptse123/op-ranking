FROM --platform=${BUILDPLATFORM:-linux/amd64} php:5.6-apache

# Update sources.list to use archive repositories for Debian Stretch
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list

# Disable repository signature checking for archive repositories
RUN echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until && \
    echo 'APT::Get::AllowUnauthenticated "true";' >> /etc/apt/apt.conf.d/99allow-unauth

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng12-dev \
    mysql-client \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set ServerName to suppress the warning message
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache for /op-ranking-page path
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|' /etc/apache2/sites-available/000-default.conf

# Create op-ranking-page directory
RUN mkdir -p /var/www/html/op-ranking-page

# Copy application files to the /op-ranking-page directory
COPY . /var/www/html/op-ranking-page/

# Create an .htaccess file to handle the base URL
RUN echo 'DirectoryIndex index.php' > /var/www/html/op-ranking-page/.htaccess

# Copy initialization scripts
COPY docker-entrypoint.sh /usr/local/bin/
COPY docker-php-init.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-php-init.sh

# Update the database configuration to use Docker environment
# Update the database configuration to use Docker environment
RUN sed -i "s/CONST DB_HOST = \"localhost\"/CONST DB_HOST = \"${DB_HOST}\"/g" /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i "s/CONST DB_USERNAME = \"root\"/CONST DB_USERNAME = \"${DB_USER}\"/g" /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i "s/CONST DB_PASSWORD = \"\"/CONST DB_PASSWORD = \"${DB_PASS}\"/g" /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php \
    && sed -i "s/CONST DB_DBNAME = \"op_ranking\"/CONST DB_DBNAME = \"${DB_NAME}\"/g" /var/www/html/op-ranking-page/admin/PM/classes/Data/Setting/Database.class.php

# Create a simple landing page in the root directory
RUN echo '<html><head><meta http-equiv="refresh" content="0;URL=/op-ranking-page/"></head><body>Redirecting...</body></html>' > /var/www/html/index.html

# Set the entrypoint to our init script which will then call docker-entrypoint.sh
ENTRYPOINT ["docker-php-init.sh"]
CMD ["apache2-foreground"]