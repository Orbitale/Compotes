#!/bin/sh
set -e

uid=$(stat -c %u /srv)
gid=$(stat -c %g /srv)

if [ "${uid}" -eq 0 ] && [ "${gid}" -eq 0 ]; then
    if [ $# -eq 0 ]; then
        php-fpm
    else
        exec "$@"
        exit
    fi
fi

sed -i "s/user = www-data/user = ${RUN_USER}/g" /etc/php/7.4/fpm/php-fpm.conf
sed -i "s/group = www-data/group = ${RUN_USER}/g" /etc/php/7.4/fpm/php-fpm.conf
sed -i -r "s/${RUN_USER}:x:\d+:\d+:/${RUN_USER}:x:$uid:$gid:/g" /etc/passwd
sed -i -r "s/${RUN_USER}:x:\d+:/${RUN_USER}:x:$gid:/g" /etc/group
chown -R "${RUN_USER}" /home

if [ $# -eq 0 ]; then
    php-fpm
else
    exec gosu "${RUN_USER}" "$@"
fi
