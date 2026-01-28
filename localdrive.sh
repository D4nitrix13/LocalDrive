#!/usr/bin/env bash
# ============================================================
#  LocalDrive - Helper script
#  Autor: Daniel Benjamin Perez Morales
# ============================================================

set -euo pipefail

PROJECT_NAME="localdrive"
PROFILE="dev"
COMPOSE_FILE="docker-compose.yaml"

# Directorio del proyecto (donde está este script)
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Comando base de docker compose (modo desarrollo)
COMPOSE="docker compose \
  --profile ${PROFILE} \
  --file ${PROJECT_DIR}/${COMPOSE_FILE} \
  --project-name ${PROJECT_NAME} \
  --project-directory ${PROJECT_DIR}"

# Configuración de PostgreSQL / secretos
SECRETS_DIR="${PROJECT_DIR}/secrets"
POSTGRES_PASSWORD_FILE="${SECRETS_DIR}/POSTGRES_PASSWORD.txt"
DB_CONTAINER_NAME="container-db"
DB_USER="postgres"
DB_HOST="localhost"
DB_PORT="5432"
DB_SETUP_SQL="/App/sql/setup.sql"

# -------------------------------
# Helpers de salida y uso
# -------------------------------

usage() {
  cat <<EOF
Uso: $(basename "$0") <comando>

Comandos disponibles:
  init      Crea el secreto si no existe, levanta los contenedores e inicializa la base de datos
  up        Levanta la aplicación (build + up en segundo plano)
  stop      Detiene los contenedores sin borrarlos
  start     Inicia de nuevo contenedores detenidos
  down      Apaga y elimina contenedores, redes, volúmenes e imágenes locales
  db        Ejecuta el script SQL de inicialización de la base de datos
  status    Muestra el estado de los contenedores del proyecto

Ejemplos:
  $(basename "$0") init
  $(basename "$0") up
  $(basename "$0") stop
  $(basename "$0") down
  $(basename "$0") status
EOF
}

info()  { printf '\033[1;34m[INFO]\033[0m %s\n' "$*" ; }
warn()  { printf '\033[1;33m[WARN]\033[0m %s\n' "$*" ; }
error() { printf '\033[1;31m[ERROR]\033[0m %s\n' "$*" >&2 ; }

# -------------------------------
# Gestión de secretos
# -------------------------------

ensure_password_file() {
  mkdir -p "${SECRETS_DIR}"

  if [[ ! -f "${POSTGRES_PASSWORD_FILE}" ]]; then
    info "No se encontró ${POSTGRES_PASSWORD_FILE}. Creando archivo de contraseña..."

    # Pedir contraseña de forma oculta
    read -s -p "Ingrese la contraseña para PostgreSQL (POSTGRES_PASSWORD): " password
    echo
    if [[ -z "${password}" ]]; then
      error "La contraseña no puede estar vacía."
      exit 1
    fi

    echo "${password}" > "${POSTGRES_PASSWORD_FILE}"
    chmod 600 "${POSTGRES_PASSWORD_FILE}"
    info "Contraseña guardada en ${POSTGRES_PASSWORD_FILE}"
  else
    info "Usando contraseña existente en ${POSTGRES_PASSWORD_FILE}"
  fi
}

get_postgres_password() {
  if [[ ! -f "${POSTGRES_PASSWORD_FILE}" ]]; then
    error "El archivo ${POSTGRES_PASSWORD_FILE} no existe. Ejecuta '$(basename "$0") init' o crea el secreto primero."
    exit 1
  fi
  cat "${POSTGRES_PASSWORD_FILE}"
}

# -------------------------------
# Comandos de Docker Compose
# -------------------------------

cmd_up() {
  info "Levantando servicios de Docker (perfil: ${PROFILE})..."
  ${COMPOSE} up \
    --build \
    --timestamps \
    --remove-orphans \
    --pull always \
    --timeout 10 \
    --detach
  info "Servicios levantados."
}

cmd_stop() {
  info "Deteniendo contenedores..."
  ${COMPOSE} stop --timeout 10
  info "Contenedores detenidos."
}

cmd_start() {
  info "Iniciando contenedores nuevamente..."
  ${COMPOSE} start
  info "Contenedores iniciados."
}

cmd_down() {
  info "Eliminando servicios, volúmenes e imágenes locales..."
  docker compose \
    --project-name "${PROJECT_NAME}" \
    --project-directory "${PROJECT_DIR}" \
    down \
    --remove-orphans \
    --timeout 10 \
    --volumes \
    --rmi local
  info "Proyecto limpiado por completo."
}

cmd_status() {
  info "Estado de los contenedores del proyecto ${PROJECT_NAME}:"
  ${COMPOSE} ps
}

# -------------------------------
# Inicialización de base de datos
# -------------------------------

cmd_db_setup() {
  info "Inicializando base de datos usando ${DB_SETUP_SQL}..."

  local pg_password
  pg_password="$(get_postgres_password)"

  docker container exec -itu0 \
    -e PGPASSWORD="${pg_password}" \
    "${DB_CONTAINER_NAME}" \
    psql \
      -h "${DB_HOST}" \
      -U "${DB_USER}" \
      -p "${DB_PORT}" \
      -f "${DB_SETUP_SQL}"

  info "Base de datos inicializada correctamente."
}

# -------------------------------
# Comando compuesto: init
# -------------------------------

cmd_init() {
  info "==> Paso 1: asegurar secreto de PostgreSQL"
  ensure_password_file

  info "==> Paso 2: levantar servicios"
  cmd_up

  info "==> Paso 3: inicializar base de datos"
  cmd_db_setup

  info "Init completado. La aplicación LocalDrive debería estar lista para usarse."
}

# -------------------------------
# Entry point
# -------------------------------

main() {
  local cmd="${1-}"

  if [[ -z "${cmd}" ]]; then
    usage
    exit 1
  fi

  case "${cmd}" in
    init)   cmd_init ;;
    up)     ensure_password_file; cmd_up ;;
    stop)   cmd_stop ;;
    start)  cmd_start ;;
    down)   cmd_down ;;
    db)     cmd_db_setup ;;
    status) cmd_status ;;
    *)
      error "Comando desconocido: ${cmd}"
      usage
      exit 1
      ;;
  esac
}

main "$@"
