# =========================
# Dockerfile para bot PHP
# =========================

# 1️⃣ Usa a imagem oficial do PHP 8.2 CLI
FROM php:8.2-cli

# 2️⃣ Define a pasta de trabalho dentro do container
WORKDIR /app

# 3️⃣ Copia todos os arquivos do projeto para o container
COPY . .

# 4️⃣ Instala dependências do sistema necessárias para extensões PHP
# libonig-dev -> necessário para mbstring
# libcurl4-openssl-dev -> necessário para curl
# pkg-config -> necessário para detectar oniguruma
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-install pcntl mbstring curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 5️⃣ Define a variável de ambiente para evitar lentidão se Xdebug estiver ativo
ENV PHP_CLI_DISABLE_XDEBUG=1

# 6️⃣ Comando padrão para rodar o bot
CMD ["php", "bot.php"]