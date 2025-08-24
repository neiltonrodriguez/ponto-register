#!/bin/bash

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

DB_HOST=${DB_HOST:-mysql}
echo "Aguardando o MySQL iniciar..."
until nc -z -v -w30 $DB_HOST 3306
do
  echo "Ainda sem conexão com o MySQL ($DB_HOST:3306), tentando novamente..."
  sleep 5
done

echo "MySQL disponível — executando comandos do Laravel..."

echo "Ajustando permissões do Laravel..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

if ! grep -q "APP_KEY=" /var/www/.env || [ -z "$APP_KEY" ]; then
  php artisan key:generate
fi

php artisan migrate --force
php artisan db:seed --force

exec php-fpm