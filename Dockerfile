FROM php:8.2-apache

# Copiar os ficheiros do projeto para o diretório do Apache
COPY . /var/www/html/

# Mudar a raiz do Apache (DocumentRoot) para a pasta public do projeto
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Dar permissões e ativar mod_rewrite se necessário
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 80