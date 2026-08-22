#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker nao encontrado. Instale Docker Engine e o plugin Compose."
    exit 1
fi

storage_path="$(sed -n 's/^WL_STORAGE_HOST_PATH=//p' .env 2>/dev/null | tail -n 1)"
storage_path="${storage_path:-./nas/data}"
mkdir -p "$storage_path/temp" "$storage_path/lixo"
chmod -R a+rwX "$storage_path"

compose=(docker compose)

"${compose[@]}" up --build -d --wait --wait-timeout 300
./atualizar-banco.sh

site_port="$("${compose[@]}" port site 80 | awk -F: 'NR == 1 { print $NF }')"
db_port="$("${compose[@]}" port servidor 3306 | awk -F: 'NR == 1 { print $NF }')"

echo
echo "Containers iniciados e saudaveis."
echo "Site: http://localhost:${site_port}/"
echo "Banco: localhost:${db_port}"
echo
echo "Acompanhe com: docker compose ps"
