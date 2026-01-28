#!/usr/bin/env bash
# ============================================================
#  LocalDrive - Helper script
# Autor: Daniel Benjamin Perez Morales
# GitHub: https://github.com/D4nitrix13
# GitLab: https://gitlab.com/D4nitrix13
# Correo electrónico: danielperezdev@proton.me
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
DB_NAME="local_drive"
DB_SETUP_SQL="/App/sql/setup.sql"

# Otros servicios
REDIS_CONTAINER_NAME="container-redis"
MONGO_CONTAINER_NAME="container-mongo"

# -------------------------------
# Helpers de salida y uso
# -------------------------------

usage() {
  cat <<EOF
Uso: $(basename "$0") <comando>

Comandos disponibles:
  up        Crea el secreto si no existe, levanta contenedores, espera servicios e inicializa la base de datos
  stop      Detiene los contenedores sin borrarlos
  start     Inicia de nuevo contenedores detenidos
  down      Apaga y elimina contenedores, redes, volúmenes e imágenes locales
  db        Ejecuta el script SQL de inicialización de la base de datos
  status    Muestra el estado de los contenedores del proyecto

Ejemplos:
  $(basename "$0") up
  $(basename "$0") stop
  $(basename "$0") down
  $(basename "$0") status
EOF
}

info()  { printf '\033[1;34m[INFO]\033[0m %s\n' "$*"; }
warn()  { printf '\033[1;33m[WARN]\033[0m %s\n' "$*"; }
error() { printf '\033[1;31m[ERROR]\033[0m %s\n' "$*" >&2; }

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
    error "El archivo ${POSTGRES_PASSWORD_FILE} no existe. Ejecuta '$(basename "$0") up' primero o crea el secreto manualmente."
    exit 1
  fi
  cat "${POSTGRES_PASSWORD_FILE}"
}

# -------------------------------
# Waiters para servicios
# -------------------------------

wait_for_postgres() {
  local retries=30
  local delay=2

  info "Esperando a que PostgreSQL acepte conexiones..."

  for ((i=1; i<=retries; i++)); do
    if docker exec "${DB_CONTAINER_NAME}" pg_isready -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USER}" >/dev/null 2>&1; then
      info "PostgreSQL está listo."
      return 0
    fi
    warn "PostgreSQL no está listo aún (intento ${i}/${retries}). Esperando ${delay}s..."
    sleep "${delay}"
  done

  error "PostgreSQL no respondió a tiempo."
  exit 1
}

wait_for_redis() {
  local retries=30
  local delay=2

  info "Esperando a que Redis acepte conexiones..."

  for ((i=1; i<=retries; i++)); do
    if docker exec "${REDIS_CONTAINER_NAME}" redis-cli PING >/dev/null 2>&1; then
      info "Redis está listo."
      return 0
    fi
    warn "Redis no está listo aún (intento ${i}/${retries}). Esperando ${delay}s..."
    sleep "${delay}"
  done

  error "Redis no respondió a tiempo."
  exit 1
}

wait_for_mongo() {
  local retries=30
  local delay=2

  info "Esperando a que MongoDB acepte conexiones..."

  for ((i=1; i<=retries; i++)); do
    if docker exec "${MONGO_CONTAINER_NAME}" mongosh --quiet --eval "db.adminCommand({ ping: 1 })" >/dev/null 2>&1; then
      info "MongoDB está listo."
      return 0
    fi
    warn "MongoDB no está listo aún (intento ${i}/${retries}). Esperando ${delay}s..."
    sleep "${delay}"
  done

  error "MongoDB no respondió a tiempo."
  exit 1
}

wait_for_services() {
  wait_for_postgres
  wait_for_redis
  wait_for_mongo
}

# -------------------------------
# Comandos de Docker Compose
# -------------------------------

cmd_up_only() {
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
  info "Inicializando base de datos usando ${DB_SETUP_SQL} (script maestro)..."

  local pg_password
  pg_password="$(get_postgres_password)"

  # Verificar que el archivo exista dentro del contenedor
  if ! docker exec "${DB_CONTAINER_NAME}" test -f "${DB_SETUP_SQL}"; then
    error "El archivo ${DB_SETUP_SQL} no existe dentro del contenedor ${DB_CONTAINER_NAME}."
    error "Revisa tu Dockerfile.database o la ruta de DB_SETUP_SQL."
    exit 1
  fi

  # setup.sql se encarga de:
  #   - DROP DATABASE local_drive;
  #   - CREATE DATABASE local_drive;
  #   - \c local_drive;
  #   - crear tablas, índices, funciones, procedimientos, triggers, vistas, etc.
  # Por eso nos conectamos a la BD 'postgres' aquí.
  docker container exec -itu0 \
    -e PGPASSWORD="${pg_password}" \
    "${DB_CONTAINER_NAME}" \
    psql \
      -h "${DB_HOST}" \
      -U "${DB_USER}" \
      -p "${DB_PORT}" \
      -d postgres \
      -v ON_ERROR_STOP=1 \
      -f "${DB_SETUP_SQL}"

  info "Base de datos ${DB_NAME} inicializada correctamente mediante ${DB_SETUP_SQL}."
}

# -------------------------------
# up "completo": secreto + up + wait + DB
# -------------------------------

cmd_up_full() {
  info "==> Paso 1: asegurar secreto de PostgreSQL"
  ensure_password_file

  info "==> Paso 2: levantar servicios"
  cmd_up_only

  info "==> Paso 3: esperar a que Postgres, Redis y Mongo estén listos"
  wait_for_services

  info "==> Paso 4: inicializar base de datos"
  cmd_db_setup

  info "up completado. La aplicación LocalDrive debería estar lista para usarse."
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
    up)     cmd_up_full ;;
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
