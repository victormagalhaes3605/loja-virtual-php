FROM php:8.2-apache

# Instalar extensões necessárias (PDO para base de dados)
RUN docker-php-ext-install pdo pdo_mysql

# Copiar os ficheiros do projeto
COPY . /var/www/html/

# Mudar a raiz do Apache para a pasta public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Configurar o Apache para escutar na porta correta do Railway
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Definir a porta padrão para o Railway (Railway usa frequentemente 8080 por defeito no Docker se mapeado, ou lê o ENV PORT)
ENV PORT=8080

# Ativar mod_rewrite
RUN a2enmod rewrite

EXPOSE 8080