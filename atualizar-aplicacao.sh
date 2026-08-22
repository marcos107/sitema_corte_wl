#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"
./atualizar-banco.sh
docker compose build site
docker compose up -d site --wait --wait-timeout 300
echo "Aplicacao reconstruida e atualizada."
