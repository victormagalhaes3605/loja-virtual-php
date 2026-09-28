FROM php:8.2-apache

# Instalar extensões necessárias (ex: PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Copiar os ficheiros do projeto para a pasta web do Apache
COPY . /var/www/html/

# Apontar a raiz do Apache para a pasta public do teu projeto
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Configurar o Apache para escutar na porta dinâmica exigida pelo Railway ($PORT)
RUN sed -i "s/Listen 80/Listen \${PORT}/g" /etc/apache2/ports.conf
RUN sed -i "s/:80/:\${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Ativar mod_rewrite para amigabilidade de URLs
RUN a2enmod rewrite