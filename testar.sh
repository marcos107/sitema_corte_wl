#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

compose=(docker compose)
if [[ "${1:-}" == "--dev" ]]; then
    compose+=(-f compose.yaml -f compose.dev.yaml)
fi

echo "Aguardando aplicacao e banco ficarem saudaveis..."
"${compose[@]}" up -d --wait --wait-timeout 300 >/dev/null

echo "1/4 - Estado dos containers"
"${compose[@]}" ps

echo "2/4 - Teste HTTP"
site_port="$("${compose[@]}" port site 80 | awk -F: 'NR == 1 { print $NF }')"
curl --fail --silent --show-error --location "http://127.0.0.1:${site_port}/" >/dev/null
echo "Site respondeu corretamente."

echo "3/4 - Teste MariaDB"
"${compose[@]}" exec -T servidor sh -c \
    'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE" -e "SELECT COUNT(*) AS total_usuarios FROM usuarios;"'

echo "4/4 - Teste de armazenamento"
"${compose[@]}" exec -T site sh -c \
    'set -eu
     arquivo=/srv/wl/temp/wl-storage-teste.txt
     printf "STORAGE WL OK\n" > "$arquivo"
     grep -q "STORAGE WL OK" "$arquivo"
     rm -f "$arquivo"
     echo "Leitura e escrita no armazenamento funcionaram."'

echo
echo "Todos os testes basicos passaram."
