#!/bin/bash

for i in {1..25}
do
    if docker-compose exec -T database mysql -uroot -proot -e "SELECT 1;" >/dev/null 2>&1
    then
        printf "\n\033[32m[%s]\033[0m %s\n" "DB" "Ok!"
        exit 0
    elif [[ $i == 25 ]]; then
        printf "\n\033[31m[%s]\033[0m %s\n" "ERR" "Cannot connect to database..."
        exit 1
    fi
    echo -e ".\c"
    sleep 0.5
done
