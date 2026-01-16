# Usando PHP 8.2 CLI
FROM php:8.2-cli

# Define a pasta de trabalho
WORKDIR /app

# Copia todos os arquivos do projeto
COPY . .

# Comando padrão para rodar o bot
CMD ["php", "bot.php"]