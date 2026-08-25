#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

if [[ ! -d .git ]]; then
    echo "Execute o script na pasta principal da instalacao WL."
    exit 1
fi

if [[ ! -d site/.git ]]; then
    echo "O clone do site nao foi encontrado em ./site."
    exit 1
fi

echo "Atualizando infraestrutura..."
git pull --ff-only

echo "Atualizando site..."
git -C site pull --ff-only

echo "Reconstruindo o container do site..."
./atualizar-aplicacao.sh

echo "Atualizacao concluida. Banco e arquivos foram preservados."
