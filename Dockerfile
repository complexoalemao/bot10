# =========================
# Dockerfile para bot PHP
# =========================

# 1️⃣ Usa a imagem oficial do PHP 8.2 CLI
FROM php:8.2-cli

# 2️⃣ Define a pasta de trabalho dentro do container
WORKDIR /app

# 3️⃣ Copia todos os arquivos do projeto para o container
COPY . .

# 4️⃣ Instala extensões PHP necessárias (exemplo comum: curl, mbstring, etc.)
#    Remova ou adicione extensões conforme seu bot precisar
RUN docker-php-ext-install pcntl mbstring curl

# 5️⃣ Define a variável de ambiente para garantir que o PHP CLI não pause em caso de erro
ENV PHP_CLI_DISABLE_XDEBUG=1

# 6️⃣ Comando padrão para rodar o bot
#    Se seu bot for CLI, ele vai rodar bot.php continuamente
CMD ["php", "bot.php"]