#!/usr/bin/env bash
set -euo pipefail

if [[ "${EUID}" -ne 0 ]]; then
    echo "Execute com sudo: sudo ./instalar-linux.sh --storage /caminho/do-outro-hd"
    exit 1
fi

project_dir="$(cd "$(dirname "$0")" && pwd -P)"
storage_path="${project_dir}/arquivos"
server_url=""
site_branch="linux-site-2026-08-22"
repo_url="https://github.com/marcos107/sitema_corte_wl.git"
owner="${SUDO_USER:-root}"

usage() {
    echo "Uso: sudo ./instalar-linux.sh [--storage /caminho] [--url http://ip:8081/]"
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --storage)
            storage_path="${2:-}"
            shift 2
            ;;
        --url)
            server_url="${2:-}"
            shift 2
            ;;
        --help|-h)
            usage
            exit 0
            ;;
        *)
            usage
            exit 1
            ;;
    esac
done

if [[ -z "$storage_path" ]]; then
    echo "Informe um caminho valido para --storage."
    exit 1
fi

if ! command -v git >/dev/null 2>&1; then
    apt-get update
    apt-get install -y git
fi

if ! command -v docker >/dev/null 2>&1; then
    apt-get update
    apt-get install -y docker.io
fi

if ! docker compose version >/dev/null 2>&1; then
    apt-get update
    apt-get install -y docker-compose-v2 || apt-get install -y docker-compose-plugin
fi

if [[ ! -d "${project_dir}/site/.git" ]]; then
    if [[ -e "${project_dir}/site" ]]; then
        echo "A pasta site existe, mas nao e um clone Git. Corrija-a antes de continuar."
        exit 1
    fi

    git clone --branch "$site_branch" --single-branch "$repo_url" "${project_dir}/site"
fi

if [[ -z "$server_url" ]]; then
    server_ip="$(hostname -I | awk '{print $1}')"
    if [[ -z "$server_ip" ]]; then
        echo "Nao foi possivel identificar o IP. Execute novamente com --url."
        exit 1
    fi
    server_url="http://${server_ip}:8081/"
fi

if [[ ! -f "${project_dir}/.env" ]]; then
    cp "${project_dir}/.env.example" "${project_dir}/.env"
fi

read_env() {
    sed -n "s/^$1=//p" "${project_dir}/.env" | tail -n 1
}

set_env() {
    local key="$1"
    local value="$2"

    if grep -q "^${key}=" "${project_dir}/.env"; then
        sed -i "s|^${key}=.*|${key}=${value}|" "${project_dir}/.env"
    else
        printf '%s=%s\n' "$key" "$value" >> "${project_dir}/.env"
    fi
}

new_password() {
    od -An -N18 -tx1 /dev/urandom | tr -d ' \n'
}

db_password="$(read_env WL_DB_PASSWORD)"
root_password="$(read_env WL_DB_ROOT_PASSWORD)"

if [[ -z "$db_password" || "$db_password" == "troque_esta_senha" ]]; then
    db_password="$(new_password)"
fi

if [[ -z "$root_password" || "$root_password" == "troque_esta_senha_root" ]]; then
    root_password="$(new_password)"
fi

set_env WL_BASE_URL "$server_url"
set_env WL_STORAGE_HOST_PATH "$storage_path"
set_env WL_DB_PASSWORD "$db_password"
set_env WL_DB_ROOT_PASSWORD "$root_password"

mkdir -p "$storage_path"
chown -R "${owner}:${owner}" "$project_dir" "$storage_path"
chmod +x "${project_dir}"/*.sh

cd "$project_dir"
./iniciar.sh
./testar.sh

echo
echo "Instalacao concluida."
echo "Site: ${server_url}"
echo "Arquivos: ${storage_path}"
echo "Login inicial: corte / ian123"
