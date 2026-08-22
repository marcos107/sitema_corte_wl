#!/bin/sh
set -eu

mkdir -p \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    /var/www/html/writable/session \
    /var/www/html/writable/uploads \
    /srv/wl/temp \
    /srv/wl/lixo

chown -R www-data:www-data /var/www/html/writable
chmod -R u+rwX,g+rwX /var/www/html/writable
chmod -R a+rwX /srv/wl

exec "$@"
