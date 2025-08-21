#!/bin/bash

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "Aguardando o MySQL iniciar..."
until nc -z -v -w30 $DB_HOST 3306
do
  echo "Ainda sem conexão com o MySQL ($DB_HOST:3306), tentando novamente..."
  sleep 5
done

echo "MySQL disponível — executando comandos do Laravel..."

if [ ! -f ".jwt_initialized" ]; then
  echo "Configurando JWT..."
  
  php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force
  php artisan jwt:secret --force
  
  touch .jwt_initialized
  echo "JWT configurado com sucesso"
fi

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

exec php-fpm