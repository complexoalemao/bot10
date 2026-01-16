# Base da imagem
FROM php:8.2-apache

# Atualiza pacotes e instala dependências para cURL, OpenSSL e compilação
RUN apt-get update && apt-get install -y \
    openssl \
    libcurl4-openssl-dev \
    pkg-config \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Habilita módulos SSL e rewrite do Apache
RUN a2enmod ssl rewrite

# Instala a extensão cURL do PHP
RUN docker-php-ext-install curl

# Gerar certificado SSL autoassinado
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=BR/ST=SaoPaulo/L=SaoPaulo/O=Bot/OU=TI/CN=meudominio.com"

# Configuração do Apache com SSL
RUN cat <<'EOF' > /etc/apache2/sites-available/000-default-ssl.conf
<VirtualHost *:443>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/apache-selfsigned.crt
    SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key
</VirtualHost>
EOF

RUN a2ensite 000-default-ssl.conf

# Copia os arquivos da aplicação
COPY . /var/www/html/

# Expõe porta HTTPS
EXPOSE 443

# Comando para iniciar Apache
CMD ["apache2-foreground"]