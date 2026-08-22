#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"
docker compose up -d servidor --wait --wait-timeout 300 >/dev/null

db_query() {
    docker compose exec -T servidor sh -c \
        'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE" -Nse "$1"' \
        sh "$1"
}

db_query 'CREATE TABLE IF NOT EXISTS wl_schema_migrations (migration VARCHAR(255) NOT NULL PRIMARY KEY, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'

shopt -s nullglob
migrations=(database/migrations/*.sql)
pendentes=()

for migration in "${migrations[@]}"; do
    nome="$(basename "$migration")"
    if [[ ! "$nome" =~ ^[A-Za-z0-9._-]+$ ]]; then
        echo "Nome de migracao invalido: $nome"
        exit 1
    fi

    aplicada="$(db_query "SELECT COUNT(*) FROM wl_schema_migrations WHERE migration = '$nome'")"
    if [[ "$aplicada" == "0" ]]; then
        pendentes+=("$migration")
    fi
done

if [[ ${#pendentes[@]} -eq 0 ]]; then
    echo "Banco atualizado. Nenhuma migracao pendente."
    exit 0
fi

./backup-banco.sh

for migration in "${pendentes[@]}"; do
    nome="$(basename "$migration")"
    echo "Aplicando: $nome"
    docker compose exec -T servidor sh -c \
        'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' \
        < "$migration"
    db_query "INSERT INTO wl_schema_migrations (migration) VALUES ('$nome')"
done

echo "Banco atualizado com sucesso."
