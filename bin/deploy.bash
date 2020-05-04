#!/bin/bash

DIR=$(dirname "$0")

cd "${DIR}"/..

# Install JS dependencies and build assets with Webpack Encore
npm install
npm run build

# Install PHP dependencies and deploy the Symfony app
composer install --no-interaction --no-progress --classmap-authoritative --no-dev --no-scripts
php bin/console --no-interaction cache:clear --no-warmup
sudo /etc/init.d/php7.4-fpm restart
php bin/console --no-interaction cache:warmup
php bin/console --no-interaction assets:install --symlink --relative
php bin/console --no-interaction doctrine:migrations:migrate

# Compotes-specific
php bin/console --no-interaction operations:update-tags
