#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-php-fpm}
env=${APP_ENV:-production}

# Adicione este comando para mudar para o usuário correto antes de executar chmod
# su -s /bin/bash -c "chmod -R 777 storage/* bootstrap/cache/*" www-data

cd /var/www/html

# Garantir que as permissões estejam corretas
#su -s /bin/bash -c "chmod -R 777 storage/* bootstrap/cache/*" www-data

# chown -R $(id -u):$(id -g) storage bootstrap/cache
#chmod -R 777 storage bootstrap/cache
#chmod -R 777 storage/* bootstrap/cache/*

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Running the queue..."
php artisan queue:work --verbose --tries=3 --timeout=90

php -i | grep PDO

# Loop para executar as tarefas agendadas a cada minuto
while true; do
    php artisan schedule:run --verbose --no-interaction &
    sleep 60
done
