#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"
mkdir -p database/backups

arquivo="database/backups/wl-$(date +%Y%m%d-%H%M%S).sql"
docker compose up -d servidor --wait --wait-timeout 300 >/dev/null
docker compose exec -T servidor sh -c \
    'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction --quick --default-character-set=utf8mb4 "$MARIADB_DATABASE"' \
    > "$arquivo"

echo "Backup criado: $arquivo"
