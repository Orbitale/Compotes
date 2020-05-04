#!/bin/bash

_TITLE="\033[32m[%s]\033[0m %s\n"
_ERROR="\033[31m[%s]\033[0m %s\n"

DIR=$(dirname "$0")

cd "${DIR}"/..

printf "${_TITLE}" "Deploy" "Install JS dependencies"
npm install
printf "${_TITLE}" "Deploy" "Build assets with Webpack Encore"
npm run build

printf "${_TITLE}" "Deploy" "Install PHP dependencies"
composer install --no-interaction --no-progress --classmap-authoritative --no-dev --no-scripts

printf "${_TITLE}" "Deploy" "Clear cache"
php bin/console --no-interaction cache:clear --no-warmup

printf "${_TITLE}" "Deploy" "Restart PHP FPM"
sudo /etc/init.d/php7.4-fpm restart || printf "${_ERROR}" "Deploy warning" "php-fpm cannot be restarted. You can ignore this message if you do not restart php-fpm at all, or if you do it in another way, like on PaaS."

printf "${_TITLE}" "Deploy" "Warm the cache"
php bin/console --no-interaction cache:warmup

printf "${_TITLE}" "Deploy" "Install bundle assets"
php bin/console --no-interaction assets:install --symlink --relative

printf "${_TITLE}" "Deploy" "Execute database migrations"
php bin/console --no-interaction doctrine:migrations:migrate

printf "${_TITLE}" "Compotes" "Synchronize all existing operations based on tag rules"
php bin/console --no-interaction operations:update-tags
