#!/bin/bash

DIR=$(dirname "$0")

cd "${DIR}"/..

composer install --no-interaction --no-progress --classmap-authoritative --no-dev --no-scripts
php bin/console --no-interaction cache:clear --no-warmup
sudo service php7.4-fpm restart
php bin/console --no-interaction cache:warmup
php bin/console --no-interaction assets:install --symlink --relative
php bin/console --no-interaction doctrine:migrations:migrate
php bin/console --no-interaction operations:update-tags

npm install
npm run build
