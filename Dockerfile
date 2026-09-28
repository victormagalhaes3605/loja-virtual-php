FROM php:8.2-apache

# Instalar extensões necessárias (ex: PDO para a base de dados)
RUN docker-php-ext-install pdo pdo_mysql

# Ativar o mod_rewrite do Apache para URLs amigáveis
RUN a2enmod rewrite

# Copiar os ficheiros do projeto para a pasta web do Apache
COPY . /var/www/html/

# Apontar a raiz do Apache para a pasta public do teu projeto
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Copiar o script de entrada e dar permissões de execução
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Definir o script como o comando de arranque do contentor
CMD ["/usr/local/bin/docker-entrypoint.sh"]