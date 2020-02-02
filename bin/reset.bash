#!/bin/bash

DIR=$(dirname "$0")

php "${DIR}"/console --no-interaction doctrine:schema:drop --force
php "${DIR}"/console --no-interaction doctrine:query:sql "truncate migration_versions;"
php "${DIR}"/console --no-interaction doctrine:migrations:migrate
php "${DIR}"/console --no-interaction doctrine:fixtures:load --append
php "${DIR}"/console --no-interaction operations:import
php "${DIR}"/console --no-interaction operations:update-tags
