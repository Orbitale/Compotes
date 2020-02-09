#!/bin/bash

for i in {1..${MAX_DB_HEALTH_ATTEMPTS}}
do
    if docker-compose exec database mysql -uroot -proot -e "SELECT 1;" >/dev/null 2>&1
    then
        printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Ok!"
        exit 0
    elif [[ $$i == ${MAX_DB_HEALTH_ATTEMPTS} ]]; then
        printf ""$(SCRIPT_ERROR_PATTERN) "ERR" "Cannot connect to mysql..."
        exit 1
    fi
    echo -e ".\c"
    sleep 1
done
