#!/bin/bash

php console --no-interaction doctrine:schema:drop --force
php console --no-interaction doctrine:query:sql "truncate migration_versions;"
php console --no-interaction doctrine:migrations:migrate
php console --no-interaction operations:import
